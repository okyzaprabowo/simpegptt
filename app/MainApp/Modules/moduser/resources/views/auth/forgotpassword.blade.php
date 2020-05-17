@extends('layouts.layout-blank')

@section('styles')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/css/pages/authentication.css') }}">
@endsection

@section('content')
    <div class="authentication-wrapper authentication-3">
        <div class="authentication-inner">

            <!-- Side container -->
            <!-- Do not display the container on extra small, small and medium screens -->
            <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5" style="background-image: url('/img/bg/21.jpg');">
                <div class="ui-bg-overlay bg-dark opacity-50"></div>

                <!-- Text -->
                <div class="w-100 text-white px-5">
                    <!-- <h1 class="display-2 font-weight-bolder mb-4">JOIN OUR<br>COMMUNITY</h1>
                    <div class="text-large font-weight-light">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vehicula ex eu gravida faucibus. Suspendisse viverra pharetra purus. Proin fringilla ac lorem at sagittis. Proin tincidunt dui et nunc ultricies dignissim.
                    </div> -->
                </div>
                <!-- /.Text -->
            </div>
            <!-- / Side container -->

            <!-- Form container -->
            <div class="d-flex col-lg-4 align-items-center bg-white p-5">
                <!-- Inner container -->
                <!-- Have to add `.d-flex` to control width via `.col-*` classes -->
                <div class="d-flex col-sm-7 col-md-5 col-lg-12 px-0 px-xl-4 mx-auto">
                    <div class="w-100">

                        <!-- Logo -->
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="ui-w-60">
                                <div class="w-100 position-relative" style="padding-bottom: 54%">
                                <img class="w-100 position-absolute" src="{{asset('assets/images/logo.gif')}}" />
                                </div>
                            </div>
                        </div>
                        <!-- / Logo -->

                        
                        <h4 class="text-center text-lighter font-weight-normal mt-5 mb-0">{{config('AppConfig.system.template.admin.title')}}</h4>
                        <br><br>
                        <h5 class="text-center text-muted font-weight-normal mb-4">{{ __('auth.forgotpassword.title') }}</h5>

                        <p>
                        {{ __('auth.forgotpassword.description') }}
                        </p>
                        @include('alert')
                        <!-- Form -->
                        <form class="my-5" action="{{route('auth.forgotPassword')}}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input name="email" type="text" class="form-control" placeholder="{{__('auth.forgotpassword.emailcaption')}}">
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">{{ __('auth.forgotpassword.button') }}</button>
                        </form>
                        <!-- / Form -->

                        <div class="w-100 py-3 px-4 px-sm-5" >
                            <div class="text-center text-muted">
                                <a href="{{route('auth.login')}}" class="d-block small">{{ __('auth.forgotpassword.back_to_login') }}</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- / Form container -->

        </div>
    </div>
@endsection