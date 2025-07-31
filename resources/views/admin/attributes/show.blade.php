@extends('admin.layouts.master', ['is_active_parent' => 'attributes', 'is_active' => 'attributes'])
@section('title')
    {{ __('admin.global.attribute_details') }}
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <!-- بطاقة تفاصيل الحالة -->
        <div class="col-lg-12 mb-4">
            <div class="card border-left border-4 @if($attribute->status) border-success @else border-danger @endif">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        @if($attribute->status)
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        @else
                            <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                        @endif
                    </div>
                    <div>
                        <h4 class="mb-0">
                            Attribute status:
                            @if($attribute->status)
                                <span class="text-success fw-bold">Active</span>
                            @else
                                <span class="text-danger fw-bold">Inactive</span>
                            @endif
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- بطاقة تفاصيل الخاصية -->
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light pt-5">
                    <h3 class="mb-0">Attribute details</h3>
                </div>
                <div class="card-body">
                    <!-- عرض التفاصيل باللغة العربية -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">{{ __('admin.form.arabic') }}</h5>
                        <div class="row mb-2">
                            <label class="col-sm-3 col-form-label fw-bold">
                                {{ __('admin.global.attribute_name') }}:
                            </label>
                            <div class="col-sm-9">
                                {{ $attribute->name ?? '-' }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-3 col-form-label fw-bold">
                                {{ __('admin.global.attribute_description') }}:
                            </label>
                            <div class="col-sm-9">
                                {!! $attribute->description ?? '-' !!}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- عرض التفاصيل باللغة الإنجليزية -->
                    <div class="mb-4">
                        <h5 class="text-primary mb-3">{{ __('admin.form.english') }}</h5>
                        <div class="row mb-2">
                            <label class="col-sm-3 col-form-label fw-bold">
                                {{ __('admin.global.attribute_name') }}:
                            </label>
                            <div class="col-sm-9">
                                {{ $attribute->name ?? '-' }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <label class="col-sm-3 col-form-label fw-bold">
                                {{ __('admin.global.attribute_description') }}:
                            </label>
                            <div class="col-sm-9">
                                {!! $attribute->description ?? '-' !!}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- قسم الأزرار -->
                <div class="card-footer bg-light text-end">
                    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary me-2">
                        {{ __('admin.form.back') }}
                    </a>
                    <a href="{{ route('admin.attributes.edit', $attribute->id) }}" class="btn btn-primary">
                        {{ __('admin.form.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
