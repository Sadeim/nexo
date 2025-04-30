var importExcel = function () {
    var element = document.querySelector("#kt_stepper_example_basic");
    var stepper = new KTStepper(element);

    // Submit form handler
    const handleSubmit = () => {

        // Handle next button
        stepper.on('kt.stepper.next', e => {
            // e.preventDefault();
            // alert('fff')
            var $this = $(e.currentTarget);
            var formjs = $('#upload_excel_file_form');
            // var type = $this.data('kt-stepper-action');
            formjs.append('<input type="hidden" name="type" value="next" /> ');
            var action = formjs.attr('action');
            var formData = new FormData(formjs[0]);

            // submitButton.disabled = true;
            axios.post(`${action}` , formData).then(response => {
                // submitButton.disabled = false;
                if(response.data.status == 200) {
                    // console.log(response.data);
                    stepper.goNext(); // go next step
                    $('#preview').html(response.data.item.render);
                    $('#preview').show( );

                } else {
                // console.log(response.data);
                    Swal.fire({
                        text: response.data.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: done,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        })

        // Handle submit button
        $(document).on('click', '.step', e => {
            e.preventDefault();
            var $this = $(e.currentTarget);
            var formjs = $('#upload_excel_file_form');
            // var type = $this.data('kt-stepper-action');
            formjs.append('<input type="hidden" name="type" value="submit" /> ');
            var action = formjs.attr('action');
            var formData = new FormData(formjs[0]);

            // submitButton.disabled = true;
            axios.post(`${action}` , formData).then(response => {
                // submitButton.disabled = false;
                if(response.data.status == 200) {
                    Swal.fire({
                        text: response.data.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: done,
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(function (result) {
                        window.location = formjs.data("kt-redirect");
                    }).catch(err => console.log(err));
                } else {
                // console.log(response.data);
                    Swal.fire({
                        text: response.data.message,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: done,
                        customClass: {
                            confirmButton: "btn btn-danger"
                        }
                    });
                }
            });
        })
           
        stepper.on("kt.stepper.previous", function (stepper) {
            stepper.goPrevious(); // go previous step
        });
    }

    // Public methods
    return {
        init: function () {
            handleSubmit();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    importExcel.init();
});
