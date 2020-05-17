@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
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
<?php 
$isSatker = \UserAuth::is('admin_satker')?true:false; 
$isPegawaiPtt = \UserAuth::is('pegawai_ptt')?true:false; 
?>
@section('scripts')
@parent
    <!-- Dependencies -->
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>    
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>

    <script>
        var apiPath = "{{config('AppConfig.endpoint.api.Pegawai')}}";
        var vueFormPegawai = new Vue({
            el: '#vueFormPegawai',
            data: {
                formTitle: "Tambah Baru",
                
                mode: "{{$mode}}",
 
                data: {!!json_encode($data)!!},
                instansiList: {!!json_encode($instansiList)!!},
                instansi:[],
                instansiIds: {!!json_encode($instansiIds)!!},
                isEditable: true,
                isProfile: false,
                isSatker: {{$isSatker?'true':'false'}},
                isPegawaiPtt: {{$isPegawaiPtt?'true':'false'}},
                
                isAdd: true,
                fotoPreview: '',
                form: {},
                formEmpty: {
                    id:0,
                    user_id: 0,
                    kode: '',
                    nama: '',
                    instansi_id: 0,
                    jabatan_id: 0,
                    gelar_depan: '',
                    gelar_belakang: '',
                    ktp: '',
                    npwp: '',
                    tanggal_lahir: '',
                    tempat_lahir: '',
                    agama_id: 0,
                    kelamin: 1,
                    golongan_darah: '',
                    tipe: '',
                    status_kawin_id: 0,
                    foto: '',
                    pendidikan: [],
                    alamat: [],
                    keluarga: [],
                    doktah: [],
                    user: {
                        username: '',
                        password: '',
                        password_confirmation: '',
                    }
                },

                //-------------------------------------------------------                
                formAlamat: {
                    id: 0,
                    pegawai_id: 0,
                    tipe_alamat: 1,
                    alamat: '',
                    kelurahan: '',
                    kecamatan: '',
                    kota: '',
                    provinsi: '',
                    kodepos: '',
                    telepon: '',
                    ponsel: '',
                    email: '',
                    emer_nama: '',
                    emer_pekerjaan: '',
                    emer_relasi: ''
                },
                formAlamatEmpty: {
                    id: 0,
                    pegawai_id: 0,
                    tipe_alamat: 1,
                    alamat: '',
                    kelurahan: '',
                    kecamatan: '',
                    kota: '',
                    provinsi: '',
                    kodepos: '',
                    telepon: '',
                    ponsel: '',
                    email: '',
                    emer_nama: '',
                    emer_pekerjaan: '',
                    emer_relasi: ''
                },
                //-------------------------------------------------------
                
                formKeluarga: {
                    id: 0,
                    pegawai_id: 0,
                    nama: '',
                    kelamin: 0,
                    tanggal_lahir: null,
                    tempat_lahir: '',
                    relasi: ''
                },
                formKeluargaEmpty: {
                    id: 0,
                    pegawai_id: 0,
                    nama: '',
                    kelamin: 0,
                    tanggal_lahir: null,
                    tempat_lahir: '',
                    relasi: ''
                },

                //-------------------------------------------------------
                
                formPendidikan: {
                    id: 0,
                    pegawai_id: 0,
                    nama_sekolah: '',
                    is_formal: 1,
                    tingkat: '',
                    tanggal_masuk: null,
                    tanggal_lulus: ''
                },
                formPendidikanEmpty: {
                    id: 0,
                    pegawai_id: 0,
                    nama_sekolah: '',
                    is_formal: 1,
                    tingkat: '',
                    tanggal_masuk: null,
                    tanggal_lulus: ''
                },

                //-------------------------------------------------------

                formDoktah: {
                    index: 0,
                    id: 0,
                    pegawai_id: 0,
                    nama: '',
                    keterangan: '',
                    filename: null,
                    filepath: ''
                },
                formDoktahEmpty: {
                    index: 0,
                    id: 0,
                    pegawai_id: 0,
                    nama: '',
                    keterangan: '',
                    filename: null,
                    filepath: null
                }
            },
            created: function() {
                if(this.mode=='add'){
                    this.isAdd = true;
                    this.form = JSON.parse(JSON.stringify(this.formEmpty));
                }else{
                    this.isAdd = false;
                    this.data.user.password = "";
                    this.data.user.password_confirmation = "";
                    this.form = JSON.parse(JSON.stringify(this.data));
                    if(this.form.foto_url) this.fotoPreview = '{{\Storage::url('/')}}' + this.form.foto;                    
                }        

                if(this.mode=='profile' || this.mode=='view')this.isEditable = false;
                if(this.mode=='profile')this.isProfile = true;

                this.renderInstnasi();
            },
            mounted: function() {                   
                $('.select2-select').select2();                
                $('.datepicker-base').datepicker({format: 'yyyy-mm-dd',autoclose: true});

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
                        if(v!=this.form.instansi_id){
                            var tmpInstansiList = this.getInstansiList(v);
                            if(tmpInstansiList.length!=0)
                                this.instansi.push(tmpInstansiList);
                        }
                    });
                    if(this.instansi.length==0){
                        var tmpInstansiList = this.getInstansiList(1);
                        this.instansi.push(tmpInstansiList);
                    }
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
                    this.form.instansi_id = this.instansiIds[this.instansiIds.length-1];
                },
                submitData(ev) {  
                    ev.preventDefault();  
                    if(this.form.user.password && this.form.user.password != this.form.user.password_confirmation){
                        showAlert({
                            text: "Konfirmasi password tidak sama, silahkan perbaiki",
                            type: "warning"
                        });
                        return false;
                    }
                    var formData = new FormData();   
                    _.forEach(this.form,(v,k)=>{
                        if(v instanceof Object && !(v instanceof String)){
                            if(v instanceof File){
                                formData.append(k, v);                                
                            }else{
                                _.forEach(v,(v2,k2)=>{
                                    if(v2 instanceof Object && !(v2 instanceof String)){
                                        if(v2 instanceof File){
                                            formData.append(k+'['+k2+']', v2);                                
                                        }else{
                                            _.forEach(v2,(v3,k3)=>{   

                                                if(v3 instanceof Object && !(v3 instanceof String)){
                                                    if(v3 instanceof File){

                                                        formData.append(k+'['+k2+']'+'['+k3+']', v3);                                
                                                    }else{
                                                        
                                                        _.forEach(v3,(v4,k4)=>{
                                                            if(v4!=null)
                                                                formData.append(k+'['+k2+']'+'['+k3+']'+'['+k4+']', v4);
                                                        });
                                                    }
                                                }else{
                                                    if(v3!=null)
                                                        formData.append(k+'['+k2+']'+'['+k3+']', v3);
                                                }
                                            });
                                        }
                                    }else{
                                        if(v2!=null)
                                            formData.append(k+'['+k2+']', v2);
                                    }
                                    
                                });

                            }
                        }else{
                            if(v!=null)
                                formData.append(k, v);                            
                        }
                    });         
                    
                    if (this.isAdd) {
                        this.saveData(formData);
                    } else {
                        formData.append('_method','PUT');
                        this.updateData(this.form.id, formData);
                    }
                },
                saveData(data) {  
                    var that = this;                
                    axios.post("{{route('pegawai.api.create')}}", data,
                        {
                            headers: {        
                                'Content-Type': 'multipart/form-data'        
                            }
                        })
                        .then((res) => {
                            // that.form = res.data.data;
                            showAlert({
                                text: "Data berhasil disimpan",
                                type: "info",
                                title: "Info"
                            });   
                            window.location.href = "{{route('pegawai.list')}}";                            
                        }).catch((err) => {
                            // let err = localApiErrorParse(res.response);
                            showAlert({
                                text: "Simpan data gagal :<br> " + err.message,
                                type: "warning"
                            });
                        });

                },
                updateData(id, data) {  
                    var that = this; 
                    
                    axios.post(("{{route('pegawai.api.update',['id'=>'PEGAWAI_ID'])}}").replace('PEGAWAI_ID',id), data,
                        {
                            headers: {        
                                'Content-Type': 'multipart/form-data'        
                            }
                        })
                        .then((res) => {
                            // that.form = res.data.data;
                            showAlert({
                                text: "Data berhasil disimpan",
                                type: "info",
                                title: "Info"
                            });        
                            
                            if(this.mode=='profile'){
                                window.location.href = "{{route('dashboard')}}";
                            }                  
                            
                        }).catch((err) => {
                            // let err = localApiErrorParse(res.response);
                            showAlert({
                                text: "Update gagal : " + err.message,
                                type: "warning"
                            });
                        });

                },
                //-----------
                handleFotoUpload(ev) {
                    this.form.foto = ev.target.files[0];
                    this.fotoPreview = '';
                    this.fotoPreview = URL.createObjectURL(ev.target.files[0]);
                },
                resetFoto(ev) {
                    this.fotoPreview = '';
                    this.form.foto = null;
                },
                //--Alamat-------------------------------------------------------
                alamatShowForm(isAdd = true, i = 0) {
                    if (!isAdd) {
                        this.formAlamat = JSON.parse(JSON.stringify(this.form.alamat[i]));
                        this.formAlamat.isAdd = false;                        
                    } else {
                        this.formAlamat = JSON.parse(JSON.stringify(this.formAlamatEmpty));
                        this.formAlamat.isAdd = true;
                    }
                    this.formAlamat.index = i;
                    $('#formPegawaiAlamatModal').modal('show');
                },
                alamatFormSubmitted(ev) {
                    ev.preventDefault();
                    if(!this.formAlamat.alamat){
                        showAlert({
                                text: "Alamat harus diisi",
                                type: "warning"
                            });
                        return false;
                    }
                    if(!this.formAlamat.tipe_alamat==3 && !this.formAlamat.emer_nama){
                        showAlert({
                                text: "Nama harus diisi",
                                type: "warning"
                            });
                        return false;
                    }
                    if (this.formAlamat.isAdd) {
                        this.form.alamat.push(JSON.parse(JSON.stringify(this.formAlamat)));
                    } else {
                        this.form.alamat[this.formAlamat.index] = JSON.parse(JSON.stringify(this.formAlamat));
                        this.form = JSON.parse(JSON.stringify(this.form));
                    }
                    $('#formPegawaiAlamatModal').modal('hide');
                },
                alamatDeleteItem(i) {
                    showAlert({
                        styleType: "modal",
                        style: "warning",
                        title: "Konfirmasi Penghapusan",
                        text: "Apakah anda yakin ?",
                        modalButtonCancel: "Batal",
                        modalButtonOk: "Ya",
                        onOk: () => {
                            this.form.alamat.splice(i,1);
                        }
                    });
                },
                //--keluarga-------------------------------------------------------
                keluargaShowForm(isAdd = true, i = 0) {
                    if (!isAdd) {
                        this.formKeluarga = JSON.parse(JSON.stringify(this.form.keluarga[i]));
                        this.formKeluarga.isAdd = false;                        
                    } else {
                        this.formKeluarga = JSON.parse(JSON.stringify(this.formKeluargaEmpty));
                        this.formKeluarga.isAdd = true;
                    }
                    this.formKeluarga.index = i;
                    $('#formPegawaiKeluargaModal').modal('show');
                    
                    $('#keluarga-tanggal-lahir')
                        .datepicker({format: 'yyyy-mm-dd',container: '#formPegawaiKeluargaModal',autoclose: true})
                        .datepicker('setDate', this.formKeluarga.tanggal_lahir);
                },
                keluargaFormSubmitted(ev) {
                    ev.preventDefault();
                    if (this.formKeluarga.isAdd) {
                        this.form.keluarga.push(JSON.parse(JSON.stringify(this.formKeluarga)));
                    } else {
                        this.form.keluarga[this.formKeluarga.index] = JSON.parse(JSON.stringify(this.formKeluarga));
                        this.form = JSON.parse(JSON.stringify(this.form));
                    }
                    $('#formPegawaiKeluargaModal').modal('hide');
                },
                keluargaDeleteItem(i) {
                    showAlert({
                        styleType: "modal",
                        style: "warning",
                        title: "Konfirmasi Penghapusan",
                        text: "Apakah anda yakin ?",
                        modalButtonCancel: "Batal",
                        modalButtonOk: "Ya",
                        onOk: () => {
                            this.form.keluarga.splice(i,1);
                        }
                    });
                },
                //--Pendidikan-------------------------------------------------------
                pendidikanShowForm(isAdd = true, i = 0) {
                    if (!isAdd) {
                        this.formPendidikan = JSON.parse(JSON.stringify(this.form.pendidikan[i]));
                        this.formPendidikan.isAdd = false;                        
                    } else {
                        this.formPendidikan = JSON.parse(JSON.stringify(this.formPendidikanEmpty));
                        this.formPendidikan.isAdd = true;
                    }
                    this.formPendidikan.index = i;

                    $('#formPegawaiPendidikanModal').modal('show');
                    
                    $('#pendidikan-tanggal-masuk')
                        .datepicker({format: 'yyyy-mm-dd',container: '#formPegawaiPendidikanModal',autoclose: true})
                        .datepicker('setDate', this.formPendidikan.tanggal_masuk);
                    $('#pendidikan-tanggal-lulus')
                        .datepicker({format: 'yyyy-mm-dd',container: '#formPegawaiPendidikanModal',autoclose: true})
                        .datepicker('setDate', this.formPendidikan.tanggal_lulus);
                    
                },
                pendidikanFormSubmitted(ev) {
                    ev.preventDefault();
                    console.log(this.formPendidikan);
                    if (this.formPendidikan.isAdd) {
                        this.form.pendidikan.push(JSON.parse(JSON.stringify(this.formPendidikan)));
                    } else {
                        this.form.pendidikan[this.formPendidikan.index] = JSON.parse(JSON.stringify(this.formPendidikan));
                        this.form = JSON.parse(JSON.stringify(this.form));
                    }
                    $('#formPegawaiPendidikanModal').modal('hide');
                },
                pendidikanDeleteItem(i) {
                    showAlert({
                        styleType: "modal",
                        style: "warning",
                        title: "Konfirmasi Penghapusan",
                        text: "Apakah anda yakin ?",
                        modalButtonCancel: "Batal",
                        modalButtonOk: "Ya",
                        onOk: () => {
                            this.form.pendidikan.splice(i,1);
                        }
                    });
                },
                //--doktah-------------------------------------------------------
                doktahShowForm(isAdd = true, i = 0) {
                    $('.inputFileDoktah').val('');
                    this.formDoktah.index = i;
                    if (!isAdd) {
                        this.formDoktah = JSON.parse(JSON.stringify(this.form.doktah[i]));
                        this.formDoktah.isAdd = false;                        
                    } else {
                        this.formDoktah = JSON.parse(JSON.stringify(this.formDoktahEmpty));
                        this.formDoktah.isAdd = true;
                    }
                    this.formDoktah.index = i;
                    $('#formPegawaiDoktahModal').modal('show');
                },
                doktahFormSubmitted(ev) {
                    ev.preventDefault();  

                    if (this.formDoktah.isAdd) {
                        this.form.doktah.push(this.formDoktah);
                    } else {
                        this.form.doktah.splice(this.formDoktah.index,1,this.formDoktah);
                    }
                    $('#formPegawaiDoktahModal').modal('hide');
                },
                doktahDeleteItem(i) {
                    showAlert({
                        styleType: "modal",
                        style: "warning",
                        title: "Konfirmasi Penghapusan",
                        text: "Apakah anda yakin ?",
                        modalButtonCancel: "Batal",
                        modalButtonOk: "Ya",
                        onOk: () => {
                            this.form.doktah.splice(i,1);
                        }
                    });
                },
                handleDoktahUpload(ev) {
                    this.formDoktah.filename = ev.target.files[0].name;
                    this.formDoktah.filepath = ev.target.files[0];
                }
            }
        });

        $(document).ready(function(){
            
        });
    </script>
@endsection

@section('content')
<div id="vueFormPegawai">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        @if($mode=='profile')
        <div>Data Pribadi</div>
        @elseif($mode=='view')
        <div><span class="text-muted font-weight-light"><a href="{{route('pegawai.list')}}">Kepegawaian</a> /</span> View Pegawai</div>
        @else
        <div><span class="text-muted font-weight-light"><a href="{{route('pegawai.list')}}">Kepegawaian</a> /</span> Form Pegawai</div>
        @endif
    </h4>
    @include("alert")
    <div class="card">

        <div class="card-body">
            <div class="d-flex flex-row">
                <div class="pr-2">
                    <div class="media align-items-center">                        
                        <template v-if="fotoPreview">
                            <img :src="fotoPreview" alt="" class="d-block" style="width: 150px;">
                        </template>
                        <div v-else style="background: #AAAAAA; width: 150px; height: 180px; text-align: center; padding: 50px;">
                            <i class="ion ion-ios-person" style="font-size: 50px;"></i>
                        </div>
                    </div>
                    <div class="mt-3 ml-3" v-if="!isPegawaiPtt">
                        <label class="btn btn-outline-primary btn-sm">
                            Change
                            <input id="inputFoto" ref="inputFoto" type="file" @change="handleFotoUpload" accept="*" class="user-edit-fileinput">
                        </label>&nbsp;
                        <div v-if="fotoPreview" @click="resetFoto" class="btn btn-default btn-sm md-btn-flat">Reset</div>
                    </div>
                </div>
                <div style="width: 100%;">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <td colspan="3"><h4 class="font-weight-bold mb-2"><span v-text="form.nama"></span></h4></td>
                            </tr>
                            <tr v-if="form.kode">
                                <td style="width: 100px;">No. Induk</td>
                                <td style="width: 2px;">:</td>
                                <td><span v-text="form.kode"></span></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td>Pegawai Tidak Tetap</td>
                            </tr>
                            <tr>
                                <td>Jabatan</td>
                                <td>:</td>
                                <td>
                                    @if($mode=='profile' || $mode=='view')
                                    {{ $data['jabatan']?$data['jabatan']['nama']:'-' }}
                                    @else               
                                    <select class="select2-select" onchange="vueFormPegawai.form.jabatan_id = this.value" data-allow-clear="true">
                                        @foreach($jabatan['data'] as $val)
                                        <option value="{{$val['id']}}" {{ isset($data['jabatan_id'])&&$val['id']==$data['jabatan_id']?' selected="true"':'' }}>{{$val['nama']}}</option>
                                        @endforeach
                                    </select>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Unit</td>
                                <td>:</td>
                                <td>           
                                    <!-- <select class="select2-select" onchange="vueFormPegawai.form.instansi_id = this.value" data-allow-clear="true">
                                        @ include('instansi.selectnested',['data'=>$instansi['data'],'induk'=>0,'maxEselon'=>4])
                                    </select>   -->
                                    <?php /* @ foreach($instansi as $key => $instansiItem)
                                    <select class="select2-select" onchange="vueFormPegawai.form.instansi[{{$key}}] = this.value" data-allow-clear="true">
                                        @ foreach($instansiItem['data'] as $val)
                                        <option value="{{$val['id']}}" {{ isset($instansi_ids[$key])&&$val['id']==$instansi_ids[$key]?' selected="true"':'' }}>{{$val['nama']}}</option>
                                        @ endforeach
                                    </select>
                                    @endforeach */ ?>

                                    <template v-for="(instansiItem, key) in instansi">
                                        <select @change="changeInstansi(key)" class="form-control mb-2" v-model="instansiIds[key+1]" {{ ($mode=='profile' || $mode=='view' || $isSatker) ? ' readonly="true" disabled="true"' : ''}}>
                                            <template v-for="(val) in instansiItem">
                                                <option :value="val.id">@{{val.nama}}</option>                                            
                                            </template>
                                        </select>
                                    </template>

                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">                                    
                                    <a href="javascript:void(0)" @click="submitData" class="btn btn-success" v-if="isEditable">
                                        Simpan Data
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="nav-tabs-top">

            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#user-biografi">Biografi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#user-alamat">Alamat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#user-pendidikan">Riwayat Pendidikan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#user-keluarga">Keluarga</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#user-doktah">Doktah</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="user-biografi">
                    @include('pegawai.formbiografi')
                </div>

                <div class="tab-pane fade" id="user-alamat">
                    @include('pegawai.formalamat',['data'=>[],'count'=>0])
                </div>

                <div class="tab-pane fade" id="user-pendidikan">
                    @include('pegawai.formpendidikan',['data'=>[],'count'=>0])
                </div>

                <div class="tab-pane fade" id="user-keluarga">
                    @include('pegawai.formkeluarga',['data'=>[],'count'=>0])
                </div>

                <div class="tab-pane fade" id="user-doktah">
                    @include('pegawai.formdoktah',['data'=>[],'count'=>0])
                </div>
            </div>

        </div>
                    
        <div class="card-body">                  
            <a href="javascript:void(0)" @click="submitData" class="btn btn-success" v-if="isEditable">
                Simpan Data
            </a>
        </div>

    </div>
</div>
@endsection