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
                    <div class="col-md-3 fv-row">
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

                    <div class="col-md-3 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Phone (portal sign-in)</label>
                        <input class="form-control" name="phone" placeholder="2246698063" inputmode="tel"
                               value="{{ isset($employee) ? $employee->phone : '' }}">
                        <div class="text-muted fs-8 mt-1">
                            How this barber signs in to their portal. Punctuation is ignored —
                            (224) 669-8063 and 2246698063 are stored the same way.
                        </div>
                    </div>

                    <div class="col-md-3 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Portal role</label>
                        @php
                            $currentRole = isset($employee)
                                ? ($employee->portal_role ?: \App\Models\Employee::ROLE_BARBER)
                                : \App\Models\Employee::ROLE_BARBER;
                        @endphp
                        <select class="form-select" name="portal_role">
                            @foreach (\App\Models\Employee::PORTAL_ROLES as $value => $label)
                                <option value="{{ $value }}" {{ $currentRole === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted fs-8 mt-1">
                            A <strong>Barber</strong> sees their own bookings in the portal.
                            A <strong>Manager</strong> sees every barber's, grouped by barber,
                            plus the bookings nobody has been assigned. Read-only either way.
                        </div>
                    </div>

                    <div class="col-md-3 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Portal</label>
                        @if (isset($employee) && $employee->hasPortalAccess())
                            <div><span class="badge badge-light-success fs-7">Active</span></div>
                            <div class="text-muted fs-8 mt-1">
                                @if ($employee->must_change_password)
                                    Password set, not yet changed by them.
                                @else
                                    Password chosen by them.
                                @endif
                                @if ($employee->last_login_at)
                                    Last signed in {{ $employee->last_login_at->diffForHumans() }}.
                                @else
                                    Has not signed in yet.
                                @endif
                            </div>
                        @else
                            <div><span class="badge badge-light-warning fs-7">Not set up</span></div>
                            <div class="text-muted fs-8 mt-1">
                                Needs a phone number and a password before they can sign in.
                            </div>
                        @endif
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

    @isset($employee)
        {{-- Outside the form above on purpose: this posts to its own endpoint,
             and saving the profile must never quietly reset a password. --}}
        <div class="card card-flush mt-7">
            <div class="card-header">
                <div class="card-title">
                    <h3>Barber portal password</h3>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-light-primary d-flex align-items-center p-5 mb-7">
                    <div class="d-flex flex-column">
                        <span class="fw-semibold">You type a temporary password and pass it to them.</span>
                        <span class="fs-7 text-muted">
                            They are asked to choose their own the first time they sign in, so the one you
                            type here stops working as soon as they do. Setting a new one here resets that.
                        </span>
                    </div>
                </div>

                <form id="portal_password_form" class="form"
                      action="{{ route('admin.employees.portal_password', $employee->id) }}">
                    @csrf
                    <div class="row g-9">
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Temporary password</label>
                            <input type="text" class="form-control" name="password" autocomplete="off"
                                   placeholder="At least 8 characters">
                            <div class="text-muted fs-8 mt-1">
                                Shown as plain text so you can read it out. It is stored hashed.
                            </div>
                        </div>
                        <div class="col-md-6 fv-row d-flex align-items-end">
                            <button type="submit" class="btn btn-light-primary">
                                <span class="indicator-label">
                                    {{ $employee->hasPortalAccess() ? 'Reset password' : 'Set password' }}
                                </span>
                                <span class="indicator-progress">Please wait
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endisset
@endsection
@push('scripts')
    <script src="{{ asset('admin_assets/js/dashboard/handleSubmitForm.js') }}"></script>
    @isset($employee)
        <script>
            // handleSubmitForm.js is bound to #kt_form alone. This form posts to
            // its own endpoint and must not redirect away from the page, so it
            // gets its own small handler rather than a second copy of that file.
            (function () {
                const form = document.getElementById('portal_password_form');
                if (!form) return;

                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const button = form.querySelector('button[type="submit"]');
                    button.setAttribute('data-kt-indicator', 'on');
                    button.disabled = true;

                    axios.post(form.getAttribute('action'), new FormData(form))
                        .then(function (response) {
                            const ok = response.data.status === 200;
                            Swal.fire({
                                text: response.data.message,
                                icon: ok ? 'success' : 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Done',
                                customClass: { confirmButton: ok ? 'btn btn-primary' : 'btn btn-danger' }
                            }).then(function () {
                                // Reload so the Portal status badge reflects the change.
                                if (ok) window.location.reload();
                            });
                        })
                        .catch(function (error) {
                            // Laravel validation answers with {errors:{field:[msg]}};
                            // response_api() answers with {message}. Both arrive here
                            // because axios rejects any non-2xx.
                            const body = (error.response && error.response.data) || {};
                            const bag = body.errors;
                            Swal.fire({
                                text: bag
                                    ? Object.values(bag)[0][0]
                                    : (body.message || 'Something went wrong.'),
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Done',
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        })
                        .then(function () {
                            button.removeAttribute('data-kt-indicator');
                            button.disabled = false;
                        });
                });
            })();
        </script>
    @endisset
@endpush
