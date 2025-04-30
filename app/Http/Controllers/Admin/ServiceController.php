<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\CreateServiceRequest;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_services|add_services', ['only' => ['index','store']]);
        $this->middleware('permission:add_services', ['only' => ['create','store']]);
        $this->middleware('permission:edit_services', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_services', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.services.index');
    }

    public function datatable(Request $request) 
    {
        $items = Service::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(CreateServiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only('name', 'description', 'is_featured', 'icon');
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'services');
            }
            Service::create($data);
            DB::commit();

            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        return view('admin.services.create', compact('service'));
    }

    public function update(CreateServiceRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only('name', 'description', 'is_featured', 'icon');
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'services');
            }
            $service = Service::findOrFail($id);
            $service->update($data);
            DB::commit();

            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Service::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    public function activate($id)
    {
        $item = Service::findOrFail($id);
        $item->status = 1 - $item->status;
        $item->save();
        return $this->response_api(200,  __('admin.form.status_changed_successfully'), '');
    }
}
