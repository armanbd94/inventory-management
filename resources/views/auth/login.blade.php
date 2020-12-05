<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from uiwebsoft.com/justlog/login-nine/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 29 Aug 2020 00:36:10 GMT -->
<head>
    <base href="{{ asset('/') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>JustLog - Login</title>

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="32x32" href="images/fev.png">
 
    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="css/login.css">

</head>
<body>

<!-- Loader -->
<div class="loader"><div class="loader_div"></div></div>

<!-- Login page -->
<div class="login_wrapper">
    <div class="row no-gutters">

        <div class="col-md-6 mobile-hidden">
            <div class="login_left">
                <div class="login_left_img"><img src="images/login-bg.jpg" alt="login background"></div>
            </div>
        </div>
        <div class="col-md-6 bg-white">
            <div class="login_box">
                     <a href="#" class="logo_text">
                            <span>JL</span> Just Log
                        </a>
                    <div class="login_form">
                        <div class="login_form_inner">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="input-text  @error('email') is-invalid @enderror" placeholder="Email Address"  value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    <i class="fas fa-envelope icon"></i>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="input-text @error('password') is-invalid @enderror" placeholder="Password"  required autocomplete="current-password">
                                    <i class="fas fa-lock icon"></i>
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                                <div class="checkbox clearfix">
                                    <div class="form-check checkbox-theme">
                                        <input class="form-check-input" type="checkbox" name="remember" value="" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}">Forgot Password</a>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn-md btn-theme btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
                                </div>
                            </form>
                        </div>
                    </div>
            </div>
        </div>
    </div>

</div>
<!-- /. Login page -->


<!-- External JS libraries -->
<script src="js/login.js"></script>

<!-- Custom JS Script -->
<script type="text/javascript">

	var $window = $(window);

        // :: Preloader Active Code
        $window.on('load', function () {
            $('.loader').fadeOut('slow', function () {
                $(this).remove();
            });
        });
</script>

</body>

<!-- Mirrored from uiwebsoft.com/justlog/login-nine/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 29 Aug 2020 00:36:12 GMT -->
</html>