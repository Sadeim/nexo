@extends('frontend.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/intlTelInput.min.css') }}">
@endsection

@section('content')
<section class="my-5 py-2">
    <div class="d-flex justify-content-center align-items-center flex-column text-center m-auto p-4">
        <a href="{{ route('home') }}" class="mb-3">
            <img src="{{ asset('frontend/assets/images/logo.png')}}" width="100" alt="logo">
        </a>
        <h3 class="my-1">أهلا بك </h3>
        <p class="text-Register mt-1 mb-4">
            <strong>
                مسجل بالفعل<a class="text-dark fw-bold" href="{{ route('login') }}"> سجل دخول</a>
            </strong>
        </p>
        @if (count($errors) > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }} <br>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="m_i" id="m_i">
            <div class="mb-3">
                <input type="text" name="first_name" value="{{ old('first_name') }}" class="form-control border-0 shadow-none text-end" placeholder="الاسم الأول">
                @error('first_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" name="last_name" value="{{ old('last_name') }}" class="form-control border-0 shadow-none text-end" placeholder="الاسم العائلة">
                @error('last_name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="text" name="email" value="{{ old('email') }}" class="form-control border-0 shadow-none text-end" placeholder="example@gmail.com">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3" style="direction: ltr;">
                <input type="hidden" value="" name="phone_code" id="phone_code">
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control border-0 shadow-none text-end" placeholder="رقم الموبايل">
                @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control border-0 shadow-none text-end mb-3" placeholder="كلمة المرور">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary w-100">تسجيل جديد</button>
        </form>

    </div>
</section>
@endsection

@section('js')
    <script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/intlTelInput.min.js') }}"></script>
    <script>
        var input = document.querySelector("#phone");
        window.intlTelInput(input, {
            utilsScript: "{{ asset('frontend/assets/js/utils.js') }}",
        });
        
        var iti = intlTelInput(input);
        $(document).ready(function() {
            $('#phone_code').val(iti.getSelectedCountryData().dialCode);
            input.addEventListener("countrychange", function() {
                $('#phone_code').val(iti.getSelectedCountryData().dialCode);
            });
        });
    </script>
@endsection