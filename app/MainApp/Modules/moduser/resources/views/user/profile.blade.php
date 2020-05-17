@extends('layouts.layout-horizontal-sidenav')
@section('content')
<div id="vueFormPegawai">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div>User Profile</div>
    </h4>
    @include("alert")
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-row">
                <!-- <div class="pr-3">
                    <div style="background: #AAAAAA; width: 100px; height: 120px; text-align: center; padding: 20px;">
                        <i class="ion ion-ios-person" style="font-size: 50px;"></i>
                    </div>
                </div> -->
                <div>
                    <h4 class="font-weight-bold mb-2">{{ \UserAuth::user('name') }}</h4>
                </div>
            </div>
        </div>
        <div class="card-body pb-2">        
            <form action="{{route('user.profile')}}" method="post">
                <input type="hidden" name="_method" value="PUT">
                @csrf

                <div class="form-group row">
                    <label class="col-form-label col-sm-2 text-sm-right">Nama</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="name" value="{{ \UserAuth::user('name') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-sm-2 text-sm-right">Email</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="email" value="{{ \UserAuth::user('email') }}">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-sm-2 text-sm-right">Username</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="username" value="{{ \UserAuth::user('username') }}">
                    </div>
                </div>

                <hr>

                <div class="form-group row">
                    <label class="col-form-label col-sm-2 text-sm-right">Passowrd</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" name="password" v-model="passwordForm.password">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-sm-2 text-sm-right">Passowrd Confirmation</label>
                    <div class="col-sm-8">
                        <input type="password" class="form-control" name="password_confirmation" v-model="passwordForm.password_confirmation">
                    </div>
                </div>
                
                <hr>
                
                <div class="form-group row">
                    <label class="col-form-label col-sm-2 text-sm-right"></label>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection