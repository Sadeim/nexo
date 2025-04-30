"use strict";

// Class Definition
var KTAuthNewPassword = function() {
    // Elements
    var form;
    var formjs = $('#kt_new_password_form');
    var submitButton;
    var validator;
    var passwordMeter;

    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				fields: {
                    'password': {

                    },
                    'confirm-password': {

                    },
                    'toc': {
                    }
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger({
                        event: {
                            password: false
                        }
                    }),
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

            validator.revalidateField('password');

            validator.validate().then(function(status) {
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
                                    var redirectUrl = form.getAttribute('data-kt-redirect-url');
                                    if (redirectUrl) {
                                        location.href = redirectUrl;
                                    }

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


                    }, 1500);
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

        form.querySelector('input[name="password"]').addEventListener('input', function() {
            if (this.value.length > 0) {
                validator.updateFieldStatus('password', 'NotValidated');
            }
        });
    }

    var validatePassword = function() {
        return  (passwordMeter.getScore() === 100);
    }

    // Public Functions
    return {
        // public functions
        init: function() {
            form = document.querySelector('#kt_new_password_form');
            submitButton = document.querySelector('#kt_new_password_submit');
            passwordMeter = KTPasswordMeter.getInstance(form.querySelector('[data-kt-password-meter="true"]'));

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTAuthNewPassword.init();
});
