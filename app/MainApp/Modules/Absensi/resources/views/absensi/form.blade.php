@extends('layouts.layout-horizontal-sidenav')


@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}"> -->
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>     -->
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <script>   
        var apiPath = "{{config('AppConfig.endpoint.api.Absensi')}}/upload";
        var vueListPegawai = new Vue({
            el: '#vueListAbsensiRawUpload',
            data: {

                sortBy: "id",
                sortDesc: false,
                searchString: "{{$filter['q']}}",
                perPageOption: [10,20,50,100],
                
                data: {
                   data: {!!json_encode($absensiRawUpload['data'])!!},
                   count: {{$absensiRawUpload['count']}},
                   currentPage: {{$absensiRawUpload['currentPage']}},
                   limit: {{$absensiRawUpload['limit']}},
                   offset: {{$absensiRawUpload['offset']}},
                   pageCount: {{$absensiRawUpload['pageCount']}}
                },

                loadParams: {
                    params: {}
                }

            },
            mounted: function() {                   
                $('.select2-select').select2();  
                // $('.datepicker-base').datepicker({format: 'yyyy-mm-dd'});
            },
            watch: {
                'data.currentPage':function(v,old) {
                    if(v!=old)
                        this.loadList(v,this.searchString,this.sortBy,this.sortDesc);
                },
                'data.limit':function(v,old) {
                    if(v!=old)
                        this.loadList(this.data.currentPage,this.searchString,this.sortBy,this.sortDesc);
                },
                sortBy(v) {
                    this.loadList(this.data.currentPage,this.searchString,v,this.sortDesc);
                },
                sortDesc(v) {
                    this.loadList(this.data.currentPage,this.searchString,this.sortBy, v);
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
            methods: {             
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
                                    this.loadList(this.data.currentPage,this.searchString,this.sortBy,this.sortAsc);
                                }).catch((err)=>{
                                    // var err = localApiErrorParse(res.response);
                                    showAlert({text: "Hapus gagal : " + err.message,type: "warning"});
                                });
                        }
                    });
                },
                loadList(curPage,q='',orderBy=false,sortDesc=false) {
                    var offset = (this.data.limit * (curPage-1));
                    
                    this.loadParams.params.limit = this.data.limit;
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

                    window.location.href = "{{route('absensi_upload')}}?" + params(this.loadParams.params);
                    return;

                    // belum bikin yang auto generate pagination, jadi sementara langsung hard link
                    axios.get(apiPath , this.loadParams)
                        .then((res)=>{
                            this.data = res.data.data;
                        }).catch((err)=>{
                            // var err = localApiErrorParse(res.response);
                            showAlert({text: "Load Data Gagal : " + err.message,type: "warning"});
                        });
                },
                processRaw(ev) {
                    var that = this;
                    axios.put("{{route('absensi_upload.api.process')}}")
                        .then((res)=>{
                            showAlert({text: "Raw data berhasil diproses",type: "info"});
                            that.loadList(that.data.currentPage,that.searchString,that.sortBy,that.sortDesc);
                        }).catch((err)=>{
                            // var err = localApiErrorParse(res.response);
                            showAlert({text: "Proses gagal : " + err.message,type: "warning"});
                        });
                }
            }
        });
    </script>
@endsection

@section('content')
<div id="vueListAbsensiRawUpload">

    <h4 class="font-weight-bold py-3 mb-4">
        <span class="text-muted font-weight-light">Absensi /</span> Upload Data Dari Mesin Absensi
    </h4>

    @include("alert")

    <div class="card"> 

        <div class="card-body">
            <form method="POST" action="{{route('absensi_upload')}}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col">    
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4 text-sm-right">Mesin Absensi</label>
                            <div class="col-sm-8">                                          
                            <select class="select2-select" name="mesin_absen_id" data-allow-clear="true">
                                @foreach($mesin['data'] as $val)
                                <option value="{{$val['id']}}">{{$val['nama'].' - ['.$val['ip'].']'}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-4 text-sm-right">Data File</label>
                            <div class="col-sm-8">
                                <input name="data_file" accept=".dat" type="file" class="form-control pb-2 h-100" placeholder="File">
                            </div>
                        </div>
                    </div>
                    <div class="col">             
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>        

        <div class="card-body">
            <div class="row">
                <div class="col">       
                    Per page: &nbsp; 
                    <select class="form-control form-control-sm d-inline-block w-auto" v-model="data.limit">
                        <option v-for="option in perPageOption" :value="option">@{{option}}</option>
                    </select>
                </div>
                <div class="col">
                    <input v-model="searchString" type="text" class="form-control form-control-sm d-inline-block w-auto float-sm-right" placeholder="Search...">
                </div>
                <div class="col text-right">
                    <div class="btn btn-success" @click="processRaw">
                        <i class="ion ion-md-checkmark"></i> Proses Raw Data
                    </div>
                </div>
            </div>
        </div>  
        
        <table class="table table-striped table-bordered mb-0">
            <thead>
                <tr>
                    <th>Nama File</th>
                    <th>Tanggal Upload</th>
                    <th>Status</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="val in data.data">
                    <td><span v-text="val.nama"></span></td>
                    <td><span v-text="val.created_at"></span></td>
                    <td>
                        <span class="badge badge-warning" v-if="val.status==0">File Baru</span>
                        <span class="badge badge-warning" v-else-if="val.status==1">Sedang proses insert</span>
                        <span class="badge badge-warning" v-else-if="val.status==2">Proses insert selesai</span>
                        <span class="badge badge-warning" v-else-if="val.status==3">Sedang proses kalkukasi</span>
                        <span class="badge badge-success" v-else>Selesai</span>
                    </td>
                    <td>      
                        <a v-if="val.status!=0" :href="('{{route('absensi_upload.detail',['id'=>'UPLOAD_ID'])}}').replace('UPLOAD_ID',val.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="View Detail">
                            <i class="ion ion-md-menu"></i>
                        </a> 
                        <div v-if="val.status!=1 && val.status!=3" @click="deleteItem(val.id)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Delete">
                            <i class="ion ion-md-close"></i>
                        </div> 
                    </td>
                </tr>
            </tbody>
        </table>
        
        <div class="card-body"> 
            <div class="row">  
                <div class="col-sm text-sm-left text-center pt-0">
                    <span class="text-muted">Page @{{data.currentPage}} of @{{data.pageCount}}</span>
                </div>
                <div class="col-sm pt-0">  
                    {{ $pagination->links() }}
                </div>
            </div>       
        </div>

    </div>
</div>
@endsection