@extends('layouts.layout-horizontal-sidenav')

@section('styles')
<link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
<link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">

<link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">

<style>
    /* ---- */
    .list-instansi .list-item {
        list-style: none;
        padding-left: 20px;
    }
    .list-instansi .heading {
        padding: 5px;
        position: relative;
    }
    .list-instansi .heading > h5 {
        font-weight: normal;
    }
    .list-instansi .heading:hover{
        background: rgba(0,0,0,0.05);
    }

    .item-header {
        padding-left: 20px;
        display: block;
        position: relative;
    }
    .item-header:after {
        font-family: 'Font Awesome 5 Free';
        content: "\f105";
        /* fa-chevron-down */
        position: absolute;
        left: 0;
        top: 1px;
        font-weight: 900;
    }

    .item-header[aria-expanded="true"]:after {
        content: "\f107";
        /* fa-chevron-up */
    }
    .item-header.no-child:after {
        content: "\f111";   
        font-size: 7px;
        padding-top: 5px;
    }
</style>
@endsection

@section('scripts')
@parent
<!-- Dependencies -->
<script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
<script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

<script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
<!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
<!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->

<script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>

<script>
    var apiPath = '{{config('AppConfig.endpoint.api.Master')}}/instansi';
    var vueListJabatan = new Vue({
        el: '#vueListEselon',
        data: {
            formTitle: 'Tambah Baru',

            data: {
                data: {!! json_encode($data) !!},
                count: {{ $count }}
            },
            dataById: {},

            isAdd: true,
            form: {
                id: 0,
                nama: '',
                kode: '',
                singkatan: '',
                eselon: 1,
                induk: 0,
                induk_path: ''
            },
            formEmpty: {
                id: 0,
                nama: '',
                kode: '',
                singkatan: '',
                eselon: 1,
                induk: 0,
                induk_path: ''
            }
        },
        created: function() {
            var that = this;
            this.form = JSON.parse(JSON.stringify(this.formEmpty));
            _.forEach(this.data.data,function(v,k){
                _.forEach(v,function(v2,k2){
                    that.dataById[v2.id] = JSON.parse(JSON.stringify(v2));
                });
            });
        },
        mounted: function() {                   
            $('.select2-modal').select2({
                dropdownParent: $("#formJabatanModal")
            });
        },
        watch: {
            'form.induk': function(v){
                console.log('induk change : ',v);
                if(v!=0){
                    this.dataById[v];
                    this.form.induk_path = this.dataById[v].induk_path + (this.dataById[v].induk_path==''?';':'') + this.dataById[v].id + ';';
                    this.form.eselon = parseInt(this.dataById[v].eselon) + 1;
                }else{
                    this.form.induk_path = '';
                    this.form.eselon = 1;
                }                
            }
        },
        methods: {
            showForm(isAdd = true, id = 0) {
                this.isAdd = isAdd;
                if (!isAdd) {
                    this.formTitle = 'Edit Data';
                    axios.get(apiPath + '/' + id)
                        .then((res) => {
                            this.form = res.data.data;
                            $('.select2-modal').val(this.form.induk).trigger('change');
                            $('#formJabatanModal').modal('show');
                        }).catch((res) => {
                            showAlert({
                                text: "Load Data Gagal : ",
                                type: "warning"
                            });
                        });
                } else {
                    this.formTitle = 'Tambah Data';
                    this.form = JSON.parse(JSON.stringify(this.formEmpty));
                    $('.select2-modal').val(id).trigger('change');
                    // this.form.induk = id;
                    $('#formJabatanModal').modal('show');
                }
            },
            formSubmitted(ev) {
                ev.preventDefault();
                console.log(this.form);
                // if(!this.hasAccess){
                //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                //     return false;
                // }
                if (this.isAdd) {
                    this.saveData(this.form);
                } else {
                    this.updateData(this.form.id, this.form);
                }
            },
            saveData(data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                axios.post(apiPath, data).then((res) => {
                    showAlert({
                        text: "Data saved"
                    });
                    $('#formJabatanModal').modal('hide');
                    window.location.href = "{{url()->current()}}";
                }).catch((res) => {
                    showAlert({
                        text: "Save data failed",
                        type: "warning"
                    });
                });
            },
            updateData(id, data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                axios.put(apiPath + '/' + id, data).then((res) => {
                    showAlert({
                        text: "Data updated"
                    });
                    $('#formJabatanModal').modal('hide');
                    window.location.href = "{{url()->current()}}";
                }).catch((res) => {
                    showAlert({
                        text: "Update data failed",
                        type: "warning"
                    });
                });
            },
            deleteItem(id) {
                showAlert({
                    styleType: "modal",
                    style: "warning",
                    title: "Konfirmasi Penghapusan",
                    text: "Apakah anda yakin ?",
                    modalButtonCancel: "Batal",
                    modalButtonOk: "Ya",
                    onOk: () => {
                        axios.delete(apiPath + "/" + id)
                            .then((res) => {
                                showAlert({
                                    text: "Data '" + id + "' Berhasil dihapus."
                                });
                                window.location.href = "{{url()->current()}}";
                            }).catch((res) => {
                                showAlert({
                                    text: "Hapus gagal : " + res.message,
                                    type: "warning"
                                });
                            });
                    }
                });
            }
        }
    });

    $(document).ready(function() { 
        // $('#modal-induk-select').on('change',function(v){
        //     vueListJabatan.form.induk = $(this).val();
        // });
    });

</script>
@endsection


@section('content')
<div id="vueListEselon">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Setup /</span> Eselon IV</div>
        <!-- <div @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Eselon
        </div> -->
    </h4>
    @include("alert")
    <div class="card">

        <div class="card-body list-instansi">
            @include('instansi.nestedlist',['data'=>$data,'induk'=>0])            
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col">
                    Jumlah {{$count}}
                </div>
            </div>
        </div>

        <!-- modal form Jabatan -->
        <div class="modal fade" id="formJabatanModal" tabindex="-1" role="dialog" aria-labelledby="formJabatanModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="formJabatanModalTitle" v-text="formTitle">Jabatan Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        
                        <div class="form-row">
                            <div class="form-group col" v-show="form.eselon != 1">
                                <label class="form-label">Parent Eselon</label>                          
                                <select id="modal-induk-select" class="select2-modal" style="width: 100%" onchange="vueListJabatan.form.induk = this.value" data-allow-clear="true">
                                    @include('instansi.selectnested',['data'=>$data,'induk'=>0])
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Eselon</label>
                                <div class="form-control">@{{form.eselon}}</div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" v-model="form.nama" placeholder="Nama">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Kode</label>
                                <input type="text" class="form-control" v-model="form.kode" placeholder="Kode">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Singkatan</label>
                                <input type="text" class="form-control" v-model="form.singkatan" placeholder="Singkatan">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="formSubmitted">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- / modal form Jabatan -->

    </div>
</div>
@endsection