<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PosServiceResource;
use App\Models\PosService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PosServiceController extends Controller
{
    public function index()
    {
        return view('admin.pos_services.index');
    }

    public function datatable(Request $request)
    {
        $items = PosService::query()->orderBy('sort_order')->orderBy('name')->search($request);
        return $this->filterDataTable($items, $request, null, PosServiceResource::class);
    }

    public function create()
    {
        return view('admin.pos_services.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        PosService::create($data);
        return $this->response_api(200, __('admin.form.added_successfully'), '');
    }

    public function edit($id)
    {
        $posService = PosService::findOrFail($id);
        return view('admin.pos_services.create', compact('posService'));
    }

    public function update(Request $request, $id)
    {
        $posService = PosService::findOrFail($id);
        $data = $this->validateData($request, $id);
        $posService->update($data);
        return $this->response_api(200, __('admin.form.updated_successfully'), '');
    }

    public function activate($id)
    {
        $posService = PosService::findOrFail($id);
        $posService->is_active = !$posService->is_active;
        $posService->save();
        return $this->response_api(200, __('admin.form.status_changed_successfully'), '');
    }

    public function destroy($id)
    {
        PosService::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }

    /**
     * One-click "clone the website services (with their prices) into the POS".
     * Idempotent per NAME: existing rows are left alone, only names not
     * already in pos_services get inserted.
     */
    public function importFromWebsite()
    {
        $existing = PosService::pluck('name')->map(fn($n) => mb_strtolower(trim($n)))->all();

        $added = 0;
        DB::transaction(function () use ($existing, &$added) {
            $sort = (int) PosService::max('sort_order') + 1;
            Service::whereNotNull('price')->orderBy('name')->get()->each(function ($s) use ($existing, &$added, &$sort) {
                if (in_array(mb_strtolower(trim((string) $s->name)), $existing, true)) {
                    return;
                }
                PosService::create([
                    'name'       => $s->name,
                    'price'      => (float) $s->price,
                    'is_active'  => true,
                    'sort_order' => $sort++,
                ]);
                $added++;
            });
        });

        return back()->with('success', "Imported {$added} service(s) from the website.");
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:120', Rule::unique('pos_services', 'name')->ignore($id)],
            'price'      => 'required|numeric|min:0',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active'  => 'nullable|boolean',
        ]);
    }
}
