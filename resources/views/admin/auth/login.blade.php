<!DOCTYPE html>
<html lang="en" data-theme-mode="light" dir="ltr" direction="ltr" style="direction:ltr;">
	<!--begin::Head-->
	<head>
		<meta charset="utf-8" />
		<title>Hrs</title>
		<meta name="description" content="Login page example" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;500&display=swap" rel="stylesheet">
		<!--end::Fonts-->
		<!--begin::Page Custom Styles(used by this page)-->
		<link href="{{asset('admin_assets/css/login-2.css')}}" rel="stylesheet" type="text/css" />
		<!--end::Page Custom Styles-->
		<!--begin::Global Theme Styles(used by all pages)-->
		<link href="{{asset('admin_assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('admin_assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('admin_assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" class="quick-panel-right demo-panel-right offcanvas-right header-fixed header-mobile-fixed subheader-enabled aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-2 login-signin-on d-flex flex-column flex-column-fluid bg-white position-relative overflow-hidden" id="kt_login">
                <!--begin::Body-->
                <div class="login-body d-flex flex-column-fluid align-items-stretch justify-content-center">
                    <div class="container row justify-content-center">
                        <div class="col-lg-6 d-flex align-items-center">
                            <!--begin::Signin-->
                            <div class="login-form login-signin">
                                @if (count($errors) > 0)
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="alert alert-danger alert-dismissible">
                                                @foreach ($errors->all() as $error)
                                                    {{ $error }} <br>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <!--begin::Form-->
                                <form method="POST" action="{{ route('admin.login.post') }}" class="form w-xxl-550px rounded-lg p-20" >
                                    @csrf
                                    <!--begin::Title-->
                                    <div class="pb-13 pt-lg-0 pt-5">
                                        <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg text-center">Admin Login Page</h3>
                                    </div>
                                    <!--begin::Title-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between mt-n5">
                                            <label class="font-size-h6 font-weight-bolder text-dark">Email</label>
                                        </div>
                                        <input class="form-control" type="email" placeholder="Email" value="{{ old('email') }}" name="email" autocomplete="off" />
                                        @error('email')
                                            <span  class="text-danger"  role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group mt-4">
                                        <div class="d-flex justify-content-between mt-n5">
                                            <label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
                                            {{-- <a href="" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5" id="kt_login_forgot">Forgot Password?</a> --}}
                                        </div>
                                        <input class="form-control" type="password" name="password" autocomplete="off" />
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <div class="checkbox-inline mt-4">
                                            <label class="checkbox">
                                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                                                <span></span>Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Action-->
                                    <div class="pb-lg-0 pb-5">
                                        <button type="submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3" style="width: 100%;">Login</button>
                                    </div>
                                    <!--end::Action-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Signin-->
                            <!--begin::Forgot-->
                            <div class="login-form login-forgot">
                                <!--begin::Form-->
                                <form class="form w-xxl-550px rounded-lg p-20" novalidate="novalidate" id="kt_login_forgot_form">
                                    <!--begin::Title-->
                                    <div class="pb-13 pt-lg-0 pt-5">
                                        <h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Forgot Password?</h3>
                                        {{-- <p class="text-muted font-weight-bold font-size-h4">Enter your email to reset your password</p> --}}
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <input class="form-control form-control-solid h-auto p-6 rounded-lg font-size-h6" type="email" placeholder="Email" name="email" autocomplete="off" />
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group d-flex flex-wrap pb-lg-0">
                                        <button type="button" id="kt_login_forgot_submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4">Submit</button>
                                        {{-- <button type="button" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3">Cancel</button> --}}
                                    </div>
                                    <!--end::Form group-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Forgot-->
                        </div>
                    </div>
                </div>
                <!--end::Body-->
                
				<!--begin::Footer-->
				<div class="login-footer py-10 flex-column-auto">
					<div class="container d-flex flex-column flex-md-row align-items-center justify-content-center justify-content-md-between">
						<div class="font-size-h6 font-weight-bolder order-2 order-md-1 py-2 py-md-0">
							<span class="text-muted font-weight-bold mr-2">2025©</span>
							<a href="" target="_blank" class="text-dark-50 text-hover-primary">Hrs</a></a>
						</div>
						<div class="font-size-h5 font-weight-bolder order-1 order-md-2 py-2 py-md-0">
							{{-- <a href="#" class="text-primary">Terms</a>
							<a href="#" class="text-primary ml-10">Plans</a> --}}
							{{-- <a href="#" class="text-primary ml-10">Contact Us</a> --}}
						</div>
					</div>
				</div>
				<!--end::Footer-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
		{{-- <script>var HOST_URL = "https://preview.keenthemes.com/keen/theme/tools/preview";</script> --}}
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3E97FF", "secondary": "#E5EAEE", "success": "#08D1AD", "info": "#844AFF", "warning": "#F5CE01", "danger": "#FF3D60", "light": "#E4E6EF", "dark": "#181C32" }, "light": { "white": "#ffffff", "primary": "#DEEDFF", "secondary": "#EBEDF3", "success": "#D6FBF4", "info": "#6125E1", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#3F4254", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#EBEDF3", "gray-300": "#E4E6EF", "gray-400": "#D1D3E0", "gray-500": "#B5B5C3", "gray-600": "#7E8299", "gray-700": "#5E6278", "gray-800": "#3F4254", "gray-900": "#181C32" } }, "font-family": "Poppins" };</script>
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{ asset('admin_assets/plugins/global/plugins.bundle.js')}}"></script>
		<script src="{{ asset('admin_assets/plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
		<script src="{{ asset('admin_assets/js/scripts.bundle.js')}}"></script>
		<!--end::Global Theme Bundle-->
		<!--begin::Page Scripts(used by this page)-->
		<script src="{{ asset('admin_assets/js/custom/pages/login/login.js')}}"></script>
		<!--end::Page Scripts-->
	</body>
	<!--end::Body-->
</html>