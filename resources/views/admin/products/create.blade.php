@extends('admin.layouts.master', ['is_active_parent' => 'products', 'is_active' => 'products'])
@section('title')
    {{ __('admin.global.add_new_product') }}
@endsection
@section('content')
    <form id="kt_form" class="form row" data-kt-redirect="{{ route('admin.products.index') }}"
        action="{{ isset($product) ? route('admin.products.update', $product->id) : route('admin.products.store') }}">
        @csrf
        @isset($product)
            @method('PATCH')
            @endif

            <div class="">
                <div class="page-content-header d-flex" style="justify-content: end;">
                    <div class="mr-5 ml-5">
                        <a href="{{ route('admin.products.index') }}" id="kt_ecommerce_add_product_cancel"
                            class="btn btn-outline btn-outline-dashed me-2 mb-2 cancel px-18">
                            {{ __('admin.form.cancel') }}
                        </a>
                        <button type="submit" id="kt_submit" class="btn btn-primary px-18">
                            <span class="indicator-label">{{ __('admin.admins.save') }}</span>
                            <span class="indicator-progress">{{ __('admin.admins.please_wait') }}
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-5 col-lg-3 mb-7">
                <div class="card card-flush">
                    <div class="card-header">
                        <div class="card-title" style="width: 100%">
                            <ul class="nav nav-tabs nav-pills border-0 flex-row flex-md-column mb-3 mb-md-0 fs-6 py-5"
                                style="width: 100%">
                                <li class="nav-item" style="list-style-type: none;">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#text">Main</a>
                                </li>
                                {{-- <li class="nav-item" style="list-style-type: none;">
                                    <a class="nav-link" data-bs-toggle="tab" href="#seo">Seo</a>
                                </li> --}}
                                <li class="nav-item" style="list-style-type: none;">
                                    <a class="nav-link" data-bs-toggle="tab" href="#media">Media</a>
                                </li>

                                <li class="nav-item" style="list-style-type: none;">
                                    <a class="nav-link" data-bs-toggle="tab" href="#attributes">Variants</a>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content col-lg-9">
                <div class="tab-pane fade show active" id="text" role="tab-panel">
                    <div class="d-flex flex-column gap-5 mb-5">

                        <div class="card card-flush generalDataTap">
                            <div class="salesTitle">
                                <h3>{{ __('admin.global.name_and_description_and_price') }}</h3>
                            </div>
                            <div class="card-body pt-0">

                                <div class="tab-content mt-5" id="myTabContent">

                                    <div class="tab-pane fade arabic-tab active show">
                                        <div class="row">
                                            <div class="col-md-12 fv-row fv-plugins-icon-container ">
                                                <div class="mb-5 fv-row">
                                                    <label class="required form-label">
                                                        {{ __('admin.global.product_name') }}
                                                    </label>
                                                    <input type="text" name="name" id="name"
                                                        class="form-control mb-2"
                                                        placeholder="{{ __('admin.global.product_name') }}"
                                                        value="{{ isset($product) ? $product->name : '' }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label ">{{ __('admin.global.product_description') }}</label>
                                            <textarea name="description" id="summernote">{{ isset($product) ? $product->description : '' }}</textarea>
                                        </div>

                                        <div class="col-md-12 fv-row fv-plugins-icon-container">
                                            <div class="mb-5 fv-row">
                                                <label class="required form-label">
                                                    {{ __('admin.global.product_price') }}
                                                </label>
                                                <input type="number" name="price" id="price" step="0.01"
                                                    class="form-control mb-2"
                                                    placeholder="{{ __('admin.global.product_price') }}"
                                                    value="{{ isset($product) ? $product->price : '' }}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="tab-pane fade" id="media" role="tab-panel">
                    <div class="d-flex flex-column gap-5 mb-5">
                        <div class="card card-flush generalDataTap draggable-zone">
                            <div class="salesTitle">
                                <h3>Add Media</h3>
                            </div>

                            <!--begin::Repeater-->
                            <div class="mx-5 " id="media_repeater">
                                <div class="form-group draggable">
                                    <div data-repeater-list="media_repeater">
                                        @if (isset($product))
                                            @foreach ($product->images as $product_image)
                                                <div data-repeater-item class="draggable"
                                                    style="border: 1px solid #988f8f40;border-radius: 10px;margin: 20px;padding: 15px;">
                                                    <div class="form-group row mb-5 ">
                                                        <div class="card-toolbar">
                                                            <a href="#"
                                                                class="btn btn-icon btn-hover-light-primary draggable-handle">
                                                                <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                                <span class="svg-icon svg-icon-2x">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path
                                                                            d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                                                            fill="currentColor"></path>
                                                                        <path opacity="0.3"
                                                                            d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                                                            fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                                <!--end::Svg Icon-->
                                                            </a>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="image-input image-input-empty"
                                                                data-kt-image-input="true"
                                                                style="background-image: url({{ getImageUrl($product_image->path, 'medium') }})">
                                                                <div class="image-input-wrapper w-125px h-125px"></div>
                                                                <input name="id" value="{{ $product_image->id }}"
                                                                    type="hidden" />
                                                                <label
                                                                    class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                    data-kt-image-input-action="change"
                                                                    data-bs-toggle="tooltip" data-bs-dismiss="click"
                                                                    title="Change avatar">
                                                                    <i class="bi bi-pencil-fill fs-7"></i>
                                                                    <input type="file" name="image"
                                                                        accept=".png, .jpg, .jpeg" />
                                                                    {{-- <input type="hidden" name="avatar_remove" /> --}}
                                                                </label>
                                                                <span
                                                                    class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                    data-kt-image-input-action="cancel"
                                                                    data-bs-toggle="tooltip" data-bs-dismiss="click"
                                                                    title="Cancel avatar">
                                                                    <i class="bi bi-x fs-2"></i>
                                                                </span>
                                                                <span
                                                                    class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                    data-kt-image-input-action="remove"
                                                                    data-bs-toggle="tooltip" data-bs-dismiss="click"
                                                                    title="Remove avatar">
                                                                    <i class="bi bi-x fs-2"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Image title</label>
                                                            <input class="form-control" name="image_title"
                                                                class="form-control mb-2 w-100"
                                                                value="{{ $product_image->image_title ?? '' }}"
                                                                placeholder="" />
                                                        </div>
                                                        <div class="col-md-2">
                                                            <a href="javascript:;" data-repeater-delete
                                                                class="btn btn-sm btn-light-danger mt-3 mt-md-9">
                                                                <i class="la la-trash-o fs-3"></i>Delete
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <div data-repeater-item class="draggable"
                                            style="border: 1px solid #988f8f40;border-radius: 10px;margin: 20px;padding: 15px;">
                                            <div class="form-group row mb-5 ">
                                                <div class="card-toolbar">
                                                    <a href="#"
                                                        class="btn btn-icon btn-hover-light-primary draggable-handle">
                                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                                        <span class="svg-icon svg-icon-2x">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                                                    fill="currentColor"></path>
                                                                <path opacity="0.3"
                                                                    d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                                                    fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </a>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="image-input image-input-empty" data-kt-image-input="true"
                                                        style="background-image: url({{ asset('admin_assets/media/svg/avatars/blank.svg') }})">
                                                        <div class="image-input-wrapper w-125px h-125px"></div>
                                                        <label
                                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                            data-bs-dismiss="click" title="Change avatar">
                                                            <i class="bi bi-pencil-fill fs-7"></i>
                                                            <input type="file" name="image"
                                                                accept=".png, .jpg, .jpeg" />
                                                            {{-- <input type="hidden" name="avatar_remove" /> --}}
                                                        </label>
                                                        <span
                                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                            data-bs-dismiss="click" title="Cancel avatar">
                                                            <i class="bi bi-x fs-2"></i>
                                                        </span>
                                                        <span
                                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                            data-bs-dismiss="click" title="Remove avatar">
                                                            <i class="bi bi-x fs-2"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Image title</label>
                                                    <input class="form-control" name="image_title"
                                                        class="form-control mb-2 w-100" placeholder="" />
                                                </div>
                                                <div class="col-md-2">
                                                    <a href="javascript:;" data-repeater-delete
                                                        class="btn btn-sm btn-light-danger mt-3 mt-md-9">
                                                        <i class="la la-trash-o fs-3"></i>Delete
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-5">
                                    <a href="javascript:;" data-repeater-create class="btn btn-primary px-18">
                                        <i class="la la-plus"></i>Add
                                    </a>
                                </div>
                            </div>
                            <!--end::Repeater-->
                        </div>
                        {{-- <div class="card card-flush generalDataTap draggable-zone">
                            <div class="salesTitle">
                                <h3>Add video</h3>
                            </div>
                            <!--begin::Repeater-->
                            <div class="mx-5">
                                <div class="form-group">
                                    <div data-repeater-item
                                        style="border: 1px solid #988f8f40;border-radius: 10px;margin: 20px;padding: 15px;">
                                        <div class="form-group row mb-5">
                                            <div class="col-md-12">
                                                <label class="form-label">Video</label>
                                                <input type="file" class="form-control" name="video"
                                                    value="{{ isset($product) ? $product->video : '' }}"
                                                    class="form-control mb-2 w-100" accept="video/*" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end::Repeater-->
                        </div> --}}
                    </div>
                </div>

                <div class="tab-pane fade" id="attributes" role="tab-panel">
                    <div class="d-flex flex-column gap-5 mb-5">
                        <div class="card card-flush generalDataTap">
                            <div class="salesTitle">
                                <h3>Variants</h3>
                            </div>

                            <div class="mx-5" id="variants-container">
                                @if (isset($product) && $product->variants->count())
                                    @foreach ($product->variants as $i => $variant)
                                        <div class="variant-item mb-5 p-4 border rounded" data-index="{{ $i }}">
                                            <input type="hidden" name="variants[{{ $i }}][id]"
                                                value="{{ $variant->id }}">
                                            <div class="row mb-4">
                                                <div class="col-md-3">
                                                    <label class="form-label">Price</label>
                                                    <input type="number" name="variants[{{ $i }}][price]"
                                                        class="form-control" value="{{ $variant->price }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">SKU</label>
                                                    <input type="text" name="variants[{{ $i }}][sku]"
                                                        class="form-control" value="{{ $variant->sku }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Quantity</label>
                                                    <input type="number" name="variants[{{ $i }}][quantity]"
                                                        class="form-control"
                                                        value="{{ $variant->inventory->quantity ?? 0 }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Barcode</label>
                                                    <input type="number" name="variants[{{ $i }}][barcode]"
                                                        class="form-control" value="{{ $variant->barcode ?? '' }}">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <button type="button" class="btn btn-danger btn-remove-variant">
                                                        <i class="la la-trash-o"></i> Delete
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="attributes-section border-top pt-4">
                                                <div class="row mb-3">
                                                    @foreach ($attributes as $attribute)
                                                        <div class="col-md-4 mb-3">
                                                            <label class="form-label">{{ $attribute->name }}</label>
                                                            <select class="form-select"
                                                                name="variants[{{ $i }}][attribute_values][{{ $attribute->id }}]">
                                                                <option value="">Choose {{ $attribute->name }}</option>
                                                                @foreach ($attribute->attributeValues as $val)
                                                                    <option value="{{ $val->id }}"
                                                                        {{ $variant->attributeValues->contains($val->id) ? 'selected' : '' }}>
                                                                        {{ $val->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- كود فارغ لأول variant --}}
                                @endif
                            </div>

                            <div class="form-group mb-5 px-5">
                                <button type="button" class="btn btn-primary" id="btn-add-variant">
                                    <i class="la la-plus"></i> Add new variant
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- hidden template -->
                {{-- for hidd another template --}}
                <template id="variant-template">
                    <div class="variant-item mb-5 p-4 border rounded" data-index="__INDEX__">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <label class="form-label">Price</label>
                                <input type="number" name="variants[__INDEX__][price]" class="form-control" value="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">SKU</label>
                                <input type="text" name="variants[__INDEX__][sku]" class="form-control" value="">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="variants[__INDEX__][quantity]" class="form-control"
                                    value="0">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Barcode</label>
                                <input type="text" name="variants[__INDEX__][barcode]" class="form-control"
                                    value="">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-remove-variant">
                                    <i class="la la-trash-o"></i> Delete
                                </button>
                            </div>
                        </div>

                        <div class="attributes-section border-top pt-4">
                            <div class="row mb-3">
                                @foreach ($attributes as $attribute)
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">{{ $attribute->name }}</label>
                                        <select class="form-select"
                                            name="variants[__INDEX__][attribute_values][{{ $attribute->id }}]">
                                            <option value="">Choose {{ $attribute->name }}</option>
                                            @foreach ($attribute->attributeValues as $val)
                                                <option value="{{ $val->id }}">{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </form>
    @endsection
    @push('scripts')
        {{-- <script src="{{ asset('admin_assets/js/dashboard/save-product-attributes.js') }}"></script> --}}
        <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
        <script src="{{ asset('admin_assets/js/summernote-lite.min.js') }}"></script>

        <script>
            $('#summernote').summernote({
                placeholder: '{{ __('admin.global.type_your_text_here') }}',
                tabsize: 2,
                height: 120,
                lang: 'ar-AR',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        </script>
        <script src="{{ asset('admin_assets/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
        <script src="{{ asset('admin_assets/js/custom/documentation/forms/formrepeater/advanced.js') }}"></script>
        <script src="{{ asset('admin_assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
        <script>
            // ---------------------draggable script--------------
            var containers = document.querySelectorAll(".draggable-zone");

            // if (containers.length === 0) {
            //     return false;
            // }

            var swappable = new Swappable.default(containers, {
                draggable: ".draggable",
                handle: ".draggable .draggable-handle",
                mirror: {
                    //appendTo: selector,
                    appendTo: "body",
                    constrainDimensions: true
                }
            });
            // ---------------------draggable script--------------
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('variants-container');
                const template = document.getElementById('variant-template').innerHTML;
                let nextIndex = container.querySelectorAll('.variant-item').length;

                document.getElementById('btn-add-variant').addEventListener('click', function() {
                    const html = template.replace(/__INDEX__/g, nextIndex);
                    const div = document.createElement('div');
                    div.innerHTML = html;
                    container.appendChild(div.firstElementChild);
                    nextIndex++;
                });

                container.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-remove-variant')) {
                        e.target.closest('.variant-item').remove();
                    }
                });
            });
        </script>
        <script>
            $(function() {
                $("#select2_category").select2();
            });
        </script>
    @endpush
