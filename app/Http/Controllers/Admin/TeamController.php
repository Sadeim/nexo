<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Team\CreateTeamRequest;
use App\Models\Section;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;
use Exception;

class TeamController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_teams|add_teams', ['only' => ['index', 'store']]);
        $this->middleware('permission:add_teams', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit_teams', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete_teams', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['section'] = Section::where('key', 'teams_section')->first();
        return view('admin.teams.index', $data);
    }

    public function datatable(Request $request) 
    {
        $items = Team::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.teams.create');
    }

    public function store(CreateTeamRequest $request)
    {
        try {
            DB::beginTransaction();
            // استخدام الحقول المطلوبة
            $data = $request->only(['name', 'position']);
            // التعامل مع الصورة
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'teams');
            }
            // بالإضافة إلى social_links (يجب أن تكون بصيغة array؛ إذا تم إرسالها كنص، يمكنك تحويلها باستخدام json_decode)
            if ($request->has('social_links')) {
                $data['social_links'] = $request->social_links; // افترض أنها مصفوفة أو JSON صالح            
            }

            Team::create($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $team = Team::findOrFail($id);
        return view('admin.teams.create', compact('team'));
    }
    public function update(CreateTeamRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['name', 'position']);
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'teams');
            }

            if ($request->has('social_links')) {
                $data['social_links'] = $request->social_links;
            }
            $team = Team::findOrFail($id);
            $team->update($data);
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    public function destroy($id)
    {
        Team::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
    // دالة إضافية لتغيير الحالة إن لزم الأمر    
    public function activate($id)
    {
        $team = Team::findOrFail($id);
        $team->status = 1 - $team->status;
        $team->save();
        return $this->response_api(200, __('admin.form.status_changed_successfully'), '');
    }
}
