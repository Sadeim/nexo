

window.columns = [
    {data: 'id'},
    {data: 'title'},
    {data: 'date'},
    {data: 'location'},
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
    // إظهار حقول الإدخال
    document.getElementById('sectionTitleText').classList.add('d-none');
    document.getElementById('sectionTitleInput').classList.remove('d-none');

    document.getElementById('sectionDescriptionText').classList.add('d-none');
    document.getElementById('sectionDescriptionInput').classList.remove('d-none');

    document.getElementById('sectionNoteText').classList.add('d-none');
    document.getElementById('sectionNoteInput').classList.remove('d-none');

    editBtn.classList.add('d-none');
    saveBtn.classList.remove('d-none');
});

saveBtn?.addEventListener('click', () => {
    const url = saveBtn.dataset.url;
    const title = document.getElementById('sectionTitleInput').value;
    const description = document.getElementById('sectionDescriptionInput').value;
    const note = document.getElementById('sectionNoteInput').value;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('#csrf_token').attr('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ title, description, note })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // تحديث النصوص
            document.getElementById('sectionTitleText').textContent = 'Title: ' + title;
            document.getElementById('sectionDescriptionText').textContent = description;
            document.getElementById('sectionNoteText').textContent = note;

            // إرجاع الحقول للنص العادي
            document.getElementById('sectionTitleText').classList.remove('d-none');
            document.getElementById('sectionTitleInput').classList.add('d-none');

            document.getElementById('sectionDescriptionText').classList.remove('d-none');
            document.getElementById('sectionDescriptionInput').classList.add('d-none');

            document.getElementById('sectionNoteText').classList.remove('d-none');
            document.getElementById('sectionNoteInput').classList.add('d-none');

            editBtn.classList.remove('d-none');
            saveBtn.classList.add('d-none');

            toastr.success('Section updated successfully');
        } else {
            toastr.error('Failed to update section');
        }
    })
    .catch(() => {
        toastr.error('Error updating section');
    });
});
document.getElementById('section_toggle')?.addEventListener('change', function () {
    const url = this.dataset.url;

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
        const label = this.nextElementSibling;
        label.textContent = data.is_active ? 'Visible' : 'Hidden';
    });
});
