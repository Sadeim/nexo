

window.columns = [
    {data: 'id'},
    {data: 'title'},
    {data: 'text'},
    {data: 'created_at'},
    {data: 'operations'}
];
window.columnDefs = [
    {
        targets: 0,
        orderable: false,
        sorting:false
    },
    {
        targets: -1,
        orderable: false,
    },
];
const editBtn = document.getElementById('editBtn');
const saveBtn = document.getElementById('saveBtn');

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

            toastr.success("تم تحديث القسم بنجاح");
            location.reload(); // لإظهار الصورة الجديدة فورًا
        } else {
            toastr.error("فشل في تحديث القسم");
        }
    })
    .catch(() => {
        toastr.error("حدث خطأ أثناء التحديث");
    });
});
