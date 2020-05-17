@extends('layouts.layout-horizontal-sidenav')

@section('styles')
@parent
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-markdown/bootstrap-markdown.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-slider/bootstrap-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/minicolors/minicolors.css') }}">
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-markdown/bootstrap-markdown.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-slider/bootstrap-slider.js') }}"></script>
    
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script src="{{ asset('/webdist/vendor/libs/minicolors/minicolors.js') }}"></script>

    <script>
        var apiPath = '{{config('AppConfig.endpoint.api.Master')}}/jenis_ijin';
        var vueListJenisIjin = new Vue({
            el: '#vueListJenisIjin',
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
                jenisIjinKategori: {!!json_encode($jenisIjin)!!},
                
                isAdd: true,
                form: {
                    id:0,
                    nama: '',
                    batas_ijin: 1,
                    is_periode: 0,
                    is_show_scanner: 0,
                    jenis_ijin_kategori_id: 0,
                    warna: '#000000',
                    template_keterangan: '',
                    batas_ijin_tahunan: 1,
                    singkatan: '',
                    deskripsi: ''
                },
                formEmpty: {
                    id:0,
                    nama: '',
                    batas_ijin: 0,
                    is_periode: 0,
                    is_show_scanner: 0,
                    jenis_ijin_kategori_id: 0,
                    warna: '#000000',
                    template_keterangan: '',
                    batas_ijin_tahunan: 0,
                    singkatan: '',
                    deskripsi: ''
                }
            },
            created: function() {
                
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },
            mounted: function() {
                var isRtl = $('body').attr('dir') === 'rtl' || $('html').attr('dir') === 'rtl';
                $('#minicolors-hue').minicolors({
                    control:  'hue',
                    position: 'bottom ' + (isRtl ? 'right' : 'left'),
                });                
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
                setBatasIjin(){
                   // this.form.batas_ijin = 
                },
                showForm(isAdd=true,id=0) {  
                    // $('#batas_ijin').slider({
                    //     ticks:[1,2,3,4,5,6,7,8,9,10],
                    //     ticks_labels:["1","2","3","4","5","6","7","8","9","10"]
                    // });              
                    this.isAdd = isAdd;
                    if(!isAdd){       
                        this.formTitle = 'Edit Data';
                        axios.get(apiPath + '/' + id)
                            .then((res)=>{
                                this.form = res.data.data;                                
                              //  $("#batas_ijin").slider('setValue',this.form.batas_ijin);
                                $('#formJenisIjinModal').modal('show');
                                $('#minicolors-hue').minicolors('value', this.form.warna);
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                    }else{    
                        this.formTitle = 'Tambah Data';  

                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                    //    $("#batas_ijin").slider('setValue',this.form.batas_ijin);
                        $('#formJenisIjinModal').modal('show');
                    }
                },
                formSubmitted(ev) {
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
                    //this.form.batas_ijin = $("#batas_ijin").slider('getValue');
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
                        $('#formJenisIjinModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Save data failed",type: "warning"});
                    });
                },
                updateData(id, data) {
                    // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        $('#formJenisIjinModal').modal('hide');
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Update data failed",type: "warning"});
                    });
                },
                showTemplate(id) {  
                    axios.get(apiPath + '/' + id)
                            .then((res)=>{
                                this.form = res.data.data;
                                var template = this.form.template_keterangan;
                                $("#template").html(template.replace(/<<INPUT>>/g, '<br><input type="text" class="form-control"><br>'));
                                //this.form.template_keterangan);
                                $('#formTemplateModal').modal('show');
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
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
              
       //     $('#batas_ijin').slider();


            $('#bs-markdown').markdown({
                iconlibrary: 'fa',
                footer: '<div id="md-character-footer"></div><small id="md-character-counter" class="text-muted">350 character left</small>',

                onChange: function(e) {
                var contentLength = e.getContent().length;

                if (contentLength > 350) {
                    $('#md-character-counter')
                    .removeClass('text-muted')
                    .addClass('text-danger')
                    .html((contentLength - 350) + ' character surplus.');
                } else {
                    $('#md-character-counter')
                    .removeClass('text-danger')
                    .addClass('text-muted')
                    .html((350 - contentLength) + ' character left.');
                }
                },
            });
        });
    </script>
@endsection


@section('content')
<div id="vueListJenisIjin">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Setup /</span> Jenis Permohonan</div>
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{route('master.jenis_ijin_kategori.list')}}" class="btn btn-primary rounded-pill d-block">
                <span class="ion ion-md-filing"></span>&nbsp; Manage Kategori
            </a>
            @if(\UserAuth::hasAccess('Master.jenisijin','c'))
            <div @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block ml-4">
                <span class="ion ion-md-add"></span>&nbsp; Tambah Jenis Permohonan
            </div>
            @endif
        </div>
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
                        <th style="width: 50px;">No.</th>
                        <th style="min-width: 15rem;">Nama</th>
                        <th>Singkatan</th>
                        <th style="width: 100px;text-align:center">Kategori</th>
                        <th>Warna Label</th>
                        <th style="width: 200px;text-align:center">Batas Permohonan (bulanan)</th> 
                        <th style="width: 200px;text-align:center">Batas Permohonan (tahunan)</th> 
                        <th style="width: 200px;text-align:center">Rentang Waktu</th>
                        <th style="width: 200px;text-align:center" title="Tampil atau tidak jam scanner di laporan jejak kehadiran">Tampilkan <br>Waktu Scanner</th>
                        <th style="min-width: 6rem;text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item,i) in data.data">
                        <td><span v-text="i+1 + ((data.currentPage-1)*data.limit)"></span></td>
                        <td><span v-text="item.nama"></span></td>
                        <td><span v-text="item.singkatan"></span></td>
                        <td style="text-align:center"><span v-text="item.kategori?item.kategori.nama:''"></span></td>
                        <td><b class="badge" :style="'background-color: ' + item.warna">  &nbsp; </b> @{{item.warna}}</td>
                        <td style="text-align:center"><span v-text="item.batas_ijin"></span></td> 
                        <td style="text-align:center"><span v-text="item.batas_ijin_tahunan"></span></td> 
                        <td style="text-align:center"><span v-text="item.is_periode==0 ? '1 tanggal': '2 tanggal'"></span></td> 
                        <td style="text-align:center"><span v-text="item.is_show_scanner==0 ? 'Tidak': 'Ya'"></span></td> 
                        <td style="text-align:center">                            
                            <div @click="showTemplate(item.id)" class="btn btn-warning btn-xs icon-btn md-btn-flat article-tooltip" title="Preview Template Permohonan">
                                <i class="ion ion-md-eye"></i>
                            </div>
                            @if(\UserAuth::hasAccess('Master.jenisijin','u'))
                            <div @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                                <i class="ion ion-md-create"></i>
                            </div> 
                            @endif
                            @if(\UserAuth::hasAccess('Master.jenisijin','d'))
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

        <!-- modal form jenis ijin -->
        <div class="modal fade" id="formJenisIjinModal" tabindex="-1" role="dialog" aria-labelledby="formJenisIjinModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formJenisIjinModalTitle" v-text="formTitle">Jenis Permohonan Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" v-model="form.nama" placeholder="Nama">
                            </div>
                            <div class="form-group col">
                                <label class="form-label">Singkatan</label>
                                <input type="text" class="form-control" v-model="form.singkatan" placeholder="Singkatan">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Kategori</label>               
                                <select class="form-control" v-model="form.jenis_ijin_kategori_id">
                                    <option value="0">Pilih</option>
                                    <option v-for="option in jenisIjinKategori.data" :value="option.id">@{{option.nama + ' [' + option.singkatan + ']'}}</option>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label class="form-label">Warna Label</label>
                                <input id="minicolors-hue" type="text" class="form-control" v-model="form.warna" placeholder="#">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Batas Permohononan Bulanan</label>
                                <input type="number" class="form-control" v-model="form.batas_ijin" placeholder="" min="0" max="12"> 
                                <!-- <div class="slider-primary">
                                    <input id="batas_ijin" class="bs-slider-variant" type="text"  data-slider-min="0" data-slider-max="10" data-slider-step="1" 
                                    data-slider-ticks-labels='["1","2","3","4","5","6","7","8","9","10"]' data-slider-ticks="[1,2,3,4,5,6,7,8,9,10]">
                                     
                                </div> -->
                            </div>
                            <div class="form-group col">
                                <label class="form-label">Batas Permohononan Tahunan</label>
                                <input type="number" class="form-control" v-model="form.batas_ijin_tahunan" placeholder="" min="0" max="12"> 
                                
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Rentang Waktu</label>
                              
                                <div class="slider-primary">
                                     <label class="form-check form-check-inline">
                                        <input class="form-check-input" v-model="form.is_periode" type="radio" name="is_periode" value="0">
                                        <span class="form-check-label">
                                            1 Tanggal
                                        </span>
                                    </label>
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" v-model="form.is_periode" type="radio" name="is_periode" value="1">
                                        <span class="form-check-label">
                                            2 Tanggal
                                        </span>
                                    </label>
                                    
                                </div>
                            </div>

                            <div class="form-group col">
                                <label class="form-label">Tampilkan Waktu Scanner (Jejak Kehadiran)</label>
                              
                                <div class="slider-primary">
                                     <label class="form-check form-check-inline">
                                        <input class="form-check-input" v-model="form.is_show_scanner" type="radio" name="is_show_scanner" value="1">
                                        <span class="form-check-label">
                                           Ya
                                        </span>
                                    </label>
                                    <label class="form-check form-check-inline">
                                        <input class="form-check-input" v-model="form.is_show_scanner" type="radio" name="is_show_scanner" value="0">
                                        <span class="form-check-label">
                                            Tidak
                                        </span>
                                    </label>
                                    
                                </div>
                            </div>

                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Template keterangan</label>
                                <textarea id="bs-markdown"  rows="10" class="form-control" v-model="form.template_keterangan" placeholder="Template Keterangan Tidak Hadir"></textarea>
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
        <!-- modal form preview template -->
        <div class="modal fade" id="formTemplateModal" tabindex="-1" role="dialog" aria-labelledby="formTemplateModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formTemplateModalTitle" >Preview Template Permohonan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                         
                        <div class="form-row">
                            <div class="form-group col">
                               <div id="template">

                               </div>
                                 
                            </div>
                        </div>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                         
                    </div>
                </div>
            </div>
        </div>
        <!-- end preview modal -->
    </div>
</div>
@endsection