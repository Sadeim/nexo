<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\Employee;
use App\Models\Service;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Employees (barbers) shown in the Flutter POS "Select employee" panel.
 * Not linked to any auth guard — an Employee is just a label the cashier
 * assigns a sale to.
 */
class EmployeeController extends Controller
{
    use SaveImageTrait;

    public function index()
    {
        return view('admin.employees.index');
    }

    public function datatable(Request $request)
    {
        $items = Employee::query()->orderBy('sort_order')->orderBy('name')->search($request);
        return $this->filterDataTable($items, $request, null, EmployeeResource::class);
    }

    public function create()
    {
        return view('admin.employees.create', [
            'services' => $this->specialtyOptions(),
        ]);
    }

    /** Marketing services offered as specialties on the public profile. */
    private function specialtyOptions()
    {
        return Service::orderBy('name')->get(['id', 'name', 'price']);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        try {
            DB::beginTransaction();
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadImage($request->file('avatar'), 'employees');
            }
            if ($request->hasFile('photo')) {
                $data['photo_path'] = $this->uploadWebpImage($request->file('photo'), 'barbers');
            }
            Employee::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        return view('admin.employees.create', [
            'employee' => $employee,
            'services' => $this->specialtyOptions(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $data = $this->validateData($request, $id);

        try {
            DB::beginTransaction();
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadImage($request->file('avatar'), 'employees');
            }
            if ($request->hasFile('photo')) {
                $data['photo_path'] = $this->uploadWebpImage($request->file('photo'), 'barbers');
            } elseif ($request->boolean('remove_photo')) {
                $data['photo_path'] = null;
            }
            $employee->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function activate($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->is_active = !$employee->is_active;
        $employee->save();
        return $this->response_api(200, __('admin.form.status_changed_successfully'), '');
    }

    public function destroy($id)
    {
        Employee::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    /**
     * Give a barber a temporary portal password.
     *
     * The owner types it and passes it on however they like. It is a one-time
     * credential by construction: setPortalPassword() always sets
     * must_change_password, so the barber is held on the change-password
     * screen until the password is theirs alone.
     *
     * Deliberately not part of the employee form. A password is not a profile
     * field, and saving the form should never silently reset one.
     */
    public function setPassword(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:72'],
        ], [
            'password.min' => 'The temporary password must be at least 8 characters.',
        ]);

        if (! $employee->phone) {
            return $this->response_api(422, 'Give this barber a phone number first — it is how they sign in.', '');
        }

        $employee->setPortalPassword($data['password']);

        return $this->response_api(200, 'Portal password set. They will be asked to change it when they sign in.', '');
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        // Normalise before validating, so the unique rule compares the value
        // that will actually be stored rather than the punctuation typed.
        //
        // Only when the field was actually submitted. merge() always sets the
        // key, so doing this unconditionally would put phone => null into every
        // request that does not carry one — and validate() would then hand it
        // to update() and wipe a barber's number on an unrelated save.
        if ($request->has('phone')) {
            $request->merge([
                'phone' => Employee::normalizePhone($request->input('phone')),
            ]);
        }

        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120', Rule::unique('employees', 'name')->ignore($id)],
            'phone'           => ['nullable', 'string', 'digits_between:7,15', Rule::unique('employees', 'phone')->ignore($id)],
            // What they see in the portal. A column with two values, so the
            // rule is the column: anything else must not reach an enum.
            'portal_role'     => ['nullable', Rule::in(array_keys(Employee::PORTAL_ROLES))],
            'sort_order'      => 'nullable|integer|min:0|max:9999',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'avatar'          => 'nullable|image|max:4096',
            'is_active'       => 'nullable|boolean',

            // Public profile
            'public_profile'  => 'nullable|boolean',
            'slug'            => ['nullable', 'string', 'max:160', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('employees', 'slug')->ignore($id)],
            'title'           => 'nullable|string|max:120',
            'bio'             => 'nullable|string|max:5000',
            'instagram'       => 'nullable|string|max:120',
            'specialties'     => 'nullable|array',
            'specialties.*'   => 'integer|exists:services,id',
            'photo'           => 'nullable|image|max:5120',
            'remove_photo'    => 'nullable|boolean',
        ], [
            'slug.regex'  => 'The URL must be lowercase letters, numbers and single hyphens.',
            'photo.max'   => 'The photo may not be larger than 5MB.',
            'slug.unique' => 'Another barber already uses that URL.',
            'phone.unique' => 'Another barber already uses that phone number.',
            'phone.digits_between' => 'Enter a phone number with 7 to 15 digits.',
            'portal_role.in' => 'Choose either Barber or Manager for the portal role.',
        ]);

        // A published barber with no slug has no reachable page, so one is
        // derived rather than left to fail silently at 404 time.
        $data['slug'] = $this->resolveSlug($data['slug'] ?? null, $data['name'], $id);

        // Stored as a bare handle so the view owns how it is displayed and
        // linked. Accepts whatever the owner pastes — "@omar", a profile URL,
        // or the handle itself.
        $data['instagram'] = $this->normalizeInstagram($data['instagram'] ?? null);

        // An empty multi-select sends nothing at all, which must clear the
        // list rather than leave the previous one in place.
        $data['specialties'] = array_values(array_unique(array_map(
            'intval',
            $request->input('specialties', [])
        )));

        /*
         * A null portal_role means "not submitted" — an empty select, or a
         * form that does not carry the field at all, since the web group
         * converts empty strings to null before validation. The column is not
         * nullable and defaults to 'barber', so the stored value is left
         * alone rather than overwritten with null. Same lesson as the phone
         * field above: an absent field must never be a destructive write.
         */
        if (array_key_exists('portal_role', $data) && $data['portal_role'] === null) {
            unset($data['portal_role']);
        }

        // Not columns.
        unset($data['photo'], $data['remove_photo']);

        return $data;
    }

    /**
     * The submitted slug, or one derived from the name, made unique.
     */
    private function resolveSlug(?string $submitted, string $name, ?int $ignoreId): string
    {
        $base = Str::slug($submitted ?: $name) ?: 'barber';
        $slug = $base;
        $n    = 1;

        while (
            Employee::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . (++$n);
        }

        return $slug;
    }

    /**
     * Reduce anything Instagram-shaped to the bare handle.
     */
    private function normalizeInstagram(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        // Pull the handle out of a pasted profile URL.
        if (str_contains($value, 'instagram.com')) {
            $path  = parse_url($value, PHP_URL_PATH) ?? '';
            $value = trim($path, '/');
            $value = explode('/', $value)[0] ?? '';
        }

        $value = ltrim($value, '@');
        $value = trim($value, '/');

        return $value === '' ? null : $value;
    }
}
