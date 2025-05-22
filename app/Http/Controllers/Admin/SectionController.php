<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    use SaveImageTrait;

    public function toggle($id)
    {
        $section = Section::findOrFail($id);
        $section->is_active = !$section->is_active;
        $section->save();

        return response()->json(['success' => true, 'is_active' => $section->is_active]);
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'note' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only('title', 'description', 'note');

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadImage($request->file('image'), 'sections');
        }

        $section->update($data);

        return response()->json(['success' => true]);
    }
}
