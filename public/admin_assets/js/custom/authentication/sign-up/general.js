"use strict";

// Class definition
var KTSignupGeneral = function() {
    // Elements
    var form;
    var formjs = $("#kt_sign_up_form");
    var submitButton;
    var validator;
    var passwordMeter;
    // Handle form
    var handleForm  = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				plugins: {
					trigger: new FormValidation.plugins.Trigger({
                        event: {
                            password: false
                        }
                    }),

				}
			}
		);

        // Handle form submit

        submitButton.addEventListener('click', function (e) {
            // Prevent button default action
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
                                    form.querySelector('[name="first_name"]').value= "";
                                    form.querySelector('[name="last_name"]').value= "";
                                    form.querySelector('[name="email"]').value= "";
                                    form.querySelector('[name="password"]').value= "";
                                    form.querySelector('[name="phone"]').value= "";

                                    var redirectUrl = form.getAttribute('data-kt-redirect-url');
                                    var newUrl = redirectUrl+'/'+response.data.client_token;

                                    if (newUrl) {
                                        location.href = newUrl;
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
                            submitButton.removeAttribute('data-kt-indicator');

                            // Enable button
                            submitButton.disabled = false;
                        }
                    }).catch(err => {
                        console.log(err.data)

                    });
                    // Simulate ajax request
                    setTimeout(function() {
                        // Hide loading indication

                    }, 2000);
                } else {
                    // Show error popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                    Swal.fire({
                        text: some_errors,
                        icon: "error",
                        buttonsStyling: false,
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        });

        // Handle password input
        // form.querySelector('input[name="password"]').addEventListener('input', function() {
        //     if (this.value.length > 0) {
        //         validator.updateFieldStatus('password', 'NotValidated');
        //     }
        // });
    }

    // Password input validation
    var validatePassword = function() {
        return (passwordMeter.getScore() === 100);
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            // Elements
            form = document.querySelector('#kt_sign_up_form');
            submitButton = document.querySelector('#kt_sign_up_submit');
            passwordMeter = KTPasswordMeter.getInstance(form.querySelector('[data-kt-password-meter="true"]'));

            handleForm ();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSignupGeneral.init();
});
