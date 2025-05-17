@extends('admin.layouts.master', ['is_active_parent' => 'home','is_active'=> 'faqs'])
@section('title', isset($faq) ? __('admin.global.edit_faq') : __('admin.global.add_new_faq'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.faqs.index') }}"
          action="{{ isset($faq) ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}" method="POST">
        @csrf
        @isset($faq)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($faq) ? __('admin.global.edit_faq') : __('admin.global.add_new_faq') }}</h2>
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
                                @if(isset($faq) && $faq->status == 1) checked @endif value="1">
                        </label>
                    </div>
                </div>
            </div>
        </div> --}}
        
        <!-- Main Content: FAQ Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush generalDataTap">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin.global.name_and_description') }}</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="mb-5">
                        <label class="required form-label">Question</label>
                        <input type="text" name="question" class="form-control" placeholder="Enter the FAQ question"
                            value="{{ isset($faq) ? $faq->question : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">Answer</label>
                        <textarea name="answer" class="form-control" rows="4" placeholder="Enter the FAQ answer">{{ isset($faq) ? $faq->answer : '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="page-buttuns mt-5">
            <div class="d-flex justify-content-end">
                <button type="submit" id="kt_submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                    <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
