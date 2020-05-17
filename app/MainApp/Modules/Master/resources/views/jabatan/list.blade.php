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
        var apiPath = '{{config('AppConfig.endpoint.api.Master')}}/jabatan';
        
        var vueListJabatan = new Vue({
            el: '#vueListJabatan',
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
                    id:0,
                    nama: '',
                    instansi_ids: '',
                    jabatan_instansi : [],
                    shift_id :0,
                    // jam_masuk: '08:00',
                    // jam_pulang: '16:00',
                    deskripsi: ''
                },
                formEmpty: {
                    id:0,
                    nama: '',
                    instansi_ids: '',
                    jabatan_instansi : [],
                    shift_id:0,
                    // jam_masuk: '08:00',
                    // jam_pulang: '16:00',
                    deskripsi: ''
                },
                jabatanInstansiEmpty :{
                    id :0,
                    instansi_id :0
                }
            },
            created: function() {
                console.log(this.data.data);
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
              //  this.form.jabatan_instansi = JSON.parse(JSON.stringify(this.jabatanInstansiEmpty));
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
                                $('#shift_id').val(this.form.shift_id).trigger('change');
                                var instansi_ids = new Array();
                                for (let i=0; i < this.form.jabatan_instansi.length;i++ ){
                                    instansi_ids[i] = this.form.jabatan_instansi[i].instansi_id;
                                     
                                }
                                // console.log(instansi_ids);
                                $('#instansi_ids').val(instansi_ids).trigger('change');
                                $('#formJabatanModal').modal('show');
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                    }else{    
                        this.formTitle = 'Tambah Data';  
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        // this.form.jabatan_instansi = JSON.parse(JSON.stringify(this.jabatanInstansiEmpty));
                        $('#formJabatanModal').modal('show');
                    }
                },
                formSubmitted(ev) {
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
                    // this.form.jam_masuk = $("#jam_masuk").val();
                    // this.form.jam_pulang = $("#jam_pulang").val();
                    // this.form.instansi_ids = $("#instansi_ids").val();

                    // this.form.jabatan_instansi = $("#instansi_ids").val();
                  //  var list_instansi = $("#instansi_ids").val();
                    

                    var ids = $("#instansi_ids").val();
                     console.log(ids);
                   // var list_id =  ids.split(',');
                   this.form.jabatan_instansi = [];
                    for (let i=0;i<ids.length;i++) {
                       // if (!this.form.jabatan_instansi[i]){
                            this.form.jabatan_instansi[i] = JSON.parse(JSON.stringify(this.jabatanInstansiEmpty));
                       // }
                        // console.log(this.form.jabatan_instansi[i].instansi_id);
                        this.form.jabatan_instansi[i].instansi_id = ids[i];
                       // this.form.jabatan_instansi[i].id = ids[i];
                    }

                    if(this.isAdd){
                        this.saveData(this.form);
                    }else{
                        this.updateData(this.form.id, this.form);
                    }
                },
                saveData(data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.post(apiPath,data).then((res)=>{
                        showAlert({text: "Data saved"});
                        $('#formJabatanModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Save data failed",type: "warning"});
                    });
                },
                updateData(id, data) {
                    // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        $('#formJabatanModal').modal('hide');
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

     /*   $('#jam_masuk').timepicker({
                    'step': 15,
                    timeFormat:'H:i',
                    zindex:9999,
                    defaultTime:'08:00',
                    scrollDefault:'08:00',
                    orientation: 'l'
                });
        $('#jam_pulang').timepicker({
                    'step': 15,
                    timeFormat:'H:i',
                    zindex:9999,
                    scrollDefault:'16:00',
                    defaultTime:'16:00',
                    orientation: 'l',
                    change:function(val){
                        vueListJabatan.data.form.jam_pulang = val;
                        console.log(val);
                    }
                });*/

        $(document).ready(function(){
           
        });
    </script>
@endsection


@section('content')
<div id="vueListJabatan">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Setup /</span> Jabatan</div>
        @if(\UserAuth::hasAccess('Master.jabatan','c'))
        <div @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Jabatan
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
                        <th style="min-width: 5rem;">Nama Jabatan</th>
                        <th>Shift</th>
                        <th>Unit Kerja</th>
                        <th>Keterangan</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i) in data.data">
                        <td><span v-text="i+1 + ((data.currentPage-1)*data.limit)"></span></td>
                        <td><span v-text="item.nama"></span></td>
                        <td><span v-text="item.shift?item.shift.nama:'Belum di set'"></span></td>
                      <!--  <td><span v-text="item.jam_masuk+' s.d '+item.jam_pulang"></span></td> -->
                        <td> 
                       <!-- <span v-text="item.jabatan_instansi"></span>-->
                             <span v-for="(x) in item.jabatan_instansi">
                                    <span v-text="x.instansi.nama"></span>,
                                </span>  
                        </td>
                        <td><span v-text="item.deskripsi"></span></td>
                        <td>           
                            @if(\UserAuth::hasAccess('Master.jabatan','u'))                 
                            <div @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                <i class="ion ion-md-create"></i>
                            </div> 
                            @endif
                            
                            @if(\UserAuth::hasAccess('Master.jabatan','d'))
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
        <div class="modal fade" id="formJabatanModal" style="z-index: 1200 !important;" tabindex="-1" role="dialog" aria-labelledby="formJabatanModalTitle" aria-hidden="true">
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
                            <div class="form-group col">
                                <label class="form-label">Nama Jabatan</label>
                                <input type="text" class="form-control" v-model="form.nama" placeholder="Nama">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Shift</label>
                                <select id="shift_id" class="form-control select2-select" onchange="vueListJabatan.form.shift_id=this.value">
                                      <option value="">Pilih Shift</option>
                                        @foreach($shift['data'] as $val)
                                        <option value="{{$val['id']}}"  >{{$val['nama']}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                      <!--  <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Jam Masuk</label>
                                <input type="text" id="jam_masuk" class="form-control" v-model="form.jam_masuk" placeholder="Jam Masuk">
                            </div>
                            <div class="form-group col">
                                <label class="form-label">Jam Pulang</label>
                                <input type="text" id="jam_pulang" class="form-control" v-model="form.jam_pulang" placeholder="Jam Pulang">
                            </div>
                        </div>
                        -->
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Unit Kerja</label>
                                <select id="instansi_ids" class="form-control select2-select" multiple  data-allow-clear="true">
                                        @include('instansi.selectnested',['data'=>$instansi['data'],'induk'=>0])
                                    </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" v-model="form.deskripsi" placeholder="Deskripsi"></textarea>
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