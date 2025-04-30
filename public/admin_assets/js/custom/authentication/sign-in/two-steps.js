"use strict";

// Class Definition
var KTSigninTwoSteps = function() {
    // Elements
    var form;
    var formjs = $("#kt_sing_in_two_steps_form");
    var submitButton;
    var resendButton;

    // Handle form
    var handleForm = function(e) {
        // Handle form submit
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            var validated = true;

            var inputs = [].slice.call(form.querySelectorAll('input[maxlength="1"]'));
            inputs.map(function (input) {
                if (input.value === '' || input.value.length === 0) {
                    validated = false;
                }
            });

            if (validated === true) {

                // Show loading indication
                submitButton.setAttribute('data-kt-indicator', 'on');

                // Disable button to avoid multiple click
                submitButton.disabled = true;

                Swal.fire({
                    didOpen: () => {
                        Swal.showLoading()
                    },
                    title: please_wait,
                    showConfirmButton: false,
                })

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

                                //form.submit(); // submit form
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

                }, 1000);
            } else {
                swal.fire({
                    text: some_errors,
                    icon: "error",
                    buttonsStyling: false,
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    KTUtil.scrollTop();
                });
            }
        });
        resendButton.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                didOpen: () => {
                    Swal.showLoading()
                },
                title: please_wait,
                showConfirmButton: false,
            })
                let target_url = e.target.getAttribute('data-action');
                console.log(target_url)
                let remember_token = document.querySelector('input[name="remember_token"]').value;
                console.log(remember_token);
                axios.post(`${target_url}` , {remember_token:remember_token}).then(response => {
                    console.log(response.data);
                    if(response.data.status) {

                        // Show message popup. For more info check the plugin's official documentation: https://sweetalert2.github.io/
                        Swal.fire({
                            text: response.data.message,
                            icon: "success",
                            buttonsStyling: false,
                            showConfirmButton: false,
                            timer: 1500
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

                }, 1000);
        });
    }

    var handleType = function() {
        var input1 = form.querySelector("[name=code_1]");
        var input2 = form.querySelector("[name=code_2]");
        var input3 = form.querySelector("[name=code_3]");
        var input4 = form.querySelector("[name=code_4]");
        var input5 = form.querySelector("[name=code_5]");
        var input6 = form.querySelector("[name=code_6]");

        input1.focus();

        input1.addEventListener("keyup", function() {
            if (this.value.length === 1) {
                input2.focus();
            }
        });

        input2.addEventListener("keyup", function() {
            if (this.value.length === 1) {
                input3.focus();
            }
        });

        input3.addEventListener("keyup", function() {
            if (this.value.length === 1) {
                input4.focus();
            }
        });

        input4.addEventListener("keyup", function() {
            if (this.value.length === 1) {
                input5.focus();
            }
        });

        input5.addEventListener("keyup", function() {
            if (this.value.length === 1) {
                input6.focus();
            }
        });

        input6.addEventListener("keyup", function() {
            if (this.value.length === 1) {
                input6.blur();
            }
        });
    }

    // Public functions
    return {
        // Initialization
        init: function() {
            form = document.querySelector('#kt_sing_in_two_steps_form');
            submitButton = document.querySelector('#kt_sing_in_two_steps_submit');
            resendButton = document.querySelector('#resend_btn');

            handleForm();
            handleType();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTSigninTwoSteps.init();
});
