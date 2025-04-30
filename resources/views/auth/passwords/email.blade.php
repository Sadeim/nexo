@extends('frontend.layout')

@section('content')
<div class="container" style="min-height: calc(50vh);">
    <h2 class="text-center mb-4">Reset Your Password</h2>

    @if (session('status'))
    <div class="row justify-content-center">
        <div class="col-6 alert alert-success">
            {{ session('status') }}
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="row justify-content-center">
            <div class="col-6 mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary" style="width: auto;">Send Password Reset Link</button>
        </div>
    </form>
</div>
@endsection
