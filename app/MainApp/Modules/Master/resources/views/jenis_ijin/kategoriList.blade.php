@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/timepicker/timepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <style type="text/css">
     .ui-timepicker-wrapper {
        z-index: 3500 !important;
    }
    .select2-container--open {
            z-index: 9999999
        }
    </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/timepicker/timepicker.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = '{{config('AppConfig.endpoint.api.Master')}}/jenis_ijin_kategori';
        
        var vueListJenisIjinKategori = new Vue({
            el: '#vueListJenisIjinKategori',
            data: {
                formTitle: 'Tambah Baru',

                sortBy: "id",
                sortDesc: false,
                searchString: "",
                curPage: 1,
                perPage: 10,
                perPageOption: [10,20,50,100],
                
                loadParams: {
                    params: {}
                },
 
                data: {
                   data: {!!json_encode($data)!!},
                   count: {{$count}},
                   currentPage: {{$currentPage}},
                   limit: {{$limit}},
                   offset: {{$offset}},
                   pageCount: {{$pageCount}}
                },
                
                isAdd: true,
                form: {
                },
                formEmpty: {
                    id:0,
                    nama: '',
                    singkatan: '',
                    tampil_rekap_kehadiran: 1,
                    tampil_kehadiran_harian: 1
                }
            },
            created: function() {
                console.log(this.data.data);
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },
            watch: {
                curPage(v) {
                    this.loadList(v,this.searchString,this.sortBy,this.sortDesc);
                },
                perPage(v) {
                    this.loadList(this.curPage,this.searchString,this.sortBy,this.sortDesc);
                },
                sortBy(v) {
                    this.loadList(this.curPage,this.searchString,v,this.sortDesc);
                },
                sortDesc(v) {
                    this.loadList(this.curPage,this.searchString,this.sortBy, v);
                },    
                searchString(v) {
                    let val = v.toLowerCase();      
                    var that = this;
                    clearTimeout(this.suggestTimeout);
                    this.suggestTimeout = setTimeout(function(){
                        that.loadList(1,val);      
                    },300);
                }
            },
            mounted: function() {                   
                $('.select2-select').select2();                
               
            },
            methods: {
                showForm(isAdd=true,id=0) {                  
                    this.isAdd = isAdd;
                    if(!isAdd){       
                        this.formTitle = 'Edit Data';
                        axios.get(apiPath + '/' + id)
                            .then((res)=>{
                                this.form = res.data.data;
                                $('#formKategoriModal').modal('show');
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                    }else{    
                        this.formTitle = 'Tambah Data';  
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        $('#formKategoriModal').modal('show');
                    }
                },
                formSubmitted(ev) {
                    ev.preventDefault();
                    if(this.isAdd){
                        this.saveData(this.form);
                    }else{
                        this.updateData(this.form.id, this.form);
                    }
                },
                saveData(data) {
                    axios.post(apiPath,data).then((res)=>{
                        showAlert({text: "Data saved"});
                        $('#formKategoriModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Save data failed",type: "warning"});
                    });
                },
                updateData(id, data) {
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        $('#formKategoriModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Update data failed",type: "warning"});
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
                        onOk:()=>{
                            axios.delete(apiPath + "/" + id)
                                .then((res)=>{
                                    showAlert({text: "Data '" + id + "' Berhasil dihapus."});
                                    this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                                }).catch((res)=>{
                                    showAlert({text: "Hapus gagal : " + res.message,type: "warning"});
                                });
                        }
                    });
                },
                loadList(curPage,q='',orderBy=false,sortDesc=false) {
                    var offset = (this.perPage * (curPage-1));
                    
                    this.loadParams.params.limit = this.perPage;
                    this.loadParams.params.offset = offset;

                    if(q!=''){
                        this.loadParams.params.q = q;
                    }else{
                        delete this.loadParams.params.q;
                    }
                    if(orderBy!=false){
                        this.loadParams.params.orderBy = orderBy;
                        this.loadParams.params.orderType = sortDesc?'DESC':'ASC';
                    }
                    axios.get(apiPath , this.loadParams)
                        .then((res)=>{
                            this.data = res.data.data;
                        }).catch((res)=>{
                            showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                        });
                }
            }
        });

        $(document).ready(function(){
           
        });
    </script>
@endsection


@section('content')
<div id="vueListJenisIjinKategori">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div>
            <span class="text-muted font-weight-light">
                Setup / 
                <a href="{{route('master.jenis_ijin.list')}}">Jenis Permohonan</a> / 
            </span> 
            Kategori
        </div>
        @if(\UserAuth::hasAccess('Master.jenisijin.kategori','c'))
        <div @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Kategori
        </div>
        @endif
    </h4>
    @include("alert")
    <div class="card">        
        
        <div class="card-body">
            <div class="row">
                <div class="col">       
                    Per page: &nbsp;                    
                    <select class="form-control form-control-sm d-inline-block w-auto" v-model="perPage">
                        <option v-for="option in perPageOption" :value="option">@{{option}}</option>
                    </select>
                </div>
                <div class="col">             
                    <input v-model="searchString" type="text" class="form-control form-control-sm d-inline-block w-auto float-sm-right" placeholder="Search...">
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th style="min-width: 5rem;">Nama Kategori</th>
                        <th>Singkatan</th>
                        <th>Visibility</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i) in data.data">
                        <td><span v-text="i+1 + ((data.currentPage-1)*data.limit)"></span></td>
                        <td><span v-text="item.nama"></span></td>
                        <td><span v-text="item.singkatan"></span></td>
                        <td>
                            <div>Rekap kehadiran : <span v-text="item.tampil_rekap_kehadiran?'Ya':'Tidak'"></span></div>
                            <div>Kehadiran harian : <span v-text="item.tampil_kehadiran_harian?'Ya':'Tidak'"></span></div>
                        </td>
                        <td>           
                            @if(\UserAuth::hasAccess('Master.jabatan.kategori','u'))                 
                            <div @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                <i class="ion ion-md-create"></i>
                            </div> 
                            @endif
                            
                            @if(\UserAuth::hasAccess('Master.jabatan.kategori','d'))
                            <div @click="deleteItem(item.id)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                                <i class="ion ion-md-close"></i>
                            </div>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-body"> 
            <div class="row">  
                <div class="col-sm text-sm-left text-center pt-0">
                    <span class="text-muted">Page <span v-text="data.currentPage"></span> of <span v-text="data.pageCount"></span></span>
                </div>
                <div class="col-sm pt-0">  
                    {{ $pagination->links() }}
                </div>
            </div>       
        </div>

        <!-- modal form Jabatan -->
        <div class="modal fade" id="formKategoriModal" style="z-index: 1200 !important;" tabindex="-1" role="dialog" aria-labelledby="formKategoriModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formKategoriModalTitle" v-text="formTitle">Jabatan Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" class="form-control" v-model="form.nama" placeholder="Nama">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Singkatan</label>
                                <input type="text" class="form-control" v-model="form.singkatan" placeholder="Singkatan">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Tampilkan di Rekap kehadiran ?</label>                                              
                                <select class="form-control" v-model="form.tampil_rekap_kehadiran">
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>                                    
                                </select>
                            </div>
                            <div class="form-group col">
                                <label class="form-label">Tampilkan di Kehadiran harian ?</label>                                             
                                <select class="form-control" v-model="form.tampil_kehadiran_harian">
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>                                    
                                </select>
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

    </div>
</div>
@endsection