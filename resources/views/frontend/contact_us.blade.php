@extends('frontend.layout')
@section('css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="breatcam-section style-two d-flex align-items-center" style="background-image: url('{{ asset($about->image1) }}');">
        <div class="container">
            <div class="row d-flex align-items-center">
                <div class="col-lg-12">
                    <div class="breatcam-content">
                        <h1> Contact Us </h1>
                        <ul>
                            <li><a href="{{ route('home') }}"> <i class="fas fa-home"></i> Home</a></li>
                            <li> Contact us </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="map-section">
        <div class="container">
            <div class="row map-bg">
                <div class="col-lg-12">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3596.6194043224186!2d89.61168491495718!3d25.650754283687256!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39fd33c03fbe69cb%3A0x273671e159f8b39e!2sRDRS%20Ulipur!5e0!3m2!1sen!2sbd!4v1636872467628!5m2!1sen!2sbd"
                        width="1320" height="552" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="contact-us">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hendre-section-title padding-lg">
                        <h1>Contact <span>Form</span></h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="contact_from_box">
                        <form id="contact_form" method="POST">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-box mb-30">
                                                <input type="text" name="name" placeholder="Name:">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6">
                                            <div class="form-box mb-30">
                                                <input type="email" name="email" placeholder="Email:">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-box mb-30">
                                                <input type="text" name="subject" placeholder="Subject:">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-box">
                                                <input type="text" name="phone" placeholder="Phone Number:">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <div class="form-box mb-30">
                                        <textarea name="message" id="message" cols="30" rows="10" placeholder="Your Message"></textarea>
                                    </div>
                                </div>
                                <div class="contact-form">
                                    <button type="submit">Contact Us <i class="bi bi-arrow-right"></i></button>
                                </div>
                            </div>
                        </form>
                        <div id="status"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection 

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#contact_form').on('submit', function(e) {
                e.preventDefault();
                $('.is-invalid').removeClass('is-invalid');
                $('.error-message').remove();

                let isValid = true;
                let formData = {};

                // Validation
                $('#contact_form input, #contact_form textarea').each(function() {
                    let field = $(this);
                    let name = field.attr('name');
                    let value = $.trim(field.val());

                    if (value === '') {
                        field.addClass('is-invalid');
                        field.after(
                            '<small class="text-danger error-message">This field is required.</small>'
                            );
                        isValid = false;
                    } else {
                        formData[name] = value;
                    }
                });

                if (!isValid) return;

                $.ajax({
                    url: "{{ route('contact.submit') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
					         toastr.success(response.message);
                        $('#contact_form')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, messages) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.after(
                                    '<small class="text-danger error-message">' +
                                    messages[0] + '</small>');
                            });
                        }
                    }
                });
            });
        });
    </script>


@endsection
