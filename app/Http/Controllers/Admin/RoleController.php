<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_roles|add_roles', ['only' => ['index','store']]);
        $this->middleware('permission:add_roles', ['only' => ['create','store']]);
        $this->middleware('permission:edit_roles', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_roles', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('admin.roles.index');
    }

    public function datatable (Request $request) 
    {
        $items = Role::query()->where('guard_name', 'admin')->whereNotIn('id', [1,2,3])->search($request);
        return $this->filterDataTable($items, $request);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy('parent'); 
        return view('admin.roles.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);

        if($validator->fails()){
            return $this->response_api(400, $validator->errors()->first(),'');
        }

        try {
            DB::beginTransaction();
                $role = Role::create(['name' => $request->input('name'), 'guard_name' => 'admin']);
                $role->syncPermissions([$request->input('permissions')]);
            DB::commit();

            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    { 
        $role = Role::find($id);
        $permissions = Permission::where('guard_name', 'admin')->get()->groupBy('parent');
        $rolePermissions = DB::table('role_has_permissions')->where('role_id',$id)
                                ->pluck('permission_id','permission_id')->all();
        return view('admin.roles.create',compact('role','permissions','rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),  [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        if($validator->fails()){
            return $this->response_api(400, $validator->errors()->first(),'');
        }
        
        $role = Role::find($id);
        $role->name = $request->input('name');
        try {
            DB::beginTransaction();
                $role->save();
                $role->syncPermissions($request->input('permissions'));
            DB::commit();

            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Role::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
