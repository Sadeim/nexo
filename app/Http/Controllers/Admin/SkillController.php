<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Skill\CreateSkillRequest;
use App\Models\Section;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_skills|add_skills', ['only' => ['index','store']]);
        $this->middleware('permission:add_skills', ['only' => ['create','store']]);
        $this->middleware('permission:edit_skills', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_skills', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $data['section'] = Section::where('key', 'skills_section')->first();
        return view('admin.skills.index', $data);
    }
    
    public function datatable(Request $request) 
    {
        $items = Skill::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.skills.create');
    }
    
    public function store(CreateSkillRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['percent','text']);
            Skill::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    
    public function edit($id)
    {
        $skill = Skill::findOrFail($id);
        return view('admin.skills.create', compact('skill'));
    }
    
    public function update(CreateSkillRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['percent','text']);
            $skill = Skill::findOrFail($id);
            $skill->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    
    public function destroy($id)
    {
        Skill::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
