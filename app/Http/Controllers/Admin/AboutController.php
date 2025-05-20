<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\About\CreateAboutRequest;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_abouts|add_abouts', ['only' => ['index','store']]);
        $this->middleware('permission:add_abouts', ['only' => ['create','store']]);
        $this->middleware('permission:edit_abouts', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_abouts', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.abouts.index');
    }

    public function datatable(Request $request)
    {
        $items = About::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        return view('admin.abouts.create');
    }

    public function store(CreateAboutRequest $request)
    {
        try {
            DB::beginTransaction();
    
            // استخراج جميع الحقول ما عدا الصور وساعات العمل
            $data = $request->except(['image1', 'image2', 'opening_hours']);
    
            // معالجة الصور
            $imageFields = ['image1', 'image2'];
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $this->uploadImage($request->file($field), 'about');
                }
            }
    
            // تحديث أو إنشاء السجل
            $about = About::first();
            if ($about) {
                $about->update($data);
            } else {
                $about = About::create($data);
            }
    
            // تحديث ساعات العمل
            if ($request->has('opening_hours')) {
                // حذف الساعات القديمة
                $about->openingHours()->delete();
    
                // إضافة الجديدة
                foreach ($request->input('opening_hours') as $day => $time) {
                    if (!empty($time['from']) && !empty($time['to'])) {
                        $about->openingHours()->create([
                            'day' => $day,
                            'from' => $time['from'],
                            'to' => $time['to'],
                        ]);
                    }
                }
            }
    
            DB::commit();
            return $this->response_api(200, __('admin.form.added_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    


    public function edit($id)
    {
        $about = About::findOrFail($id);
        return view('admin.abouts.create', compact('about'));
    }

    public function update(CreateAboutRequest $request, About $about)
    {
        try {
            DB::beginTransaction();
    
            // استخراج البيانات ما عدا الصور وساعات العمل
            $data = $request->except([
                'image1', 'image2', 'opening_hours'
            ]);
    
            // معالجة الصور
            $imageFields = ['image1', 'image2'];
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $this->uploadImage($request->file($field), 'about');
                }
            }
    
            // تحديث بيانات about
            $about->update($data);
    
            // تحديث بيانات opening hours
            if ($request->has('opening_hours') && is_array($request->opening_hours)) {
                // حذف السجلات القديمة
                $about->openingHours()->delete();
    
                // إدخال السجلات الجديدة
                foreach ($request->opening_hours as $hour) {
                    $about->openingHours()->create([
                        'day'    => $hour['day'] ?? '',
                        'from'   => $hour['from'] ?? null,
                        'to'     => $hour['to'] ?? null,
                        'status' => $hour['status'] ?? false,
                    ]);
                }
            }
    
            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->response_api(400, $this->exMessage($e));
        }
    }
    


    public function destroy($id)
    {
        About::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
