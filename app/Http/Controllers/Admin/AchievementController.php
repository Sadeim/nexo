<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Achievement\CreateAchievementRequest;
use App\Models\Achievement;
use App\Models\Section;
use App\Traits\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    use SaveImageTrait;

    // public function __construct()
    // {
    //     $this->middleware('permission:view_achievements|add_achievements', ['only' => ['index','store']]);
    //     $this->middleware('permission:add_achievements', ['only' => ['create','store']]);
    //     $this->middleware('permission:edit_achievements', ['only' => ['edit','update']]);
    //     $this->middleware('permission:delete_achievements', ['only' => ['destroy']]);
    // }
    
    public function index()
    {
        $data['section'] = Section::where('key', 'achievements_section')->first();
        return view('admin.achievements.index', $data);
    }
    
    public function datatable(Request $request) 
    {
        $items = Achievement::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.achievements.create');
    }
    
    public function store(CreateAchievementRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['description', 'title']);
            $data['year'] =  0;
            Achievement::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    
    public function edit($id)
    {
        $achievement = Achievement::findOrFail($id);
        return view('admin.achievements.create', compact('achievement'));
    }
    
    public function update(CreateAchievementRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['description', 'title', 'year']);

            $achievement = Achievement::findOrFail($id);
            $achievement->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    
    public function destroy($id)
    {
        Achievement::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
