@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <style type="text/css">
         .importantRule{
            z-index:1600 !important;
         }  
         .select2-container--open {
            z-index: 9999999
        }
        .default-style .datepicker-dropdown{
            z-index:9999 !important;
         } 
    </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script> -->
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = '{{config('AppConfig.endpoint.api.Absensi')}}/permohonan';
        var apiJeniIjinPath = '{{config('AppConfig.endpoint.api.Master')}}/jenis_ijin';
        var vueListPermohonanAbsen = new Vue({
            el: '#vueListPermohonanAbsen',
            data: {
                formTitle: 'Tambah Baru',

                sortBy: "waktu_mulai",
                sortDesc: false,
                searchString: "{{$filter['q']}}",
                curPage: 1,
                perPage: {{$limit}},
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
                   pageCount: {{$pageCount}},
                   alasans:[]
                },
                
                isAdd: true,
                form: {
                    id:0,
                    pegawai_id: '',
                    pegawai: {
                        nama: ''
                    },
                    ijin_id: '',
                    tanggal:'',
                    waktu_mulai:'',
                    waktu_selesai:'',
                    keterangan: '',
                    template: '',
                    is_periode:0,
                },
                formEmpty: {
                    id:0,
                    pegawai_id: '',
                    pegawai: {
                        nama: '{{ \UserAuth::user('name') }}'
                    },
                    ijin_id: '',
                    tanggal:'',
                    waktu_mulai:'',
                    waktu_selesai:'',
                    keterangan: '',
                    template: '',
                    is_periode:0,
                }
            },
            created: function() {
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },
            mounted: function() {                   
                $('.select2-select').select2();                
                //  $('.datepicker-base').datepicker({format: 'yyyy-mm-dd'});
                var awal = moment(String(this.form.waktu_mulai),'YYYY-MM-DD').format('DD-MM-YYYY');
                            var akhir = moment(String(this.form.waktu_selesai),'YYYY-MM-DD').format('DD-MM-YYYY');
                            // this.form.waktu_mulai = awal;
                            // this.form.waktu_selesai = akhir;
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
                formatDate(value) {
                    if (value) {
                        return moment(String(value)).format('DD MMMM YYYY');
                    }
                },
                showForm(isAdd=true,id=0) {                  
                    this.isAdd = isAdd;
                    if(!isAdd){       
                        this.formTitle = 'Edit Data';
                        axios.get(apiPath + '/' + id)
                            .then((res)=>{
                                this.form = res.data.data;
                                // $('#ijin_id').select2('val',this.form.ijin_id);
                                $('#ijin_id').val(this.form.ijin_id).trigger('change');
                                //template.replace(/<<INPUT>>/g, '<br><input type="text" name="alasan[]" class="form-control item_alasan"><br>')
                                $("#template").html(this.form.jenis_ijin.template_keterangan.replace(/<<INPUT>>/g, '<br><input type="text" name="alasan[]" class="form-control item_alasan"><br>'));
                                this.form.template = this.form.jenis_ijin.template_keterangan;
                                $("#is_periode").val(this.form.jenis_ijin.is_periode);
                                var waktu_mulai = moment(String(this.form.waktu_mulai),'YYYY-MM-DD').format('DD-MM-YYYY');
                                var waktu_selesai = moment(String(this.form.waktu_selesai),'YYYY-MM-DD').format('DD-MM-YYYY');
                                this.form.waktu_mulai = waktu_mulai;
                                this.form.waktu_selesai = waktu_selesai;
                                var alasan=this.form.keterangan;
                                if ((alasan!="")&&(alasan!=null)&&(alasan!=undefined)){
                                    alasan=alasan.split('~');
                                    var i=0;
                                    $('input.item_alasan').each(function() {
                                            // console.log(alasan[i]);
                                            $(this).val(alasan[i]);
                                            i++;
                                            
                                        });    
                                }
                                
                                $('#formPermohonanAbsenModal').modal('show');
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                    }else{    
                        this.formTitle = 'Tambah Data';  
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        $('#formPermohonanAbsenModal').modal('show');
                    }
                },
                formSubmitted(ev) {
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
                    this.form.ijin_id = $('#ijin_id').val();
                    // console.log(this.alasans);
                    var alasan='';
                    $('input.item_alasan').each(function() {
                        alasan +=$(this).val()+'~';
                     });
                    this.form.keterangan = alasan;//$("input[name=alasan[]]").val(); ;//JSON.stringify(this.alasans);//$('#template').html();
                    var data = this.form;
                     
                    // data.waktu_mulai =  moment($("#waktu_mulai").val(),'DD-MM-YYYY').format('YYYY-MM-DD');
                    if (  $("#is_periode").val()=="1"){
                        // data.waktu_selesai =  moment($("#waktu_selesai").val(),'DD-MM-YYYY').format('YYYY-MM-DD');
                    }else{
                        data.waktu_selesai = data.waktu_mulai;

                    }
                    
                    
                    
                    if(this.isAdd){
                        this.saveData(data);
                    }else{
                        this.updateData(this.form.id, data);
                    }
                  
                },
                saveData(data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.post(apiPath,data).then((res)=>{
                        showAlert({text: "Data saved"});
                        $('#formPermohonanAbsenModal').modal('hide');
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        // var awal = moment(String(res.data.data.waktu_mulai),'YYYY-MM-DD').format('DD-MM-YYYY');
                        //     var akhir = moment(String(res.data.data.waktu_selesai),'YYYY-MM-DD').format('DD-MM-YYYY');
                        // this.form.waktu_mulai =awal;
                        // this.form.waktu_selesai = akhir;
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: res.message,type: "warning"});
                    });
                },
             /*   showTemplate(id) {  
                    axios.get(apiPath + '/' + id)
                            .then((res)=>{
                               // this.form = res.data.data;
                                var template = res.data.data.template_keterangan;
                                $("#template").html(template.replace(/<<INPUT>>/g, '<br><input type="text" name="alasan[]"   class="form-control"><br>'));
                                //this.form.template_keterangan);
                           //     $('#formTemplateModal').modal('show');
                            }).catch((res)=>{
                                showAlert({text: "Load Data Gagal : ",type: "warning"});
                            });
                },   */
                updateData(id, data) {
                    // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        $('#formPermohonanAbsenModal').modal('hide');
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
                                    showAlert({text: "Data permohonan Berhasil dihapus."});
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
                    window.location.href = "{{route('permohonan_absen.index')}}?" + params(this.loadParams.params);
                    // axios.get(apiPath , this.loadParams)
                    //     .then((res)=>{
                    //         this.data = res.data.data;
                    //         // console.log(this.data);
                    //     }).catch((res)=>{
                    //         showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                    //     });
                }
            }
        });
    /*       
        $('#daterange-2').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
              //  orientation:"top",
              drops:'up',
                locale: {
                format: 'MM/DD/YYYY h:mm A'
                },
                opens: 'right'//(isRtl ? 'left' : 'right')
            });
*/
           

        $(document).ready(function(){
            $('#formPermohonanAbsenModal').on('shown.bs.modal', function() {
            //    $('.daterangepicker').css('z-index','1600','important');
              // $('#daterange-2').addClass('importantRule');
            //   var origStyleContent = $('.daterangepicker').attr('style');
          //  origStyleContent = 'top:0px !important;right:auto;left:428px;'
                // $('.daterangepicker').attr('style', origStyleContent + ';z-index:1600 !important;top:0px !important;');
            }); 
            $('.periode').datepicker({
                        orientation:  'auto left',
                        autoclose:true,    
                        format: 'dd-mm-yyyy',
                        language:'id',
                            /*{
                            toDisplay : function(date,format,language){
                                date.format = 'dd-mm-yyyy';
                                date.languange = 'id';
                                return date;
                            },
                            toValue : function(date,format,language){
                                date.format = 'yyyy-mm=dd';
                                date.languange = 'id';
                                return date;
                            }
                        }, */
                        zIndexOffset:9999
                    });    
            $('#ijin_id').on('select2:select', function (e) {
                axios.get(apiJeniIjinPath + '/' + $(this).val())
                            .then((res)=>{
                               // this.form = res.data.data;
                                var template = res.data.data.template_keterangan;
                                var is_periode = res.data.data.is_periode == "1";
                                 vueListPermohonanAbsen.form.template = template;
                                //  console.log(vueListPermohonanAbsen.form.template);
                                $("#is_periode").val(res.data.data.is_periode);
                                $("#lbl-sd").hide();
                                $("#waktu_selesai").hide();
                                if (is_periode){
                                    $("#lbl-sd").show();
                                    $("#waktu_selesai").show();
                                }
                                $("#template").html(template.replace(/<<INPUT>>/g, '<br><input type="text" name="alasan[]" class="form-control item_alasan"><br>'));
                                // $("#template").html(' <div v-for="(alasan, index) in alasans">'+
                                // template.replace(/<<INPUT>>/g, '<br><input type="text" name="alasan[]" v-model="alasan.value" class="form-control"><br>'
                                // )+'</div>');
                                //this.form.template_keterangan);
                           //     $('#formTemplateModal').modal('show');
                            }).catch((res)=>{
                                $("#template").empty();
                                vueListPermohonanAbsen.form.template = '';
                                showAlert({text: "Load Data Gagal Template Ijin belum ditentukan : ",type: "warning"});
                            });
            });
        });
    </script>
@endsection


@section('content')
<div id="vueListPermohonanAbsen">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Absensi /</span> {{$page_title}}</div>
        @if( $is_approval=="0" )
        @if(\UserAuth::hasAccess('Absensi.permohonan','c'))
        <div v-if="{{$is_approval==='0'}}" @click="showForm(true,0)" class="btn btn-primary rounded-pill d-block">
            <span class="ion ion-md-add"></span>&nbsp; Tambah Permohonan Ketidakhadiran {{$is_approval}}
        </div>
        @endif
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
        <?php
        $isPegawaiPtt = \UserAuth::is('pegawai_ptt')?true:false; 
        ?>
        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
            @if( $is_approval=="0" )
                <thead>
                    <tr>
                        <th style="width: 20px">No.</th>
                        @if (!$isPegawaiPtt)
                        <th style="min-width:0.5rem;">Nama Pegawai</th>
                        @endif

                        <th style="min-width:0.5rem;">Jenis Permohonan</th>
                        <th class="text-center" style="width: 200px;">Tanggal Permohonan</th> 
                        <th class="text-center" style="width: 350px;">Waktu Permohonan</th>
                        <!-- <th>Keterangan</th> -->
                        <th class="text-center" style="width: 10px;">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <tr v-for="(item,i) in data.data">
                    <td><span v-text="i+1 + ((data.currentPage-1)*data.limit)"></span></td> 
                    @if (!$isPegawaiPtt)
                    <td><span v-text="item.pegawai.nama"></span></td>
                    @endif
                    <td><span v-text="item.jenis_ijin.nama"></span></td>
               
                    <td class="text-center"><span v-text="formatDate(item.tanggal)"></span></td>
                    <td class="text-center">
                        <span v-if="item.jenis_ijin.is_periode==1" v-text="formatDate(item.waktu_mulai) +' s.d '+ formatDate(item.waktu_selesai)"></span>
                        <span v-else v-text="formatDate(item.waktu_mulai)"></span>
                        </td>
                    <!-- <td>
                        <span v-if="item.ijin.nama"
                    </td> -->
                    <!-- <td><span v-text="item.keterangan"></span></td> -->
                    <td class="text-center">
                        <span class="badge badge-success" v-if="item.approve_status==0">Baru </span>
                        <span class="badge badge-info" v-else-if="item.approve_status==1">Disetujui </span>
                        <span class="badge badge-danger" v-bind:title="item.approve_desc" v-else>Ditolak</span>
                    </td>
                    <td class="text-center">
                    <!-- request pa 26 maret 2020 
                        Pak mungkin bisa dibuka sipeg pns, sebagai contoh bisa edit walau sudah approve pak
                        : trisna Muhun pak,  dinten jumat kamari abdi disauran ibu nova dr kepegawaian hoyong na kitu, supados teu robah rubih deui. -->
                        
                        @if(\UserAuth::hasAccess('Absensi.permohonan','u'))
                        <div  @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                            <i class="ion ion-md-create"></i>
                        </div> 
                        @endif
                        @if(\UserAuth::hasAccess('Absensi.permohonan','d'))
                        <div  @click="deleteItem(item.id)"  class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                            <i class="ion ion-md-close"></i>
                        </div>
                        @endif
                        <!-- <div v-if="item.approve_status==0" @click="showForm(false,item.id)" class="btn btn-success btn-xs icon-btn md-btn-flat article-tooltip" title="Edit">
                            <i class="ion ion-md-create"></i>
                        </div> 
                        @if (!$isPegawaiPtt)
                            <div   @click="deleteItem(item.id)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                                <i class="ion ion-md-close"></i>
                            </div>
                        @else
                        <div v-if="item.approve_status==0" @click="deleteItem(item.id)" class="btn btn-danger btn-xs icon-btn md-btn-flat article-tooltip" title="Remove">
                            <i class="ion ion-md-close"></i>
                        </div>
                        @endif -->
                    </td>  
                          
                   </tr>
                   
                </tbody>
                @endif
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

        <!-- modal form PermohonanAbsen -->
        <div class="modal fade" id="formPermohonanAbsenModal" tabindex="-1" role="dialog" aria-labelledby="formPermohonanAbsenModalTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formPermohonanAbsenModalTitle" v-text="formTitle">PermohonanAbsen Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Pemohon</label>
                                <input type="text" v-model="form.pegawai.nama" class="form-control" readonly value="{{ \UserAuth::user('name') }}" placeholder="Nama"  {{  $errors->has('nama')?' is-invalid':''}} >
                                <input type="hidden" class=""  name="is_periode" id="is_periode" value=""    >
                                <div  id="error-nama" class="invalid-tooltip"></div>
                            
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Jenis Permohonan</label>
                                <select class="select2-select" id="ijin_id" name="ijin_id" v-model="form.ijin_id"   data-allow-clear="true"  {{  $errors->has('ijin_id')?' is-invalid':''}} 
                                onchange="vueListPermohonanAbsen.form.ijin_id = this.value">

                                    <option value="">Pilih Jenis Permohonan</option>
                                    @foreach($ijin['data'] as $val)
                                    <option value="{{$val['id']}}"  >{{$val['nama']}}</option>
                                    @endforeach
                                </select>
                                <div  id="error-ijin_id" class="invalid-tooltip"></div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Waktu</label>
                                <input type="text" id="waktu_mulai" name="waktu_mulai" v-model="form.waktu_mulai" class="form-control periode importantRule"
                                onchange="vueListPermohonanAbsen.form.waktu_mulai = this.value">
                            </div>
                            <div class="form-group col">
                                <label class="form-label" id="lbl-sd">Sampai dengan</label>
                                <input type="text" id="waktu_selesai"  name="waktu_selesai" v-model="form.waktu_selesai" class="form-control periode importantRule"
                                onchange="vueListPermohonanAbsen.form.waktu_selesai = this.value">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label class="form-label">Keterangan</label>
                                
                                <div id="template">
                                </div>
                                 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        @if( $is_approval=="0" )
                        <button type="button" class="btn btn-primary" @click="formSubmitted">Save</button>
                        @endif
                        @if( $is_approval=="1" )
                        <button type="button" class="btn btn-primary" @click="formSubmitted">Approved</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection