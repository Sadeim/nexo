<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FAQ\CreateFAQRequest;
use App\Models\Faq;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_faqs|add_faqs', ['only' => ['index','store']]);
        $this->middleware('permission:add_faqs', ['only' => ['create','store']]);
        $this->middleware('permission:edit_faqs', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_faqs', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['section'] = Section::where('key', 'faqs_section')->first();
        return view('admin.faqs.index', $data);
    }

    public function datatable(Request $request) 
    {
        $items = Faq::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(CreateFAQRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['question', 'answer', 'status']);
            Faq::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $faq = Faq::findOrFail($id);
        return view('admin.faqs.create', compact('faq'));
    }

    public function update(CreateFAQRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['question', 'answer', 'status']);
            $faq = Faq::findOrFail($id);
            $faq->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Faq::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
