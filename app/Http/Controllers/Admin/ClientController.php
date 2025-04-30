<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class ClientController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_clients|add_clients', ['only' => ['index','store']]);
        $this->middleware('permission:add_clients', ['only' => ['create','store']]);
        $this->middleware('permission:edit_clients', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_clients', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.clients.index');
    }

    public function datatable(Request $request) 
    {
        $items = Client::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(CreateClientRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->only('name', 'link');

            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadImage($request->logo, 'clients');
            }

            Client::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('admin.clients.create', compact('client'));
    }

    public function update(CreateClientRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $client = Client::findOrFail($id);
            $data = $request->only('name', 'link');

            if ($request->hasFile('logo')) {
                $data['logo'] = $this->uploadImage($request->logo, 'clients');
            }

            $client->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Client::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
