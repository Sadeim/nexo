@extends('admin.layouts.master', ['is_active_parent' => 'menu_items','is_active'=> 'menu_items'])
@section('title', isset($menu_item) ? __('admin.global.edit_menu_item') : __('admin.global.add_new_menu_item'))

@section('content')
    <form id="kt_form" class="form row d-flex flex-column flex-lg-row addForm" data-kt-redirect="{{ route('admin.menu_items.index') }}"
          action="{{ isset($menu_item) ? route('admin.menu_items.update', $menu_item->id) : route('admin.menu_items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($menu_item)
            @method('PATCH')
        @endisset

        <div class="page-content-header mb-5">
            <h2 class="table-title">{{ isset($menu_item) ? __('admin.global.edit_menu_item') : __('admin.global.add_new_menu_item') }}</h2>
        </div>
        
        <!-- Sidebar: Status and Logo Image Section -->
        <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
            <!-- menu_item Logo Section -->
            <div class="card card-flush">
                <div class="card-header justify-content-center p-5">
                    <div class="card-toolbar">
                        <div class="image-input image-input-outline" data-kt-image-input="true">
                            <div class="image-input-wrapper w-200px h-200px" style="background-image: url({{ isset($menu_item) && $menu_item->image ? asset($menu_item->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                                <i class="bi bi-pencil-fill fs-7"></i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                                <i class="bi bi-x fs-2"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content: menu_item Details -->
        <div class="d-flex flex-column flex-row-fluid gap-3 col-lg-9">
            <div class="card card-flush">
                <div class="card-body">
                    <div class="mb-5">
                        <label class="required form-label">{{ __('admin.form.name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter menu item Name"
                               value="{{ isset($menu_item) ? $menu_item->name : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="" cols="30" rows="10" class="form-control">{{ isset($menu_item) ? $menu_item->description : '' }}</textarea>
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Price</label>
                        <input type="text" name="price" class="form-control" placeholder="Enter menu item price"
                               value="{{ isset($menu_item) ? $menu_item->price : '' }}">
                    </div>
                    <div class="mb-5">
                        <label class="form-label">Category</label>
                        <select class="form-select mb-8" data-control="select2" data-placeholder="{{ isset($menu_item) ? $menu_item->category->name : 'Choose category'}}" name="category_id">
                            <option></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    @if(isset($menu_item) && $menu_item->id == $category->id) selected @endif>{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div class="page-buttuns mt-5">
            <div class="row justify-content-between">
                <div class="d-flex justify-content-end">
                    <button type="submit" id="kt_submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                        <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <a href="{{ route('admin.menu_items.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
