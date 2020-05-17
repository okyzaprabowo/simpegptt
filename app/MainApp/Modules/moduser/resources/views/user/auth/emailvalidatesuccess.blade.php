@extends('layouts.landing')

@section('content')
<div class="card">
    <div class="p-4 p-sm-5">

        <!-- Logo -->
        <!-- <div class="d-flex justify-content-center align-items-center mb-4">
            <a href="{!! config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.domain') !!}">
                <img src="{{asset('assets/images/logo.png')}}" alt="" style="width:200px; height:auto">
            </a>
        </div> -->
        <!-- / Logo -->
            <h1 class="display-4 text-center">{!! config('AppConfig.system.template.frontend.title') !!}</h1>
        <!-- Form -->
            <hr class="mt-0 mb-4">
            <h5 class="text-center font-weight-bold mb-4">Email Verification Success</h5>
            <p class="text-center">
                Email Anda berhasil diverifikasi.
            </p>            
            <p class="text-center mt-5 mb-0">
                <a href="{!! config('AppConfig.client.endpoint.'.config('AppConfig.system.mode').'.domain') !!}">Back</a>
            </p>
    </div>
</div>
@endsection