@extends('admin.layouts.master', ['is_active_parent' => 'user_management', 'is_active' => 'employees'])
@section('title', isset($employee) ? 'Edit employee' : 'Add new employee')
@section('content')
    <form id="kt_form" class="form" data-kt-redirect="{{ route('admin.employees.index') }}"
          enctype="multipart/form-data"
          action="{{ isset($employee) ? route('admin.employees.update', $employee->id) : route('admin.employees.store') }}">
        @csrf
        @isset($employee)
            @method('PATCH')
        @endisset

        <div class="page-content-header">
            <h2 class="table-title">{{ isset($employee) ? 'Edit employee' : 'Add new employee' }}</h2>
        </div>

        <div class="card card-flush">
            <div class="card-body">
                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">{{ __('admin.form.name') }}</label>
                        <input class="form-control" name="name" placeholder="Employee name"
                               value="{{ isset($employee) ? $employee->name : '' }}">
                    </div>
                    <div class="col-md-3 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Sort order</label>
                        <input type="number" min="0" class="form-control" name="sort_order"
                               placeholder="0"
                               value="{{ isset($employee) ? $employee->sort_order : 0 }}">
                        <div class="text-muted fs-8 mt-1">Lower numbers appear first in the POS panel.</div>
                    </div>
                    <div class="col-md-3 fv-row d-flex align-items-end">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                   {{ !isset($employee) || $employee->is_active ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold ms-2">Active</span>
                        </label>
                    </div>
                </div>

                <div class="row g-9 mb-7">
                    <div class="col-md-4 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Commission rate (% of sales)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" class="form-control"
                                   name="commission_rate" placeholder="0"
                                   value="{{ isset($employee) ? number_format((float) $employee->commission_rate, 2, '.', '') : '0' }}">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="text-muted fs-8 mt-1">
                            Percent of service subtotal that goes to this employee. Tips are always paid 100% on top.
                            Set to 0 for tips-only.
                        </div>
                    </div>
                </div>

                <div class="row g-9">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Avatar (optional)</label>
                        <input type="file" name="avatar" accept="image/*" class="form-control">
                        @if (isset($employee) && $employee->avatar)
                            <div class="mt-3">
                                <img src="{{ asset($employee->avatar) }}"
                                     class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>


        <div class="card card-flush mt-7">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="fw-bold m-0">Public profile</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-5 mb-7">
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-7 text-gray-700">
                                A published barber appears in the <strong>Our Barbers</strong> section on the
                                website, gets their own page, and can be chosen by name when a customer books.
                                Publishing is separate from <strong>Active</strong> above: Active controls the POS,
                                this controls the website. Both must be on for the barber to be visible.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-7">
                    <div class="col-md-4 fv-row d-flex align-items-center">
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <input type="hidden" name="public_profile" value="0">
                            <input class="form-check-input" type="checkbox" name="public_profile" value="1"
                                   {{ isset($employee) && $employee->public_profile ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold ms-2">Show on the website</span>
                        </label>
                    </div>
                    <div class="col-md-8 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Page URL</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ url('/barbers') }}/</span>
                            <input class="form-control" name="slug" placeholder="auto-generated from the name"
                                   value="{{ isset($employee) ? $employee->slug : '' }}">
                        </div>
                        <div class="text-muted fs-8 mt-1">
                            Leave blank to generate one from the name. Lowercase letters, numbers and hyphens.
                            @if (isset($employee) && $employee->slug && $employee->public_profile)
                                &nbsp;·&nbsp;
                                <a href="{{ url('/barbers/' . $employee->slug) }}" target="_blank" rel="noopener">
                                    Preview page <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Title</label>
                        <input class="form-control" name="title" placeholder="Master Barber"
                               value="{{ isset($employee) ? $employee->title : '' }}">
                        <div class="text-muted fs-8 mt-1">Shown under the name on the card and the page.</div>
                    </div>
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text">&#64;</span>
                            <input class="form-control" name="instagram" placeholder="handle"
                                   value="{{ isset($employee) ? $employee->instagram : '' }}">
                        </div>
                        <div class="text-muted fs-8 mt-1">
                            Paste the handle or the full profile link — either is stored as the handle.
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-7">
                    <div class="col-md-12 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Bio</label>
                        <textarea class="form-control" name="bio" rows="4"
                                  placeholder="A couple of sentences about this barber.">{{ isset($employee) ? $employee->bio : '' }}</textarea>
                        <div class="text-muted fs-8 mt-1">
                            The first 155 characters are also used as the page description in search results.
                        </div>
                    </div>
                </div>

                <div class="row g-9 mb-7">
                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Specialties</label>
                        @php
                            $selectedSpecialties = isset($employee) ? ($employee->specialties ?? []) : [];
                        @endphp
                        <select class="form-select" name="specialties[]" multiple size="8">
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}"
                                        {{ in_array($service->id, $selectedSpecialties) ? 'selected' : '' }}>
                                    {{ $service->name }}@if ($service->price !== null) — ${{ number_format((float) $service->price, 2) }}@endif
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">
                            Hold Ctrl (Cmd on Mac) to pick several. Listed with prices on the barber's page.
                            These are the website services, not the POS catalogue.
                        </div>
                    </div>

                    <div class="col-md-6 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Photo</label>
                        <input type="file" name="photo" accept="image/*" class="form-control">
                        <div class="text-muted fs-8 mt-1">
                            Max 5MB. Resized to 800px wide and converted to WebP on upload.
                            A portrait crop works best. Without one, the card shows the barber's initials.
                        </div>

                        @if (isset($employee) && $employee->photo_path)
                            <div class="mt-3 d-flex align-items-center gap-3">
                                <img src="{{ asset($employee->photo_path) }}" alt="{{ $employee->name }}"
                                     style="width:96px;height:120px;object-fit:cover;border-radius:6px;">
                                <label class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" name="remove_photo" value="1">
                                    <span class="form-check-label fw-semibold ms-2">Remove this photo</span>
                                </label>
                            </div>
                        @endif
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
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-light me-5 cancel">{{ __('admin.form.cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
@endpush
