// window.columns tells DataTables how to display each column in your table
window.columns = [
    {data: 'id'},
    {data: 'name'},
    {data: 'price'},
    {data: 'description'},
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