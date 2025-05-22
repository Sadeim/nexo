<script>
    var hostUrl = "assets/";
</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ asset('admin_assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('admin_assets/js/scripts.bundle.js') }}"></script>
<!--begin::Custom Javascript(used by this page)-->
{{-- <script src="{{ asset('admin_assets/js/custom/utilities/modals/users-search.js') }}"></script> --}}
{{-- <script src="{{ asset('admin_assets/js/intlTelInput.min.js') }}"></script> --}}
<!--end::Custom Javascript-->
<script src="{{ asset('admin_assets/js/axios.min.js') }}"></script>
{{-- <script src="{{ asset('admin_assets/js/summernote-lite.min.js') }}"></script> --}}

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
</script>
<script>
    let delete_item = '{{__('admin.form.delete_item')}}';
    let cancel = '{{__('admin.form.cancel')}}';
    let do_you_want_to_delete_this_address = '{{__('admin.form.do_you_want_to_delete_this_address')}}';
    let delete_confirm = '{{__('admin.form.delete')}}';
    let exit = '{{__('admin.form.exit')}}';
    let delete_proccessing = '{{__('admin.form.delete_proccessing')}}';
    let do_you_want_activate = '{{__('admin.form.do_you_want_activate')}}';
    let do_you_want_deactivate = '{{__('admin.form.do_you_want_deactivate')}}';
    let deactivate = '{{__('admin.form.deactivate')}}';
    let activate = '{{__('admin.form.activate')}}';
    let it_is_not_deleted = '{{__('admin.form.it_is_not_deleted')}}';
    let deleted_successfully = '{{__('admin.form.deleted_successfully')}}';
    let done = '{{__('admin.form.done')}}';
    let please_wait = '{{__('admin.form.please_wait')}}';
    let ok_go_it = '{{__('admin.form.ok_go_it')}}';
    let no_data_available_in_table = '{{__('admin.form.no_data_available_in_table')}}';
    let showing_no_records = '{{__('admin.form.showing_no_records')}}';
    let are_you_sure_to_delete_these_records = '{{__('admin.form.are_you_sure_to_delete_these_records')}}';
    let some_errors  = '{{__('admin.form.some_errors ')}}';
    let are_you_sure_you_want_to_delete  = '{{ __('admin.global.are_you_sure_you_want_to_delete') }}';
</script>
<script>
    
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');
const globalLoader = document.getElementById('globalLoader');
editBtn?.addEventListener('click', () => {
    // إظهار الحقول القابلة للتحرير
    document.getElementById('sectionTitleText')?.classList.add('d-none');
    document.getElementById('sectionTitleInput')?.classList.remove('d-none');

    document.getElementById('sectionDescriptionText')?.classList.add('d-none');
    document.getElementById('sectionDescriptionInput')?.classList.remove('d-none');

    document.getElementById('sectionNoteText')?.classList.add('d-none');
    document.getElementById('sectionNoteInput')?.classList.remove('d-none');

    // تبديل الأزرار
    editBtn.classList.add('d-none');
    saveBtn.classList.remove('d-none');
});

saveBtn?.addEventListener('click', () => {
    const url = saveBtn.dataset.url;
    const formData = new FormData();

    // الحقول النصية
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

    if (!formData.has('title') && !formData.has('description') && !formData.has('note') && !formData.has('image')) {
        toastr.warning('لم يتم تعديل أي شيء');
        // إخفاء مؤشر التحميل وإعادة تفعيل الزر
        globalLoader.classList.add('d-none');
        saveBtn.disabled = false;
        return;
    }
globalLoader.classList.remove('d-none');
saveBtn.disabled = true;
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
            location.reload(); // لإظهار الصورة الجديدة فورًا
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
document.getElementById('section_toggle')?.addEventListener('change', function () {
    const toggle = this;
    const url = toggle.dataset.url;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('#csrf_token').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        const label = toggle.nextElementSibling;
        if (data.is_active) {
            label.textContent = 'Visible';
            toastr.success('Activated successfully');
        } else {
            label.textContent = 'Hidden';
            toastr.success('Cancelled successfully');
        }
    })
    .catch(() => {
        // لو صار خطأ نرجع التبديل لحالته السابقة
        toggle.checked = !toggle.checked;
        toastr.error('An error occurred while attempting to activate/cancel');
    });
});

</script>
@stack('scripts')
