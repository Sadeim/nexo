var CWSaveForm = function () {

    const handleSubmit = () => {
        // Define variables
        let validator;

        // Get elements
        const form = document.getElementById('kt_form');
        const formjs = $("#kt_form");
        const submitButton = document.getElementById('kt_submit');

        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                plugins: {
                    declarative: new FormValidation.plugins.Declarative({
                        html5Input: true,
                        prefix: 'data-fv-',
                    }),
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            }
        );

        // Handle submit button
        submitButton.addEventListener('click', e => {
            e.preventDefault(); 
            // Validate form before submit
            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');
                    let action = formjs.attr('action');
                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Disable submit button whilst loading
                        let formData = new FormData(formjs[0]);
                        submitButton.disabled = true;
                        axios.post(`${action}` , formData).then(response => {
                            console.log(response.data);
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            if(response.data.status == 200) {

                                Swal.fire({
                                    text: response.data.message,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Done",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        // Enable submit button after loading
                                        window.location = form.getAttribute("data-kt-redirect");

                                        // Redirect to customers list page
                                        // window.location = form.getAttribute("data-kt-redirect");
                                    }else {
                                        Swal.fire({
                                            text: response.data.message,
                                            icon: "error",
                                            buttonsStyling: false,
                                            confirmButtonText: "Done",
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
                            }else {
                                Swal.fire({
                                    text: response.data.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Done",
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
                        }).catch(error => {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                    
                            // استخرج رسالة الخطأ من الرد إن وُجدت
                            let errorMessage = "حدث خطأ ما.";
                            if (error.response && error.response.data && error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                    
                            // عرض الخطأ للمستخدم
                            Swal.fire({
                                text: errorMessage,
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Done",
                                customClass: {
                                    confirmButton: "btn btn-danger"
                                }
                            });
                        });


                    } else {
                        Swal.fire({
                            text: "Sorry, looks like there are some errors detected, please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            }
        })
    }

    // Public methods
    return {
        init: function () {
            // Init forms
            handleSubmit();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    CWSaveForm.init();
});
