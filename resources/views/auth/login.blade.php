@extends('frontend.layout')
@section('css')
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/intlTelInput.min.css') }}">
@endsection

@section('content')
 
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2>Sign In</h2>
                </div>
                <div class="card-body">
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <input type="hidden" name="redirect_to" id="redirect_to">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-lg" name="email" id="email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Your Password</label>
                            <input type="password" class="form-control form-control-lg" name="password" id="password">
                        </div>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                        </div>
                    </form>
                    <hr>
                    <div class="text-center">
                        <span>or sign in with</span>
                    </div>
                    {{-- <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('social.redirect', 'facebook') }}" class="btn btn-outline-primary" style="width: auto;">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-danger">
                            <i class="fa-brands fa-google"></i>
                        </a>
                        <a href="{{ route('social.redirect', 'apple') }}" class="btn btn-outline-dark">
                            <i class="fa-brands fa-apple"></i>
                        </a>
                    </div> --}}
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a data-bs-toggle="modal" data-bs-target="#SignUpModal" class="text-decoration-none">Create a New Account</a>
                        <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot Password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


 


 
@endsection

@section('js')
   
@endsection
