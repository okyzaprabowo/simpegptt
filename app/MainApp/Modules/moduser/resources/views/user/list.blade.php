@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script> -->
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = "{{config('AppConfig.endpoint.api.moduser')}}";
        var vueListPegawai = new Vue({
            el: '#vueListUser',
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
                    deskripsi: ''
                },
                formEmpty: {
                    id:0,
                    nama: '',
                    deskripsi: ''
                }
            },
            created: function() {
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
                // searchString(v) {
                //     let val = v.toLowerCase();      
                //     var that = this;
                //     clearTimeout(this.suggestTimeout);
                //     this.suggestTimeout = setTimeout(function(){
                //         that.loadList(1,val);      
                //     },300);
                // }
            },
            methods: {
                showForm(isAdd=true,id=0) {   
                    this.isAdd = isAdd;
                    if(!isAdd){
                        window.location.href = ("{{route('user.edit',['id'=>'USER_ID'])}}").replace("USER_ID",id);
                    }else{    
                        window.location.href = ("{{route('user.view',['id'=>'USER_ID'])}}").replace("USER_ID",id);
                    }
                },
                goSearch()
                {
                    this.loadList(this.curPage,this.searchString,this.sortBy,this.sortDesc);
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
                    window.location.href = "{{route('user.list')}}?" + params(this.loadParams.params);
                    // axios.get(apiPath , this.loadParams)
                    //     .then((res)=>{
                    //         this.data = res.data.data;
                    //     }).catch((res)=>{
                    //         showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                    //     });
                }
            }
        });

        $(document).ready(function(){
            
        });
    </script>
@endsection


@section('content')
<div id="vueListUser">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">Setup /</span> Managemen User</div>
        @if(\UserAuth::hasAccess('moduser.user','c'))
        <a href="{{route('user.addNew')}}" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah User
        </a>
        @endif
    </h4>
    @include("alert")
    <div class="card">        
        
        <div class="card-body">
            <div class="row">
                <div class="col">       
                    Per page: &nbsp;                    
                    <select class="form-control d-inline-block w-auto" v-model="perPage">
                        <option v-for="option in perPageOption" :value="option">@{{option}}</option>
                    </select>         
                    <input v-model="searchString" type="text" class="form-control d-inline-block w-auto" placeholder="Search...">
                    <button class="btn btn-success" @click="goSearch()">Go</button>
                </div>
                <div class="col">    
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th style="min-width: 5rem;">Nama</th> 
                        <th style="min-width: 1rem;">Username</th>
                        <th style="">Email</th> 
                        <th style="">Role</th> 
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i) in data.data">
                        <td><span v-text="(i+1) * data.currentPage"></span></td>
                        <td><span v-text="item.name"></span></td>
                        <td><span v-text="item.username"></span></td> 
                        <td><span v-text="item.email"></span></td> 
                        <!-- <td><span ></span></td>  -->
                        <td>
                            <span v-for="(role) in item.roles">
                                <span :class="{badge:true,'m-1':true, 'badge-outline-success':role.is_main_role==1, 'badge-outline-info':role.is_main_role==0}" v-text="role.role.name"></span> 
                            </span>
                        </td> 
                        <td>         
                            <template v-if="item.level != 6">                   
                                <div @click="showForm(true,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="View">
                                    <i class="ion ion-md-eye"></i>
                                </div> 
                                @if(\UserAuth::hasAccess('moduser.user','u'))
                                <div @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                    <i class="ion ion-md-create"></i>
                                </div> 
                                @endif
                                @if(\UserAuth::hasAccess('moduser.user','d'))
                                <div @click="deleteItem(item.id)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                                    <i class="ion ion-md-close"></i>
                                </div>
                                @endif
                            </template>
                            <template v-else>                                                   
                                <div @click="window.location.href = ('{{route('pegawai.view',['id'=>'PEGAWAI_ID'])}}').replace('PEGAWAI_ID',item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="View">
                                    <i class="ion ion-md-eye"></i>
                                </div>
                                <div @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                    <i class="ion ion-md-create"></i>
                                </div> 
                            </template>
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

        

    </div>
</div>
@endsection