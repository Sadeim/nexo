@extends('admin.layouts.master', ['is_active_parent' => 'contact','is_active'=> 'contact'])
@section('title')
    contact page
@endsection
@section('content')
@if ($section)
    <div class="container-xxl">
        <div class="card card-flush mb-5" id="sectionCard">
            <div class="card-header justify-content-center p-5">
                <div class="card-toolbar">
                    <div class="image-input image-input-outline" data-kt-image-input="true">
                        <div class="image-input-wrapper w-200px h-200px" 
                             style="background-image: url({{ $section->image ? asset($section->image) : asset('admin_assets/media/svg/files/blank-image.svg') }})">
                        </div>

                        {{-- زر تغيير الصورة --}}
                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                               data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change image">
                            <i class="bi bi-pencil-fill fs-7"></i>
                            <input type="file" name="image" accept=".png, .jpg, .jpeg" id="sectionImageInput" />
                        </label>

                        {{-- زر الإلغاء --}}
                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" 
                              data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel image">
                            <i class="bi bi-x fs-2"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="card-title">
                        {{-- عنوان السكشن إن أردت إضافته --}}
                    </h2>
                </div>

                <div class="d-flex align-items-center gap-4">
                    {{-- Toggle --}}
                    {{-- <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="section_toggle"
                            {{ $section->is_active ? 'checked' : '' }}
                            data-url="{{ route('admin.sections.toggle', $section->id) }}" />
                        <label class="form-check-label" for="section_toggle">
                            {{ $section->is_active ? 'Visible' : 'Hidden' }}
                        </label>
                    </div> --}}

                    {{-- أزرار التعديل --}}
                    {{-- <button class="btn btn-primary" id="editBtn">
                        <i class="fas fa-edit"></i> Edit
                    </button> --}}
                    <button class="btn btn-sm btn-light-success" id="saveBtn"
                        data-url="{{ route('admin.sections.update', $section->id) }}">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </div>

            <div class="card-body">
                {{-- الحقول الأخرى --}}
            </div>
        </div>
    </div>
@endif
@endsection
@push('scripts')
<script>
    const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');

saveBtn?.addEventListener('click', () => {
    const url = saveBtn.dataset.url;
    const formData = new FormData();

    // الحقول النصية - فقط إذا كانت موجودة
    const titleInput = document.getElementById('sectionTitleInput');
    const descriptionInput = document.getElementById('sectionDescriptionInput');
    const noteInput = document.getElementById('sectionNoteInput');
    const imageInput = document.getElementById('sectionImageInput');

    if (titleInput) formData.append('title', titleInput.value.trim());
    if (descriptionInput) formData.append('description', descriptionInput.value.trim());
    if (noteInput) formData.append('note', noteInput.value.trim());
    if (imageInput?.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }

    // التحقق أنه على الأقل تم تعديل شيء واحد
    if (!formData.has('title') && !formData.has('description') && !formData.has('note') && !formData.has('image')) {
        toastr.warning('لم يتم تعديل أي شيء');
        return;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('#csrf_token').attr('content'),
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (titleInput) {
                document.getElementById('sectionTitleText').textContent = 'Title: ' + (titleInput.value.trim() || 'N/A');
                titleInput.classList.add('d-none');
                document.getElementById('sectionTitleText').classList.remove('d-none');
            }

            if (descriptionInput) {
                document.getElementById('sectionDescriptionText').textContent = descriptionInput.value.trim() || 'N/A';
                descriptionInput.classList.add('d-none');
                document.getElementById('sectionDescriptionText').classList.remove('d-none');
            }

            if (noteInput) {
                document.getElementById('sectionNoteText').textContent = noteInput.value.trim() || 'N/A';
                noteInput.classList.add('d-none');
                document.getElementById('sectionNoteText').classList.remove('d-none');
            }

            editBtn?.classList.remove('d-none');
            saveBtn?.classList.add('d-none');

            toastr.success("Section updated successfully");
            location.reload(); // لو بدك تغير الصورة المعروضة
        } else {
            if (data.errors) {
                // عرض أول رسالة خطأ فقط
                const firstKey = Object.keys(data.errors)[0];
                const firstError = data.errors[firstKey][0];
                toastr.error(firstError);
            } else {
                toastr.error("Failed to update section");
            }    
        }
    })
    .catch(() => {
        toastr.error("An error occurred during the update.");
    });
});

</script>
@endpush
@push('modals')
 
@endpush
