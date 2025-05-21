@extends('admin.layouts.master', ['is_active_parent' => 'about', 'is_active' => 'approaches'])
@section('title', isset($approach) ? 'Edit Approach' : 'Add New Approach')

@section('content')
<form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm"
      action="{{ isset($approach) ? route('admin.approaches.update', $approach->id) : route('admin.approaches.store') }}"
      method="POST" enctype="multipart/form-data" data-kt-redirect="{{ route('admin.approaches.index') }}">
    @csrf
    @isset($approach)
        @method('PATCH')
    @endisset

    <div class="page-content-header mb-5">
        <h2 class="table-title">{{ isset($approach) ? 'Edit Approach' : 'Add New Approach' }}</h2>
    </div>

    {{-- Sidebar: Images --}}
    <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
        @foreach(['image_1' => 'Image 1', 'image_2' => 'Image 2'] as $field => $label)
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    {{-- <label class="available">{{ $label }}</label> --}}
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                 style="background-image: url({{ isset($approach) && $approach->$field ? asset($approach->$field) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                            </div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                   data-kt-image-input-action="change"
                                   data-bs-toggle="tooltip"
                                   title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="{{ $field }}" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                  data-kt-image-input-action="cancel"
                                  data-bs-toggle="tooltip"
                                  title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Main Content --}}
    <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
        <div class="card card-flush generalDataTap">
            <div class="card-header">
                <h3 class="card-title">Approach Details</h3>
            </div>
            <div class="card-body pt-0">

                {{-- Title --}}
                <div class="mb-5">
                    <label class="required form-label">Title</label>
                    <input type="text" name="title" class="form-control"
                           placeholder="Enter Title"
                           value="{{ isset($approach) ? $approach->title : ''}}">
                </div>

                {{-- Subtitle --}}
                <div class="mb-5">
                    <label class="form-label">Subtitle</label>
                    <input type="text" name="subtitle" class="form-control"
                           placeholder="Enter Subtitle"
                           value="{{ isset($approach) ? $approach->subtitle : ''}}">
                </div>

                {{-- Mission Description --}}
                <div class="mb-5">
                    <label class="form-label">Mission Description</label>
                    <textarea name="mission_description" class="form-control" rows="3"
                              placeholder="Enter mission description">{{ isset($approach) ? $approach->mission_description : ''}}</textarea>
                </div>

                {{-- Mission Points --}}
                <div class="mb-5">
                    <label class="form-label">Mission Points</label>
                    <div id="mission-points-container">
                        @if (isset($approach) && $approach->mission_points)
                            @foreach($approach->mission_points as $idx => $point)
                                <div class="d-flex mb-2 point-row">
                                    <input type="text"
                                        name="mission_points[]"
                                        class="form-control me-2"
                                        placeholder="Point"
                                        value="{{ $point }}">
                                    <button type="button" class="btn btn-danger btn-remove-point">×</button>
                                </div>
                            @endforeach
                        @else
                            @foreach([''] as $idx => $point)
                                <div class="d-flex mb-2 point-row">
                                    <input type="text"
                                        name="mission_points[]"
                                        class="form-control me-2"
                                        placeholder="Point"
                                        value="{{ $point }}">
                                    <button type="button" class="btn btn-danger btn-remove-point">×</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-mission-point">
                        + Add Point
                    </button>
                </div>

                {{-- Vision Description --}}
                <div class="mb-5">
                    <label class="form-label">Vision Description</label>
                    <textarea name="vision_description" class="form-control" rows="3"
                              placeholder="Enter vision description">{{ isset($approach) ? $approach->vision_description : ''}}</textarea>
                </div>

                {{-- Vision Points --}}
                <div class="mb-5">
                    <label class="form-label">Vision Points</label>
                    <div id="vision-points-container">
                      
                        @if (isset($approach) && $approach->vision_points)
                            @foreach($approach->vision_points as $idx => $point)
                                <div class="d-flex mb-2 point-row">
                                    <input type="text"
                                        name="vision_points[]"
                                        class="form-control me-2"
                                        placeholder="Point"
                                        value="{{ $point }}">
                                    <button type="button" class="btn btn-danger btn-remove-point">×</button>
                                </div>
                            @endforeach
                        @else
                            @foreach([''] as $idx => $point)
                                <div class="d-flex mb-2 point-row">
                                    <input type="text"
                                        name="vision_points[]"
                                        class="form-control me-2"
                                        placeholder="Point"
                                        value="{{ $point }}">
                                    <button type="button" class="btn btn-danger btn-remove-point">×</button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-vision-point">
                        + Add Point
                    </button>
                </div>

                {{-- Value Description --}}
                <div class="mb-5">
                    <label class="form-label">Value Description</label>
                    <textarea name="value_description" class="form-control" rows="3"
                              placeholder="Enter value description">{{ isset($approach) ? $approach->value_description : ''}}</textarea>
                </div>

                {{-- Value Points --}}
                <div class="mb-5">
                    <label class="form-label">Value Points</label>
                    <div id="value-points-container">
                        
                        @if (isset($approach) && $approach->value_points)
                            @foreach($approach->value_points as $idx => $point)
                                <div class="d-flex mb-2 point-row">
                                    <input type="text"
                                        name="value_points[]"
                                        class="form-control me-2"
                                        placeholder="Point"
                                        value="{{ $point }}">
                                    <button type="button" class="btn btn-danger btn-remove-point">×</button>
                                </div>
                            @endforeach
                        @else
                            @foreach([''] as $idx => $point)
                                <div class="d-flex mb-2 point-row">
                                    <input type="text"
                                        name="value_points[]"
                                        class="form-control me-2"
                                        placeholder="Point"
                                        value="{{ $point }}">
                                    <button type="button" class="btn btn-danger btn-remove-point">×</button>
                                </div>
                            @endforeach
                        @endif
                        
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-value-point">
                        + Add Point
                    </button>
                </div>

            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="page-buttuns mt-5">
            <div class="d-flex justify-content-end">
                <button type="submit" id="kt_submit" class="btn btn-primary">
                    <span class="indicator-label">Save</span>
                    <span class="indicator-progress">Please wait...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
                <a href="{{ route('admin.approaches.index') }}" class="btn btn-light ms-3">Cancel</a>
            </div>
        </div>
    </div>
</form>

<template id="point-row-template">
    <div class="d-flex mb-2 point-row">
        <input type="text" name="__NAME__[]" class="form-control me-2" placeholder="Point" value="">
        <button type="button" class="btn btn-danger btn-remove-point">×</button>
    </div>
</template>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function setupRepeater(containerId, addButtonId, inputName) {
        const container = document.getElementById(containerId);
        const addBtn = document.getElementById(addButtonId);
        const template = document.getElementById('point-row-template').innerHTML.replace(/__NAME__/g, inputName);

        // Add existing remove handlers
        container.querySelectorAll('.btn-remove-point').forEach(btn =>
            btn.addEventListener('click', () => btn.closest('.point-row').remove())
        );

        addBtn.addEventListener('click', () => {
            const div = document.createElement('div');
            div.innerHTML = template;
            const row = div.firstElementChild;
            row.querySelector('.btn-remove-point').addEventListener('click', () => row.remove());
            container.appendChild(row);
        });
    }

    setupRepeater('mission-points-container', 'btn-add-mission-point', 'mission_points');
    setupRepeater('vision-points-container', 'btn-add-vision-point', 'vision_points');
    setupRepeater('value-points-container', 'btn-add-value-point', 'value_points');
});
</script>
@endpush
