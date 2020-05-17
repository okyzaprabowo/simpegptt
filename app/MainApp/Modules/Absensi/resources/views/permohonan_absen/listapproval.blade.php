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
    <script src="{{ asset('/webdist/vendor/libs/bootbox/bootbox.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = '{{config('AppConfig.endpoint.api.Absensi')}}/permohonan/approve';
        var apiPath2 = '{{config('AppConfig.endpoint.api.Absensi')}}/permohonan/approval';
        var apiJeniIjinPath = '{{config('AppConfig.endpoint.api.Master')}}/jenis_ijin';
        var vueListPermohonanAbsen = new Vue({
            el: '#vueListPermohonanAbsen',
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
                   pageCount: {{$pageCount}},
                   alasans:[]
                },
                
                isAdd: true,
                form: {
                    id:0,
                    pegawai_id: '',
                    ijin_id: '',
                    tanggal:'',
                    waktu_mulai:'',
                    waktu_selesai:'',
                    keterangan: '',
                    is_periode:0,
                },
                formEmpty: {
                    id:0,
                    pegawai_id: '',
                    ijin_id: '',
                    tanggal:'',
                    waktu_mulai:'',
                    waktu_selesai:'',
                    keterangan: '',
                    is_periode:0,
                }
            },
            created: function() {
                this.form = JSON.parse(JSON.stringify(this.formEmpty));
                console.log(this.data);
            },
            mounted: function() {                   
                $('.select2-select').select2();                
                //  $('.datepicker-base').datepicker({format: 'yyyy-mm-dd'});
                
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
                formatText(idx,template,value) {
                    if (template==null) template = '';
                    template = template.replace(/\r\n/g, "\n");
                   

                 //   template = template.replace(/<<INPUT>>/g, '<p class="font-weight-semibold item_alasan"></p>');
                //    template = template.replace(/<<INPUT>>/g, '<input type="text" readonly name="alasan[]" class="form-control item_alasan">');
                                var alasan=value;
                                var temp='';
                                if ((alasan!="")&&(alasan != null)){
                                    alasan=alasan.split('~');
                                    var i=0;
                                    count = 0,
                                    pos = template.indexOf("<<INPUT>>");
                                    // ES5 without the for loop:
                                        arrValues = template.split('<<INPUT>>');
                                       
                                        idx = 0;
                                        $.each(arrValues, function (intIndex, objValue) {
                                         //  console.log(objValue);
                                            // if (objValue=="<<INPUT>>"){
                                            //     console.log("kadie");
                                            //     objValue = alasan[idx];
                                            //     idx++;
                                            // }
                                            let als = '';
                                            if ((alasan[idx]== null)&&(alasan[idx]==undefined)) als = '';
                                            else if (alasan[idx]=="") als='';
                                            else als = '<p class="font-weight-semibold item_alasan"><u><mark>'+alasan[idx]+'</mark></u></p>';;//alasan[idx];
                                            temp += objValue+als;
                                            idx++;
                                        });
                                        template =  temp;
                                   //   console.log(temp);
                                   
                                            //console.log(alasan);
                                          /*  $("#div-template-"+idx+' p').each(function(i){
                                             console.log(i)  ;
                                             i++;
                                           }); */

                                     /*   $('#div-template-'+idx+' input.item_alasan').each(function() {
                                             console.log(alasan[i]);
                                            $(this).val(alasan[i]);
                                            i++;
                                            
                                        });                   */
                                    
                                  /*   while (pos > -1) {
                                        template.replace(/<<INPUT>>/g,'<p class="font-weight-semibold item_alasan">'+alasan[count]+'</p>');
                                        ++count;
                                        template.indexOf("<<INPUT>>");
                                        console.log(pos);
                                    }  */
                                       

                                   // console.log(i);  
                                    // $('input.item_alasan').each(function() {
                                           
                                    //         $(this).val(alasan[i]);
                                    //         i++;
                                            
                                    //     });    
                                }
                   
                    return template;
                },
                formatDate(value) {
                    if (value) {
                        return moment(String(value)).format('DD MMMM YYYY');
                    }
                },
                
                saveData(data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.post(apiPath,data).then((res)=>{
                        showAlert({text: "Data saved"});
                        $('#formPermohonanAbsenModal').modal('hide');
                        this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Save data failed",type: "warning"});
                    });
                },
            
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
                approveItem(id,tipe_approve) {                    
                    showAlert({
                        styleType: "modal",
                        style: "warning",
                        title: "Konfirmasi Approval",
                        text: "Apakah anda yakin ?",
                        modalButtonCancel: "Batal",
                        modalButtonOk: "Ya",
                        onOk:()=>{  
                            var alasanTolak = '';
                            if (tipe_approve=="2"){ //jika tolak
                               // alasanTolak = prompt("Alasan Penolakan", "");
                               bootbox.prompt({
                                    title: 'Alasan penolakan',

                                    callback: function(result) {
                                        console.log(result);
                                        // return false;
                                        if (result === null) {
                                            alasanTolak='-';//alert('Prompt dismissed');
                                        } else {
                                            alasanTolak = result;//alert('Hi ' + result + '!');
                                            axios.put(apiPath + "/" + id,{approve_status:tipe_approve,approve_desc:alasanTolak})
                                            .then((res)=>{
                                                showAlert({text: "Data permohonan Berhasil diapprove."});
                                                vueListPermohonanAbsen.loadList(vueListPermohonanAbsen.curPage,vueListPermohonanAbsen.searchString,vueListPermohonanAbsen.sortBy,vueListPermohonanAbsen.sortAsc);
                                            }).catch((res)=>{
                                                showAlert({text: "Approval gagal : " + res.message,type: "warning"});
                                            });
                                        }
                                       
                                    },
                                    });
                                
                            } else {

                          
                                axios.put(apiPath + "/" + id,{approve_status:tipe_approve,approve_desc:alasanTolak})
                                    .then((res)=>{
                                        showAlert({text: "Data permohonan Berhasil diapprove."});
                                        //  this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                                        vueListPermohonanAbsen.loadList(vueListPermohonanAbsen.curPage,vueListPermohonanAbsen.searchString,vueListPermohonanAbsen.sortBy,vueListPermohonanAbsen.sortAsc);
                                    }).catch((res)=>{
                                        showAlert({text: "Approval gagal : " + res.message,type: "warning"});
                                    });
                            }
                        }
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
                    this.loadParams.params.approve_status = 0;
                    axios.get(apiPath2 , this.loadParams)
                        .then((res)=>{
                            this.data = res.data.data;
                            console.log(this.data);
                        }).catch((res)=>{
                            showAlert({text: "Load Data Gagal : " + res.message,type: "warning"});
                        });
                }
            }
        });
     
           

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
                                showAlert({text: "Load Data Gagal Template Ijin belum ditentukan : ",type: "warning"});
                            });
            });
        });
    </script>
@endsection


@section('content')
<div id="vueListPermohonanAbsen">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-1">
        <div><span class="text-muted font-weight-light">Dashboard /</span> {{$page_title}}</div>
        
    </h4>
    @include("alert")
    <div class="card mt-5"  v-for="(item,i) in data.data"  v-if="data.count>0">        
        <div class="p-4 p-md-x5">
            <span class="text-body text-large font-weight-semibold" v-text="item.pegawai.nama"></span>
            
            <div class="d-flex flex-wrap mt-3">
                <div class="mr-3"><i class="vacancy-tooltip ion ion-md-medal text-light" title="Jabatan"></i>&nbsp; <span v-text="item.pegawai.jabatan.nama"></span></div>
                <div class="mr-3"><i class="vacancy-tooltip ion ion-md-business text-light" title="Unit Kerja"></i>&nbsp; <span v-text="item.pegawai.instansi?item.pegawai.instansi.nama:''"></span></div>
                <div class="mr-3"><i class="vacancy-tooltip ion ion-md-time text-primary" title="Tanggal Pengajuan"></i>&nbsp; <span v-text="formatDate(item.tanggal)"></span></div>
                <div class="mr-3"><i class="vacancy-tooltip ion ion-md-bookmark text-danger" title="Jenis Permohonan"></i>&nbsp; <span v-text="item.jenis_ijin.nama"></span></div>
            </div>
            <div class="mt-3 mb-4"   v-html="'Waktu permohonan untuk tanggal : <u><mark>'+formatDate(item.waktu_mulai) + (item.jenis_ijin.is_periode==1?' s.d '+formatDate(item.waktu_selesai):'')+'</mark></u>'"></div>
            <div class="mt-3 mb-4" v-bind:id="'div-template-'+i" v-html="formatText(i,item.template,item.keterangan)">
                
            </div>
            <button type="button" class="btn btn-primary rounded-pill" @click="approveItem(item.id,1)" >Approve</button>
            <button type="button" class="btn btn-danger rounded-pill" @click="approveItem(item.id,2)" >Tolak</button>
        </div>

        <hr class="border-light m-0">

    </div>    

    <div class="card mt-5"  v-if="data.count==0">    
     <span class="text-body text-large font-weight-semibold" >Tidak ada data permohonan yang harus ditindaklanjuti untuk user ini.</span>
    </div>    
       

  
</div>
@endsection