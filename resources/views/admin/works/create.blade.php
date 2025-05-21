@extends('admin.layouts.master', ['is_active_parent' => 'home', 'is_active'=> 'works'])

@section('title')
    {{ __('admin.global.add_new_work') }}
@endsection

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm"
        data-kt-redirect="{{ route('admin.works.index') }}"
        action="{{ route('admin.works.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="page-content-header mb-5">
            <h2 class="table-title">{{ __('admin.global.add_new_work') }}</h2>
        </div>

        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            {{-- <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ __('admin.form.status') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="status" value="1">
                            <span class="form-check-label fw-bold text-muted"></span>
                        </label>
                    </div>
                </div>
            </div> --}}

            <div class="card card-flush">
                <div class="card-header card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline image-input-placeholder image-input-empty"
                            data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px"
                                style="background-image: url('@if(isset($work) && $work->image !== null) {{ asset($work->image) }} @else {{ asset('admin_assets/media/svg/files/blank-image.svg') }} @endif')"></div>

                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                            </label>

                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>

                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="d-flex flex-column gap-5">
                <div class="card card-flush generalDataTap">
                    <div class="salesTitle">
                        <h3>{{ __('admin.global.name_and_description') }}</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-5 fv-row">
                            <label class="required form-label">{{ __('admin.form.title') }}</label>
                            <input type="text" name="title" value="{{ isset($work) ? $work->title : ''  }}" class="form-control mb-2" placeholder="{{ __('admin.form.title') }}" />
                        </div>
                        <div class="mb-5 fv-row">
                            <label class="form-label">{{ __('admin.global.category') }}</label>
                            <input type="text" name="category" value="{{ isset($work) ? $work->category : ''  }}" class="form-control mb-2" placeholder="{{ __('admin.global.category') }}" />
                        </div>

                        <div class="mb-5 fv-row">
                            <label class="form-label">{{ __('admin.form.description') }}</label>
                            <textarea name="description" value="{{ isset($work) ? $work->description : ''  }}" class="form-control" rows="4">{{ isset($work) ? $work->description : ''  }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end right">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.works.index') }}" class="btn btn-light me-5 cancel">
                        {{ __('admin.form.cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
