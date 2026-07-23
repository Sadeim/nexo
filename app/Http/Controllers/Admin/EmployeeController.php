<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\Employee;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        try {
            DB::beginTransaction();
            if ($request->hasFile('avatar')) {
                $data['avatar'] = $this->uploadImage($request->file('avatar'), 'employees');
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
        return view('admin.employees.create', compact('employee'));
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

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:120', Rule::unique('employees', 'name')->ignore($id)],
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'avatar'     => 'nullable|image|max:4096',
            'is_active'  => 'nullable|boolean',
        ]);
    }
}
