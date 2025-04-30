"use strict";

// Class definition
var KTSigninGeneral = function() {
    // Elements
    var form;
    var formjs = $("#kt_sign_in_form");
    var submitButton;
    var validator;

    // Handle form
    var handleForm = function(e) {
        // Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
        validator = FormValidation.formValidation(
			form,
			{
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
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
                                    form.querySelector('[name="email"]').value= "";
                                    form.querySelector('[name="password"]').value= "";

                                    //form.submit(); // submit form
                                    var redirectUrl = form.getAttribute('data-kt-redirect-url');
                                    if (redirectUrl) {
                                        location.href = redirectUrl;
                                    }

                            });
                        }else {
                            console.log();

                            if (response.data.client_token) {
                                var redirectUrlActivate = form.getAttribute('data-kt-redirect-url-activate');
                                var newUrl = redirectUrlActivate+'/'+response.data.client_token;

                                if (newUrl) {
                                    location.href = newUrl;
                                }
                            } else {
                                Swal.fire({
                                    text: response.data.message,
                                    icon: "error",
                                    buttonsStyling: false,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            }

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

                    }, 2000);
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

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#kt_sign_in_form');
            submitButton = document.querySelector('#kt_sign_in_submit');

            handleForm();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSigninGeneral.init();
});
