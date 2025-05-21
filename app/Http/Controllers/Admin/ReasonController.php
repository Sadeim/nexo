<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reason\CreateReasonRequest;
use App\Models\Reason;
use App\Models\Section;
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
        $section = Section::where('key', 'reasons_section')->first();
        return view('admin.reasons.index', compact('reasons', 'section'));
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

    public function store(CreateReasonRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->except(['icon']);
            if ($request->hasFile('icon')) {
                $data['icon'] = $this->uploadImage($request->file('icon'), 'reasons');
            }
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

    public function update(CreateReasonRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->except(['icon']);
            if ($request->hasFile('icon')) {
                $data['icon'] = $this->uploadImage($request->file('icon'), 'reasons');
            }
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
