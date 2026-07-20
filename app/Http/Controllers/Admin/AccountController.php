<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAccountRequest;
use App\Http\Requests\Admin\UpdateAccountRequest;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * User (admins) management with roles (admin / cashier).
 *
 * Access: the whole admin route group already requires the `admin` role
 * (EnsureAdminRole), so cashiers can never reach these actions. The guards
 * below add defense-in-depth around the two dangerous operations:
 *   - an admin cannot delete/disable their own account, and
 *   - the last remaining active admin cannot be deleted/disabled (never lock
 *     the whole system out of the dashboard).
 */
class AccountController extends Controller
{
    /** Roles selectable when creating/editing a user. */
    private function selectableRoles()
    {
        return Role::where('guard_name', 'admin')
            ->whereIn('name', ['admin', 'cashier'])
            ->get();
    }

    /**
     * True if $admin is the last active account holding the `admin` role.
     */
    private function isLastActiveAdmin(Admin $admin): bool
    {
        if (!$admin->hasRole('admin')) {
            return false;
        }

        $otherActiveAdmins = Admin::role('admin', 'admin')
            ->where('status', 1)
            ->where('id', '!=', $admin->id)
            ->count();

        return $otherActiveAdmins === 0;
    }

    public function index()
    {
        return view('admin.accounts.index');
    }

    public function datatable(Request $request)
    {
        $items = Admin::query()->with('roles')->orderBy('id', 'DESC')->search($request);
        return $this->filterDataTable($items, $request, null, \App\Http\Resources\Admin\AccountResource::class);
    }

    public function create()
    {
        return view('admin.accounts.create', ['roles' => $this->selectableRoles()]);
    }

    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            $admin = Admin::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'status'   => 1,
            ]);
            $admin->syncRoles([$data['role']]);
            DB::commit();

            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.accounts.create', [
            'admin'     => $admin,
            'roles'     => $this->selectableRoles(),
            'adminRole' => $admin->roles->first(),
        ]);
    }

    public function update(UpdateAccountRequest $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $data = $request->validated();

        // Guard: prevent an admin from demoting the last active admin (itself
        // or another) out of the admin role, which would lock the dashboard.
        if ($data['role'] !== 'admin' && $this->isLastActiveAdmin($admin)) {
            return $this->response_api(403, 'You cannot change the role of the last active admin.');
        }

        try {
            DB::beginTransaction();
            $update = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];
            if (!empty($data['password'])) {
                $update['password'] = Hash::make($data['password']);
            }
            $admin->update($update);
            $admin->syncRoles([$data['role']]);
            DB::commit();

            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Toggle active status (used for "disable" instead of hard delete).
     */
    public function activate($id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === Auth::guard('admin')->id()) {
            return $this->response_api(403, 'You cannot disable your own account.');
        }

        // Only block when we are about to DEACTIVATE the last active admin.
        if ($admin->status == 1 && $this->isLastActiveAdmin($admin)) {
            return $this->response_api(403, 'You cannot disable the last active admin.');
        }

        $admin->status = 1 - $admin->status;
        $admin->save();

        return $this->response_api(200, __('admin.form.status_changed_successfully'), '');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === Auth::guard('admin')->id()) {
            return $this->response_api(403, 'You cannot delete your own account.');
        }

        if ($this->isLastActiveAdmin($admin)) {
            return $this->response_api(403, 'You cannot delete the last active admin.');
        }

        $admin->delete();
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
