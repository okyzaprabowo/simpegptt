@extends('layouts.layout-horizontal-sidenav')
@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <style>        
        .user-edit-fileinput {
            position: absolute;
            visibility: hidden;
            width: 1px;
            height: 1px;
            opacity: 0;
        }
    </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies --> 
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <script>        
    var apiPath = "{{config('AppConfig.endpoint.api.Pegawai')}}";
        var vueFormPegawai = new Vue({
            el: '#vueFormPegawai',
            data: {
                instansiList: {!!json_encode($instansiList)!!},
                instansi:[],
                instansiIds: {!!json_encode($instansiIds)!!},
                isEditable: true,
                instansi_id: 0
            },
            created: function() {
                this.renderInstnasi();
            },
            mounted: function() {                              
                // $('.datepicker-base').datepicker({format: 'yyyy-mm-dd',autoclose: true});

                $('.instansi_id').val({{isset($data['profile'])?$data['profile']['instansi_id']:0}});            
                $('.select2-select').select2();
                // $('.datepicker-keluarga').datepicker({format: 'yyyy-mm-dd',container: '#formPegawaiKeluargaModal'});
                // $('.datepicker-pendidikan').datepicker({format: 'yyyy-mm-dd',container: '#formPegawaiPendidikanModal'});
            },
            watch: {
                // instansiIds: function(v,o) {
                //     console.log('change',v,o);
                // },
            },
            methods: {
                getInstansiList(instansiId) {
                    var searchKeys = ['induk'];
                    const filtered = this.instansiList.data.filter(d => {
                        return Object.keys(d)
                            .filter(k => searchKeys.includes(k))
                            .map(k => String(d[k]))[0] == instansiId;
                    });
                    return filtered;
                },
                getInstansi(instansiId) {
                    var searchKeys = ['id'];
                    const filtered = this.instansiList.data.filter(d => {
                        return Object.keys(d)
                            .filter(k => searchKeys.includes(k))
                            .map(k => String(d[k]))[0] == instansiId;
                    });
                    return filtered[0];
                },
                renderInstnasi() {
                    _.forEach(this.instansiIds,(v,k)=>{
                        if(v!=this.instansi_id){
                            var tmpInstansiList = this.getInstansiList(v);
                            if(tmpInstansiList.length!=0)
                                this.instansi.push(tmpInstansiList);
                        }
                    });
                    if(this.instansi.length==0){
                        var tmpInstansiList = this.getInstansiList(1);
                        this.instansi.push(tmpInstansiList);
                    }
                    console.log(this.instansi);
                },
                changeInstansi(key) {
                    this.instansi.splice(key+1);
                    this.instansiIds.splice(key+2);
                    if(this.instansiIds[key+1]!=undefined){
                        var tmpInstansiList = this.getInstansiList(this.instansiIds[key+1]);
                        if(tmpInstansiList.length>0)
                            this.instansi.push(tmpInstansiList);
                    }else{
                        this.instansiIds.splice(key+1);
                    }
                    this.instansi_id = this.instansiIds[this.instansiIds.length-1];
                    $('#form-instansi-id').val(this.instansi_id);
                }
            }
        });
        $(document).ready(function(){
            // $('.instansi_id').val({{isset($data['profile'])?$data['profile']['instansi_id']:''}});            
            // $('.select2-select').select2();
        });
    </script>
@endsection

@section('content')
<?php 
$isPegawai = false;
$isSetSatker = false;
if(isset($data['id'])){
    $isSetSatker = \Facades\App\MainApp\Modules\moduser\Repositories\UserRepo::isHasRole($data['id'],'pejabat_approval') 
                    || \Facades\App\MainApp\Modules\moduser\Repositories\UserRepo::isHasRole($data['id'],'admin_satker')
                    || \Facades\App\MainApp\Modules\moduser\Repositories\UserRepo::isHasRole($data['id'],'pimpinan2')
                    || \Facades\App\MainApp\Modules\moduser\Repositories\UserRepo::isHasRole($data['id'],'pimpinan3');
    $isPegawai = \Facades\App\MainApp\Modules\moduser\Repositories\UserRepo::isHasRole($data['id'],'pegawai_ptt');
}
$viewMode = $mode=='view'?true:false;
?>
<div id="vueFormPegawai">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">Setup / <a href="{{route('user.list')}}">Managemen User</a> /</span> {{$viewMode?('View User '.(isset($data['name'])?$data['name']:'')):'User Form'}}</div>
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
                <div style="width: 100%;">                
                    <form action="{{$mode=='add'?route('user.create'):route('user.update',['id'=>$data['id']])}}" method="post">
                        @if($mode=='edit')
                        <input type="hidden" name="_method" value="PUT">
                        @endif
                        @csrf
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Nama</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="{{ isset($data['name'])?$data['name']:'' }}" {{$isPegawai||$viewMode?'disabled':''}}>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Email</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="email" value="{{ isset($data['email'])?$data['email']:'' }}" {{$isPegawai||$viewMode?'disabled':''}}>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Username</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="username" value="{{ isset($data['username'])?$data['username']:'' }}" {{$isPegawai||$viewMode?'disabled':''}}>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Role</label>
                            <div class="col-sm-8">
                                <?php /*
                                <select name="role_code" class="form-control">
                                    @foreach($roles as $role)
                                    <option value="{{$role['role_code']}}" {{ isset($role_code) && $role_code == $role['role_code']?'selected="true"':'' }}>{{'['.$role['role_code'].'] '.$role['name']}}</option>
                                    @endforeach
                                </select>*/ ?>
                                
                                @foreach($roles as $role)
                                @if($role['role_code']=='pegawai_ptt')
                                    @if($isPegawai||$viewMode)
                                    <label class="custom-control custom-checkbox">
                                        <input name="role_code[]" type="checkbox" class="custom-control-input" value="{{$role['role_code']}}" checked readonly="true" disabled="true">
                                        <span class="custom-control-label">{{'['.$role['role_code'].'] '.$role['name']}}</span>
                                    </label>
                                    <input name="role_code[]" type="hidden" value="{{$role['role_code']}}">
                                    @else
                                    @endif
                                @else
                                <label class="custom-control custom-checkbox">
                                    <input name="role_code[]" type="checkbox" class="custom-control-input" value="{{$role['role_code']}}" {{ isset($data['role']) && strpos($data['role'],';'.$role['role_code'].';')!==false?'checked':'' }} {{$viewMode?'disabled':''}}>
                                    <span class="custom-control-label">{{'['.$role['role_code'].'] '.$role['name']}}</span>
                                </label>
                                @endif
                                @endforeach
                            </div>
                        </div>

                        @if($isSetSatker)
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Unit Kerja</label>
                            <div class="col-sm-8">                                        
                                <?php /* <select class="select2-select instansi_id" name="profile[instansi_id]" data-allow-clear="true">
                                    @include('instansi.selectnested',['data'=>$instansi['data'],'induk'=>0,'maxEselon'=>4])
                                </select> */ ?>
                                
                                <input type="hidden" id="form-instansi-id" name="profile[instansi_id]" value="{{ isset($data['profile']['instansi_id'])?$data['profile']['instansi_id']:0 }}">    
                                <template v-for="(instansiItem, key) in instansi">
                                    <select @change="changeInstansi(key)" class="form-control mb-2" v-model="instansiIds[key+1]" {{$isPegawai||$viewMode?'disabled':''}}>
                                        <template v-for="(val) in instansiItem">
                                            <option :value="val.id">@{{val.nama}}</option>                                            
                                        </template>
                                    </select>
                                </template>
                            </div>
                        </div>
                        @endif
                        <hr>
                        @if(!$viewMode)
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="password" {{$isPegawai?'disabled':''}}>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right">Password Confirmation</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="password_confirmation" {{$isPegawai?'disabled':''}}>
                            </div>
                        </div>
                        
                        <hr>

                        <div class="form-group row">
                            <label class="col-form-label col-sm-2 text-sm-right"></label>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        @endif
                    </form>
            </div>

        </div>
    </div>
</div>
@endsection