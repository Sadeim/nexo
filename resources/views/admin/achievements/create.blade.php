@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'achievements'])
@section('title', isset($achievement) ? __('admin.global.edit_achievement') : __('admin.global.add_new_achievement'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.achievements.index') }}"
          action="{{ isset($achievement) ? route('admin.achievements.update', $achievement->id) : route('admin.achievements.store') }}" method="POST">
        @csrf
        @isset($achievement)
            @method('PATCH')
        @endisset

        <div class="page-content-header mb-5">
            <h2 class="table-title">{{ __('admin.global.add_new_achievement') }}</h2>
        </div>
        
        <!-- Sidebar: Status Section -->
        {{-- <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <div class="card card-flush">
                <div class="card-header">
                    <div class="card-title">
                        <h3>{{ __('admin.form.status') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="status" value="0">
                            <input class="form-check-input btn active_operation" type="checkbox" name="status" 
                                @if(isset($achievement) && $achievement->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div>
        </div> --}}
        
        <!-- Main Content: Achievement Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="salesTitle">
                    <h3>{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row">
                        <div class="col-md-4 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Enter Achievement Title"
                                    value="{{ isset($achievement) ? $achievement->title : '' }}">
                            </div>
                        </div>
                        <!-- Number Field -->
                        <div class="col-md-4 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Year</label>
                                <input type="text" name="year" class="form-control" placeholder="Enter Achievement Year"
                                    value="{{ isset($achievement) ? $achievement->year : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 fv-row">
                            <div class="mb-5">
                                <label class="required form-label">Description</label>
                                <textarea name="description" class="form-control">{{ isset($achievement) ? $achievement->description : '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end right">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.achievements.index') }}" id="kt_ecommerce_add_product_cancel" class="btn btn-light me-5 cancel">
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
