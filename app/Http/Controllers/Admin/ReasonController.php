<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reason\CreateReasonRequest;
use App\Models\Reason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;
class ReasonController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_reasons|add_reasons', ['only' => ['index','store']]);
        $this->middleware('permission:add_reasons', ['only' => ['create','store']]);
        $this->middleware('permission:edit_reasons', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_reasons', ['only' => ['destroy']]);
    }

    public function index()
    {
        $reasons = Reason::orderBy('id', 'DESC')->get();
        return view('admin.reasons.index', compact('reasons'));
    }

    public function datatable(Request $request) 
    {
        $items = Reason::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.reasons.create');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            Reason::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch(\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $reason = Reason::findOrFail($id);
        return view('admin.reasons.create', compact('reason'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
           
            $reason = Reason::findOrFail($id);
            $reason->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch(\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Reason::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
