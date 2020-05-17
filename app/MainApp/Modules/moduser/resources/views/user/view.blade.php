@extends('layouts.layout-horizontal-sidenav')
@section('content')
<div id="vueFormPegawai">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">View User /</span> {{ isset($data['name'])?$data['name']:'' }}</div>
    </h4>
    @include("alert")
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-row">

                <div class="pr-3">
                    <div style="background: #AAAAAA; width: 100px; height: 120px; text-align: center; padding: 20px;">
                        <i class="ion ion-ios-person" style="font-size: 50px;"></i>
                    </div>
                </div>

                <div style="width: 100%;">                
                    
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Nama</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ isset($data['name'])?$data['name']:'' }}" disabled="true">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Email</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ isset($data['email'])?$data['email']:'' }}" disabled="true">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Username</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ isset($data['username'])?$data['username']:'' }}" disabled="true">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 text-sm-right">Role</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ $data['roles']&&isset($data['roles'][0])?$data['roles'][0]['role']['name']:'' }}" disabled="true">
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection