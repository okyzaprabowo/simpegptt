@extends('layouts.layout-horizontal-sidenav')

@section('scripts')
@parent
    <!-- Dependencies -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>     -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script> -->
    <script>   
        var apiPath = "{{config('AppConfig.endpoint.api.Absensi')}}/upload";
        var vueListPegawai = new Vue({
            el: '#vueListAbsensiRaw',
            data: {
                uploadId: {{ $absensiRawUpload['id'] }},
                
                sortBy: "id",
                sortDesc: false,
                searchString: "{{$filter['q']}}",
                perPageOption: [10,20,50,100],
                
                data: {
                   data: {!!json_encode($absensiRaw['data'])!!},
                   count: {{$absensiRaw['count']}},
                   currentPage: {{$absensiRaw['currentPage']}},
                   limit: {{$absensiRaw['limit']}},
                   offset: {{$absensiRaw['offset']}},
                   pageCount: {{$absensiRaw['pageCount']}}
                },
                
                loadParams: {
                    params: {}
                }

            },
            mounted: function() {   
                var that = this;                
                // $('.select2-select').select2();  
                // $('.datepicker-base').datepicker({format: 'yyyy-mm-dd'});
                $(document).on('click','.pagination .page-item > a',function(v){
                    // v.preventDefault();
                    return;
                    // belum bikin yang auto generate pagination, jadi sementara langsung hard link
                    axios.get($(this).attr('href'))
                        .then((res)=>{
                            that.data = res.data.data.absensiRaw;
                        }).catch((res)=>{
                            showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                        });
                });
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
                            axios.delete(apiPath + "/detail/" + id)
                                .then((res)=>{
                                    showAlert({text: "Data '" + id + "' Berhasil dihapus."});
                                    this.loadList(this.data.currentPage,this.searchString,this.sortBy,this.sortAsc);
                                }).catch((res)=>{
                                    showAlert({text: "Hapus gagal : " + res.message,type: "warning"});
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
                    
                    window.location.href = "{{route('absensi_upload.detail',['id'=>$absensiRawUpload['id']])}}?" + params(this.loadParams.params);
                    return;

                    // belum bikin yang auto generate pagination, jadi sementara langsung hard link
                    axios.get(apiPath + '/' + this.uploadId , this.loadParams)
                        .then((res)=>{
                            this.data = res.data.data.absensiRaw;
                        }).catch((res)=>{
                            showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                        });
                }
            } 
        });
    </script>
@endsection

@section('content')
<div id="vueListAbsensiRaw">
    <h4 class="font-weight-bold py-3 mb-4">
        <span class="text-muted font-weight-light">Absensi / <a href="{{route('absensi_upload')}}">Upload Data</a> /</span> Detail {{$absensiRawUpload['created_at']}}
    </h4>

    @include("alert")

    <div class="card">

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
            </div>
        </div>

        <table class="table table-striped table-bordered mb-0">
            <thead>
                <tr>
                    <th>Pin</th>
                    <th>Pegawai</th>
                    <th>Waktu Scan</th>
                    <th>Status</th>
                    <th style="width: 100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="val in data.data">
                    <td>@{{val.pin}}</td>
                    <td>@{{val.pegawai?val.pegawai.nama:'-'}}</td>                    
                    <td>@{{val.scan_time}}</td>
                    <td>
                        <span class="badge badge-success" v-if="val.status==1">Sudah diproses</span>
                        <span class="badge badge-secondary" v-else>Data baru</span>
                    </td>
                    <td>                        
                        <div @click="deleteItem(val.id)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="View">
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