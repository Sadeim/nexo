"use strict";
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var validator;
var validator_extra;
var phone_input;
var category_search = 0;
var myData = {};
var myCitiesData = {
    region_id: null
};
var sesstion_target_id = '';
var tables;
// Class definition
var extra_formjs = $('#form_extra');
var KTDatatablesServerSide = function () {
    var table;
    var dt;
    var dt_city;
    var footerCallBack;
    var form = document.getElementById('form_post');
    var formjs = $('#form_post');
    var extra_form = document.getElementById('form_extra');

    var modal = $('#m_store_modal') || null;
    var extraSubmitButton = document.getElementById('form_extra_submit');
    var filterPayment;
    // Private functions
    var initDatatable = function () {
        dt = $('#oc_datatable').DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            language: {
                info: ` Show _START_ to _END_ from _TOTAL_`,
                // search: 'البحث'
                emptyTable: no_data_available_in_table,
                infoEmpty: showing_no_records
            },
            // order: [[0, 'desc']],
            // stateSave: true,
            select: {
                style: 'os',
                selector: 'td:first-child',
                className: 'row-selected'
            },
            ajax: {
                url: window.datatable_url,
                data: function (d) {
                    return $.extend(d, myData);
                },
            },
            columns: window.columns,
            columnDefs: window.columnDefs,
            "footerCallback": window.footerCallBack,

            // Add data-filter attribute
        });
        dt_city = $('#kt_datatable_cities').DataTable({
            searchDelay: 500,
            processing: true,
            serverSide: true,
            language: {
                info: ` Show _START_ to _END_ from _TOTAL_`,
                // search: 'البحث'
            },
            // order: [[0, 'desc']],
            // stateSave: true,
            select: {
                style: 'os',
                selector: 'td:first-child',
                className: 'row-selected'
            },
            ajax: {
                url: window.datatable_cities_url,
                data: function (d) {
                    return $.extend(d, myCitiesData);
                },
            },
            columns: window.city_columns,
            columnDefs: window.city_columnDefs,
            // Add data-filter attribute
        });
        table = dt.$;
        tables = dt;
        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
            if ($(".datatable-checkbox").length > 0) {
                initToggleToolbar();
                toggleToolbars();
            }
            handleDeleteRows();
            KTMenu.createInstances();
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
        dt_city.on('draw', function () {
            if ($("#oc_datatable").length > 0) {
                initToggleToolbar();
                toggleToolbars();
            }

            handleDeleteRows();
            KTMenu.createInstances();
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    };

    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value, 'search', true, false).draw();
        });
        $(document).on('change', '#kt_filter_farmer', function (e) {
            dt.column(2).search(e.target.value, 'farmer_id', true, false).clear().draw();
        });
        $(document).on('change', '#kt_filter_crop', function (e) {
            dt.column(4).search(e.target.value, 'crop_id',1, true, false).clear().draw();
            // dt.search(e.target.value, 'crop_id', true, false).clear().draw();
        });
        $(document).on('change', '#kt_filter_farm', function (e) {
            dt.search(e.target.value, 'farm_id', true, false).clear().draw();
        });
    };
    var handleSubDatatableShow = () => {
        $(document).on('click', '#oc_datatable .edit_sub_datatable', e => {
            let $this = $(e.currentTarget);
            let id = $this.data('id');
            myCitiesData.region_id = id;
            dt_city.ajax.reload();
            let title = $this.data('title');
            $("#m_cities_modal .span_title").text(title);
            $("#create_city_form").data('parent', id);
            $("#m_cities_modal").modal('show');
        });
    };
 
    // Filter Datatable
    var handleShowEditModal = () => {
        if (sesstion_target_id != '') {
            axios.get(`${sesstion_target_id}`).then(response => {
                if (response.data.status == 200) {
                    $("#m_store_modal .modal-content").empty().append(response.data.item.render);
                    KTImageInput.createInstances();
                    $('#category_main').select2();
                    $('#category_sub_sub').select2();
                    $('.summernote').summernote({
                        placeholder: 'المحتوى',
                        height: 250
                    });
                    if ($(".phone-input").length > 0) {
                        var input = document.querySelector(".phone-input");
                        phone_input = intlTelInput(input, {
                            initialCountry: "ps",
                            hiddenInput: 'country_code',
                            nationalMode: false,
                            autoHideDialCode: false,
                            separateDialCode: true
                        });
                        $(".phone-input").blur();
                    }
                    initFormValidation();
                    $("#m_store_modal").modal("show");
                }
            }).catch(err => {
                customSweetAlert(
                    'error',
                    err.response.data.message,
                    ''
                );
            });
        }
    };
    var handleShowRows = () => {
        $(document).on('click', '.add', e => {
            e.preventDefault();
            let $this = $(e.currentTarget);
            let target_url = $this.data('url');
            axios.get(`${target_url}`).then(response => {
                if (response.data.status == 200) {
                    $("#online_courses_modal_modal .modal-content").empty().append(response.data.item.render);
                    // console.log(response.data.item.render);
                    // KTImageInput.createInstances();
                    // $('#category_main').select2();
                    // $('#category_sub_sub').select2();
                    // $('.summernote').summernote({
                    //     placeholder: 'المحتوى',
                    //     height: 250
                    // });
                    // if ($(".phone-input").length > 0) {
                    //     var input = document.querySelector(".phone-input");
                    //     phone_input = intlTelInput(input, {
                    //         initialCountry: "ps",
                    //         hiddenInput: 'country_code',
                    //         nationalMode: false,
                    //         autoHideDialCode: false,
                    //         separateDialCode: true
                    //     });
                    //     $(".phone-input").blur();
                    // }
                    // initFormValidation();
                    $("#online_courses_modal_modal").modal("show");

                }
            }).catch(err => {

                customSweetAlert(
                    'error',
                    err.response.data.message,
                    ''
                );
            });
        });


        $(document).on('click', '.showModal', e => {
            e.preventDefault();
            let $this = $(e.currentTarget);
            let target_url = $this.data('url');
            axios.get(`${target_url}`).then(response => {
                if (response.data.status == 200) {
                    $("#online_courses_modal_modal .modal-content").empty().append(response.data.item.render);
                    $("#online_courses_modal_modal").modal("show");

                }
            }).catch(err => {

                customSweetAlert(
                    'error',
                    err.response.data.message,
                    ''
                );
            });
        });

    };
 
    var handleCheckRows = () => {
        $(document).on('change', `#oc_datatable .checkbox`, function () {
            let check = false;
            let ids = [];
            // let level = $('#oc_datatable').attr('data-level');
            $(`.oc_datatable .checkbox`).each(function (index) {
                if ($(this).is(":checked")) {
                    check = true;
                    ids.push($(this).val());
                } else {
                    $("#bluck_delete").addClass("d-none");
                }
            });
            if (check) {
                $("#bluck_delete").removeClass("d-none");
            } else {
                $("#bluck_delete").addClass("d-none");
            }
        });
        $(document).on("click", ".check-all", function () {
            if ($(this).is(":checked")) {
                $(`.oc_datatable .checkbox`).prop("checked", true);
            } else {
                $(`.oc_datatable .checkbox`).prop("checked", false);
            }
        });
    };
    var handleDeleteRows = () => {
        const deleteButtons = document.querySelectorAll('[data-kt-docs-table-filter="delete_row"]');
        deleteButtons.forEach(d => {
            d.addEventListener('click', function (e) {
                e.preventDefault();
                let target_url = e.target.getAttribute('data-action');
                let customerName;

                if (target_url == null) {
                    target_url = e.target.parentElement.getAttribute('data-action');
                }
                let sub_table = e.target.getAttribute('data-table');

                const parent = e.target.closest('tr');
                if (e.target.parentElement.getAttribute('data-name')) {
                    customerName = e.target.parentElement.getAttribute('data-name');
                } else {
                    customerName = parent.querySelectorAll('td')[1].innerText;
                }
                Swal.fire({
                    // text: delete_item+`${customerName} ؟`,
                    // icon: "warning",
                    // showCancelButton: true,
                    // buttonsStyling: false,
                    // confirmButtonText: delete_confirm,
                    // cancelButtonText: exit,
                    // customClass: {
                    //     confirmButton: "btn fw-bold btn-danger",
                    //     cancelButton: "btn fw-bold btn-active-light-primary"
                    // }

                    html: `
                <h3>`+ delete_item + `</h3>
                <p>` + are_you_sure_you_want_to_delete + ` </p>
                <div class="bg-light text-start py-8 px-5 rounded-3 mt-7">
                    ${customerName}
                </div>`,
                    buttonsStyling: false,
                    showCancelButton: true,
                    cancelButtonText: cancel,
                    confirmButtonText: delete_confirm,
                    customClass: {
                        confirmButton: "btn btn-primary",
                        cancelButton: 'btn btn-outline text-gray-500',
                        popup: 'custom-popup'
                    }
                }).then(function (result) {
                    if (result.value) {
                        Swal.fire({
                            // text: "يتم حذف " + customerName + 'الرجاء الإنتظار',
                            text: `${please_wait}`,
                            icon: "info",
                            buttonsStyling: false,
                            showConfirmButton: false,
                        });
                        axios.delete(`${target_url}`).then(response => {
                            if (response.data.status == 200) {
                                Swal.close();
                                Swal.fire({
                                    // text: "تم حذف " + customerName + "بنجاح",
                                    text: `${deleted_successfully}`,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `${done}`,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    console.log(sub_table)
                                    if (sub_table !== '' && sub_table !== undefined && sub_table !== null) {
                                        dt_city.draw();
                                    } else {
                                        dt.draw();
                                    }
                                });
                            } else {
                                Swal.close();
                                Swal.fire({
                                    // text: customerName + `${it_is_not_deleted}`,
                                    text: response.data.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: `${done}`,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        }).catch(err => {
                            customSweetAlert(
                                'error',
                                err.response.data.message,
                                ''
                            );
                        });
                    }
                    // else if (result.dismiss) {
                    //     Swal.fire({
                    //         text: customerName + `${it_is_not_deleted}`,
                    //         icon: "error",
                    //         buttonsStyling: false,
                    //         confirmButtonText: `${done}`,
                    //         customClass: {
                    //             confirmButton: "btn fw-bold btn-primary",
                    //         }
                    //     });
                    // }
                });
            })
        });

        $(document).on('click', '#bluck_delete', function (e) {
            e.preventDefault();
            let ids = [];
            let target_url = $(this).data('action')
            let level = $('#oc_datatable').attr('data-level');
            $(`.oc_datatable .checkbox`).each(function (index) {
                if ($(this).is(":checked")) {
                    ids.push($(this).val());
                }
                console.log(ids);
            });

            if (ids.length > 0) {
                Swal.fire({
                    text: are_you_sure_to_delete_these_records,
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: delete_confirm,
                    cancelButtonText: exit,
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {
                        axios.post(`${target_url}`, { id: ids }).then(response => {
                            if (response.data.status == 200) {
                                $("#bluck_delete").addClass('d-none');
                                Swal.fire({
                                    text: response.data.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `${done}`,
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        dt = $('#oc_datatable').DataTable();
                                        dt.ajax.reload();
                                    } else {
                                        // alert();
                                        Swal.fire({
                                            text: response.data.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: `${done}`,
                                            customClass: {
                                                confirmButton: "btn btn-danger"
                                            }
                                        }).then(function (result) {
                                            if (result.isConfirmed) {
                                                // Enable submit button after loading

                                                // Redirect to customers list page
                                                // window.location = form.getAttribute("data-kt-redirect");
                                            }
                                        });
                                    }
                                }).catch(err => console.log(err));
                            } else {
                                Swal.fire({
                                    text: response.data.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: `${done}`,
                                    customClass: {
                                        confirmButton: "btn btn-danger"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        // Enable submit button after loading

                                        // Redirect to customers list page
                                        // window.location = form.getAttribute("data-kt-redirect");
                                    }
                                });
                            }
                        });
                    }
                });

            } else {
                // customSweetAlert(
                //     'error',
                //     'الرجاء تحديد عنصر واحد على الأقل للمتابعة',
                //     ''
                // );
            }
        });
    };

    var getErrors = (jqXhr, path) => {
        if (jqXhr != null)
            switch (jqXhr.status) {
                case 401:
                    // $(location).prop('pathname', path);
                    // break;
                    customSweetAlert(
                        'error',
                        jqXhr.responseJSON.message,
                        ''
                    );
                case 400:
                    customSweetAlert(
                        'error',
                        jqXhr.responseJSON.message,
                        ''
                    );
                    break;
                case 422:
                    (function ($) {
                        var $errors = jqXhr.responseJSON;
                        var errorsHtml = '<ul style="list-style-type: none">';
                        $.each($errors, function (key, value) {
                            // form.find(".my-validate")
                            errorsHtml += '<li style="font-family: \'Droid.Arabic.Kufi\' !important; text-align: right">' + value[0] + '</li>';

                        });
                        errorsHtml += '</ul>';
                        customSweetAlert(
                            'error',
                            'حدث خطأ أثناء العملية',
                            errorsHtml
                        );
                    })(jQuery);

                    break;
                default:
                    errorCustomSweet();
                    break;
            }
        return false;
    }
    var handleActiveOperation = () => {
        $(document).on('change', '.active_operation', e => {
            e.preventDefault();
            console.log('activation')
            let $this = $(e.currentTarget);
            let target_url = $this.data('url');
            let customerName = $this.data('title');
            let textCheck;
            let confirmActive;
            if ($this.is(':checked')) {
                $this.prop('checked', false);
                textCheck = `${do_you_want_activate}${customerName} ؟`;
                confirmActive = `${activate}`;
            } else {
                $this.prop('checked', true);
                textCheck = `${do_you_want_deactivate}${customerName} ؟`;
                confirmActive = `${deactivate}`;
            }
            Swal.fire({
                text: textCheck,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: confirmActive,
                cancelButtonText: exit,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    Swal.fire({
                        text: "الرجاء الإنتظار ",
                        icon: "info",
                        buttonsStyling: false,
                        showConfirmButton: false,
                    });
                    axios.post(`${target_url}`).then(response => {
                        if (response.data.status == 200) {
                            Swal.close();
                            if ($this.is(':checked')) {
                                $this.prop('checked', false);
                            } else {
                                $this.prop('checked', true);
                            }
                            Swal.fire({
                                text: "status changed successfully",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `${done}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {

                                dt.draw();
                            });
                        } else {
                            Swal.close();
                        }
                    }).catch(err => {
                        customSweetAlert(
                            'error',
                            err.response.data.message,
                            ''
                        );
                    });

                }
                // else if (result.dismiss) {
                //     Swal.close();
                //     Swal.fire({
                //         text: " لم يتم تغيير حالة " + customerName,
                //         icon: "error",
                //         buttonsStyling: false,
                //         confirmButtonText: done,
                //         customClass: {
                //             confirmButton: "btn fw-bold btn-primary",
                //         }
                //     });
                // }
            });
        });
        $(document).on('change', '.feature_operation', e => {
            e.preventDefault();
            let $this = $(e.currentTarget);
            let target_url = $this.data('url');
            let customerName = $this.data('title');
            let textCheck;
            let confirmActive;
            if ($this.is(':checked')) {
                $this.prop('checked', false);
                textCheck = `هل تريد إظهاره في التطبيق ${customerName} ؟`;
                confirmActive = "إظهار";
            } else {
                $this.prop('checked', true);
                textCheck = `هل تريد إخفائه في التطبيق ${customerName} ؟`;
                confirmActive = "إخفاء";
            }
            Swal.fire({
                text: textCheck,
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: confirmActive,
                cancelButtonText: exit,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    Swal.fire({
                        text: "الرجاء الإنتظار ",
                        icon: "info",
                        buttonsStyling: false,
                        showConfirmButton: false,
                    });
                    axios.post(`${target_url}`).then(response => {
                        if (response.data.status == 200) {
                            Swal.close();
                            if ($this.is(':checked')) {
                                $this.prop('checked', false);
                            } else {
                                $this.prop('checked', true);
                            }
                            Swal.fire({
                                text: "status changed successfully",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: `${done}`,
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {

                                dt.draw();
                            });
                        } else {
                            Swal.close();
                        }
                    }).catch(err => {
                        customSweetAlert(
                            'error',
                            err.response.data.message,
                            ''
                        );
                    });

                } else if (result.dismiss) {
                    Swal.close();
                    Swal.fire({
                        text: " لم يتم تغيير حالة " + customerName,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `${done}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
    };
    // Init toggle toolbar
    var initToggleToolbar = function () {
        const container = document.querySelector('#oc_datatable');
        const checkboxes = container.querySelectorAll('.datatable-checkbox');
        $(document).on('click', '.check-all-datatable', e => {
            let $this = $(e.currentTarget);
            if ($this.is(':checked')) {
                $this.prop('checked', true);
                $(".datatable-checkbox").prop('checked', true);
                toggleToolbars();
            } else {
                $this.prop('checked', false);
                $(".datatable-checkbox").prop('checked', false);
                toggleToolbars();
            }
        });
        $(document).on('click', '.sub-check-all-datatable', e => {
            let $this = $(e.currentTarget);
            if ($this.is(':checked')) {
                $this.prop('checked', true);
                $(".sub-datatable-checkbox").prop('checked', true);
                toggleToolbars();
            } else {
                $this.prop('checked', false);
                $(".sub-datatable-checkbox").prop('checked', false);
                toggleToolbars();
            }
        });
        $(document).on('click', '.datatable-checkbox', () => {
            setTimeout(function () {
                toggleToolbars();
            }, 50);
        });
        $(document).on('click', '.sub-datatable-checkbox', () => {
            setTimeout(function () {
                toggleToolbars();
            }, 50);
        });
        const deleteSelected = document.querySelector('[data-kt-docs-table-select="delete_selected"]');
        deleteSelected.addEventListener('click', function (e) {
            // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
            let target_url = e.target.getAttribute('data-url');
            Swal.fire({
                text: "هل تريد حذف هذه البيانات ؟",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                confirmButtonText: delete_confirm,
                cancelButtonText: exit,
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                },
            }).then(function (result) {
                if (result.value) {
                    let ids = [];
                    $(".datatable-checkbox").each(function () {
                        if (this.checked) {
                            ids.push($(this).val());
                        }
                    });
                    $.ajax({
                        url: target_url,
                        method: 'post',
                        type: 'json',
                        data: {
                            id: ids
                        },
                        beforeSend: function (xhr) {
                            Swal.fire({
                                text: "يتم حذف البيانات الرجاء الإنتظار ",
                                icon: "info",
                                buttonsStyling: false,
                                showConfirmButton: false,
                            });
                        },
                        success: function (response) {
                            Swal.close();
                            if (response.status == true) {
                                Swal.fire({
                                    text: "deleted successfully",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: `${done}`,
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // delete row data from server and re-draw datatable
                                    dt.draw();
                                });
                                const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
                                headerCheckbox.checked = false;
                            }
                        },
                        error: function (response) {
                            toastr.error(response.responseJSON.message);
                        }
                    });
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "لم يتم حذف البيانات المحددة",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: `${done}`,
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    });
                }
            });
        });
        const sub_deleteSelected = document.querySelector('[data-kt-docs-table-select="sub_delete_selected"]');
        if (sub_deleteSelected !== null) {
            sub_deleteSelected.addEventListener('click', function (e) {
                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                let target_url = e.target.getAttribute('data-url');
                Swal.fire({
                    text: "هل تريد حذف هذه البيانات ؟",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    showLoaderOnConfirm: true,
                    confirmButtonText: delete_confirm,
                    cancelButtonText: exit,
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    },
                }).then(function (result) {
                    if (result.value) {
                        let ids = [];
                        $(".sub-datatable-checkbox").each(function () {
                            if (this.checked) {
                                ids.push($(this).val());
                            }
                        });
                        $.ajax({
                            url: target_url,
                            method: 'post',
                            type: 'json',
                            data: {
                                id: ids
                            },
                            beforeSend: function (xhr) {
                                Swal.fire({
                                    text: "يتم حذف البيانات الرجاء الإنتظار ",
                                    icon: "info",
                                    buttonsStyling: false,
                                    showConfirmButton: false,
                                });
                            },
                            success: function (response) {
                                Swal.close();
                                if (response.status == true) {
                                    Swal.fire({
                                        text: "deleted successfully",
                                        icon: "success",
                                        buttonsStyling: false,
                                        confirmButtonText: `${done}`,
                                        customClass: {
                                            confirmButton: "btn fw-bold btn-primary",
                                        }
                                    }).then(function () {
                                        // delete row data from server and re-draw datatable
                                        dt_city.draw();
                                    });
                                    const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
                                    headerCheckbox.checked = false;
                                }
                            },
                            error: function (response) {
                                toastr.error(response.responseJSON.message);
                            }
                        });
                    } else if (result.dismiss === 'cancel') {
                        Swal.fire({
                            text: "لم يتم حذف البيانات المحددة",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: `${done}`,
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            });
        }
    };
    // Toggle toolbars
    var toggleToolbars = function () {
        // Define variables
        const container = document.querySelector('#oc_datatable');
        const toolbarBase = document.querySelector('[data-kt-docs-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');
        // Select refreshed checkbox DOM elements
        const allCheckboxes = container.querySelectorAll('tbody .datatable-checkbox');
        let checkedState = false;
        let count = 0;
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });
        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }

        const sub_container = document.querySelector('#kt_datatable_cities');
        if (sub_container !== null) {
            const sub_toolbarBase = document.querySelector('[data-kt-docs-table-toolbar="sub"]');
            const sub_toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="sub_selected"]');
            const sub_selectedCount = document.querySelector('[data-kt-docs-table-select="sub_selected_count"]');
            const sub_allCheckboxes = sub_container.querySelectorAll('tbody .sub-datatable-checkbox');
            let sub_checkedState = false;
            let sub_count = 0;
            sub_allCheckboxes.forEach(c => {
                if (c.checked) {
                    sub_checkedState = true;
                    sub_count++;
                }
            });
            // Toggle toolbars
            if (sub_checkedState) {
                sub_selectedCount.innerHTML = sub_count;
                sub_toolbarBase.classList.add('d-none');
                sub_toolbarSelected.classList.remove('d-none');
            } else {
                sub_toolbarBase.classList.remove('d-none');
                sub_toolbarSelected.classList.add('d-none');
            }
        }


    };
    return {
        init: function () {
            initDatatable();
            if ($("#form_post").length > 0) {
                initFormValidation();
            }
            handleSubDatatableShow();
            handleSearchDatatable();
            if ($(".datatable-checkbox").length > 0) {
                initToggleToolbar();
            }
            if (extra_formjs.length > 0) {
                initExtraFormValidation();
            }
            handleShowRows();
            handleCheckRows();
            handleDeleteRows();
            getErrors();
            handleActiveOperation();
        }
    };
}();
// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
$(document).ready(() => {
    $(document).on("blur", ".phone-input", e => {
        let $this = $(e.currentTarget);
        $this.parent().find("input[name='country_code']").val('+' + phone_input.getSelectedCountryData().dialCode)
    });
});
var handelLimitSubmitButtons = (validator, formjs, submitButton, modal = null) => {
    let validatorCheck;
    if (validator === 'limit_validator') {
        validatorCheck = limit_validator;
    }
    if (validatorCheck) {
        validatorCheck.validate().then(function (status) {
            if (status == 'Valid') {
                let ids = [];
                $(".datatable-checkbox").each(function () {
                    if (this.checked) {
                        ids.push($(this).val());
                    }
                });
                var formData = new FormData(formjs[0]);
                for (let i in ids) {
                    formData.append('ids[]', ids[i]);
                }

                var url = formjs.attr('action');
                var _method = formjs.attr('method');
                submitButton.attr('data-kt-indicator', 'on');
                submitButton.attr('disabled', true);
                axios.post(`${url}`, formData).then(response => {
                    if (response.data.status == 200) {
                        submitButton.attr('data-kt-indicator', 'off');
                        submitButton.attr('disabled', false);
                        tables.draw();
                        if (modal) {
                            modal.modal('hide');
                            validatorCheck.resetForm(true);
                        }
                        customSweetAlert(
                            'success',
                            response.data.message,
                            '',
                            function (event) {
                                if (response.data.item.model == 'limit') {

                                }
                            }
                        );
                    } else {
                        submitButton.attr('data-kt-indicator', 'off');
                        submitButton.attr('disabled', false);
                        customSweetAlert(
                            'error',
                            response.message,
                            response.errors_object
                        );
                    }
                }).catch(err => {
                    submitButton.attr('data-kt-indicator', 'off');
                    submitButton.attr('disabled', false);
                    customSweetAlert(
                        'error',
                        err.response.data.message,
                        ''
                    );
                });
            }
        });
    }
}
function customSweetAlert(type, title, html, func, done_btn = null) {
    console.log(done_btn)
    var then_function = func || function () {
    };
    if (done_btn == false) {
        swal.fire({
            title: '<span class="' + type + '">' + title + '</span>',
            icon: type,
            html: html,
            showConfirmButton: false
        }).then(then_function);
    } else {
        swal.fire({
            title: '<span class="' + type + '">' + title + '</span>',
            icon: type,
            html: html,
            confirmButtonText: `تم`,
            // confirmButtonColor: '#56ace0',
            customClass: {
                confirmButton: "btn btn-primary"
            }
            // confirmButtonClass: "btn btn-secondary m-btn m-btn--wide"

        }).then(then_function);
    }

}