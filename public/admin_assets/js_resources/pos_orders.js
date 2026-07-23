window.columns = [
    {data: 'order_number'},
    {data: 'employee'},
    {data: 'cashier'},
    {data: 'subtotal'},
    {data: 'tip'},
    {data: 'total'},
    {data: 'method'},
    {data: 'date'},
    {data: 'operations'}
];
window.columnDefs = [
    {
        targets: 0,
        orderable: false,
        sorting: false
    },
    {
        targets: -1,
        orderable: false,
    },
];
