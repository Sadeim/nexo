<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventRequest;
use App\Models\Event;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class EventController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_events|add_events', ['only' => ['index','store']]);
        $this->middleware('permission:add_events', ['only' => ['create','store']]);
        $this->middleware('permission:edit_events', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_events', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['section'] = Section::where('key', 'events_section')->first();
        return view('admin.events.index', $data);
    }

    public function datatable(Request $request) 
    {
        $items = Event::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(CreateEventRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'events');
            }

            Event::create($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('admin.events.create', compact('event'));
    }

    public function update(CreateEventRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $event = Event::findOrFail($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->image, 'events');
            }

            $event->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }

    public function destroy($id)
    {
        Event::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
