@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
@endsection

@section('scripts')
@parent
    <!-- Dependencies --> 
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = '{{config('AppConfig.endpoint.api.Master')}}/mesin_absen';
        var vueListMesinAbsen = new Vue({
            el: '#vueListMesinAbsen',
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
                instansiList: {!!json_encode($instansiList)!!},
                instansi:[],
                instansiIds: {!!json_encode($instansiIds)!!},
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
                    ip: '',
                    instansi_id: ''
                },
                formEmpty: {
                    id:0,
                    nama: '',
                    ip: '',
                    instansi_id: ''
                }
            },
            created: function() {
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
                this.renderInstnasi();
            },
            mounted: function() {                   
                $('.select2-select').select2({dropdownParent: $("#formMesinAbsenModal")});
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
                    this.instansi = [];
                    _.forEach(this.instansiIds,(v,k)=>{
                        if(v!=this.form.instansi_id){
                            var tmpInstansiList = this.getInstansiList(v);
                            if(tmpInstansiList.length!=0){
                                if(k!=0)tmpInstansiList.splice(0,0,{id:0,name:''});
                                this.instansi.push(tmpInstansiList);
                            }
                                
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
                    if(this.instansiIds[key+1] != 0){
                        if(this.instansiIds[key+1]!=undefined){
                            var tmpInstansiList = this.getInstansiList(this.instansiIds[key+1]);
                            
                            if(tmpInstansiList.length>0){
                                tmpInstansiList.splice(0,0,{id:0,name:''});
                                this.instansi.push(tmpInstansiList);
                            }
                                
                        }else{
                            this.instansiIds.splice(key+1);
                        }
                    }
                    this.form.instansi_id = this.instansiIds[this.instansiIds.length-1];
                    if(this.form.instansi_id==0)this.form.instansi_id = this.instansiIds[this.instansiIds.length-2];
                },
                showForm(isAdd=true,id=0) {                  
                    this.isAdd = isAdd;
                    if(!isAdd){       
                        this.formTitle = 'Edit Data';
                        axios.get(apiPath + '/' + id)
                            .then((res)=>{
                                this.form = res.data.data;
                                $('#formMesinAbsenModal').modal('show');
                                var tmpInstansi = this.getInstansi(this.form.instansi_id);
                                tmpInstansi = tmpInstansi.induk_path.split(';');
                                tmpInstansi.splice(tmpInstansi.length-1,1,this.form.instansi_id);
                                tmpInstansi.splice(0,1);
                                tmpInstansi = tmpInstansi.map(x => parseInt(x));
                                this.instansiIds = tmpInstansi;
                                this.renderInstnasi();
                                // $('.select-instansi').val(this.form.instansi_id);
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                    }else{    
                        this.formTitle = 'Tambah Data';  
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        $('#formMesinAbsenModal').modal('show');
                        this.instansiIds = [1];
                        this.renderInstnasi();
                    }
                },
                formSubmitted(ev) {
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
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
                        $('#formMesinAbsenModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Save data failed : " + res.messages,type: "warning"});
                    });
                },
                updateData(id, data) {
                    // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        $('#formMesinAbsenModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Update data failed : " + res.messages,type: "warning"});
                    });
                },
                tarikData(id) {                    
                    alert('underconstruction');
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
<div id="vueListMesinAbsen">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Setup /</span> Mesin Absen</div>
        @if(\UserAuth::hasAccess('Master.mesinabsen','c'))
        <div @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Mesin Absen 
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
                        <th style="min-width: 5rem;">Satuan Kerja</th>
                        <th style="min-width: 5rem;">Nama Mesin</th>
                        <th>IP Address</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i) in data.data">
                        <td><span v-text="(i+1) * data.currentPage"></span></td>
                        <td><span v-text="item.instansi?item.instansi.nama:'-'"></span></td>
                        <td><span v-text="item.nama"></span></td>
                        <td><span v-text="item.ip"></span></td>
                        <td>  
                            <div @click="tarikData(item.id)" class="btn btn-warning btn-xs icon-btn md-btn-flat article-tooltip" title="Tarik Data Absen">
                                <i class="ion ion-md-cloud-download"></i>
                            </div> 
                            
                            @if(\UserAuth::hasAccess('Master.mesinabsen','u'))
                            <div @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                <i class="ion ion-md-create"></i>
                            </div> 
                            @endif
                            @if(\UserAuth::hasAccess('Master.mesinabsen','d'))
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

        <!-- modal form MesinAbsen -->
        <div class="modal fade" id="formMesinAbsenModal" tabindex="-1" role="dialog" aria-labelledby="formMesinAbsenModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formMesinAbsenModalTitle" v-text="formTitle">Mesin Absen Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                           
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Satuan kerja </label>      
                                <?php /* <select class="select2-select select-instansi" onchange="vueListMesinAbsen.form.instansi_id = this.value" data-allow-clear="true">
                                    @include('instansi.selectnested',['data'=>$instansi['data'],'induk'=>0,'maxEselon'=>4])
                                </select> */ ?>
                                
                                <template v-for="(instansiItem, key) in instansi">
                                    <select @change="changeInstansi(key)" class="form-control mb-2" v-model="instansiIds[key+1]">
                                        <template v-for="(val) in instansiItem">
                                            <option :value="val.id">@{{val.nama}}</option>                                            
                                        </template>
                                    </select>
                                </template>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Nama Mesin </label>
                                <input type="text" class="form-control" v-model="form.nama" placeholder="Nama">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">IP Address</label>
                                <input type="text" class="form-control" v-model="form.ip" placeholder="IP Address">
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