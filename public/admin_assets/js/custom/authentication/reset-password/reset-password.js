"use strict";

// Class Definition
var KTAuthResetPassword = function() {
    // Elements
    var form;
    var formjs = $("#kt_password_reset_form");
    var submitButton;
    var validator;

    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
            form,
            {
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',  // comment to enable invalid state icons
                        eleValidClass: '' // comment to enable valid state icons
                    })
                }
            }
        );

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            // Validate form
            validator.validate().then(function (status) {
                if (status == 'Valid') {
                    // Show loading indication
                    submitButton.setAttribute('data-kt-indicator', 'on');

                    // Disable button to avoid multiple click
                    submitButton.disabled = true;

                    let action = form.getAttribute('action');
                    console.log(action)
                    let formData = new FormData(formjs[0]);
                    axios.post(`${action}` , formData).then(response => {
                        console.log(response.data);
                        if(response.data.status) {
                            submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton.disabled = false;

                            // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                            Swal.fire({
                                text: response.data.message,
                                icon: "success",
                                buttonsStyling: false,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function (result) {
                                    form.querySelector('[name="email"]').value= "";
                            });
                        }else {
                            console.log()
                            Swal.fire({
                                text: response.data.message,
                                icon: "error",
                                buttonsStyling: false,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }).catch(err => {
                        console.log(err.data)
                    });

                    // Simulate ajax request
                    setTimeout(function() {
                        // Hide loading indication
                        submitButton.removeAttribute('data-kt-indicator');

                        // Enable button
                        submitButton.disabled = false;

                    }, 3000);
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: "Sorry, looks like there are some errors detected, please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });
    }

    // Public Functions
    return {
        // public functions
        init: function() {
            form = document.querySelector('#kt_password_reset_form');
            submitButton = document.querySelector('#kt_password_reset_submit');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAuthResetPassword.init();
});
