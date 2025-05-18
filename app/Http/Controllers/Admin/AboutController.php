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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // استخراج جميع الحقول ما عدا الصور
            $data = $request->except([
                'image1', 'image2',
            ]);

            // معالجة الصور
            $imageFields = [
                'image1', 'image2',
            ];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $data[$field] = $this->uploadImage($request->file($field), 'about');
                }
            }

            // التحديث أو الإنشاء
            $about = About::first();
            if ($about) {
                $about->update($data);
            } else {
                About::create($data);
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

    public function update(CreateAboutRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $about = About::findOrFail($id);

            // استخراج الحقول النصية فقط
            $data = $request->except([
                'image1', 'image2',
            ]);

            // معالجة الصور
            $imageFields = [
                'image1', 'image2',
            ];

            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    // حذف الصورة القديمة إذا كانت موجودة (اختياري)
                    if ($about->$field && Storage::exists($about->$field)) {
                        Storage::delete($about->$field);
                    }
                    $data[$field] = $this->uploadImage($request->file($field), 'about');
                }
            }

            $about->update($data);

            DB::commit();
            return $this->response_api(200, __('admin.form.updated_successfully'), '');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->response_api(400, $this->exMessage($e));
        }
    }


    public function destroy($id)
    {
        About::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
