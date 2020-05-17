@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/timepicker/timepicker.css') }}">
     <style type="text/css" >
        .bgg-info{
            background-color:#f5f5f5;
        }
        .bgg-info-2{
            background-color:#e1e6e6;
        }
     </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>     -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script> -->
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/timepicker/timepicker.js') }}"></script>
    <script>   
        var apiPath = "{{config('AppConfig.endpoint.api.Master')}}/shift/detail";
        var vueListShiftDetail = new Vue({
            el: '#vueListShiftDetail',
            data: {
                shifId: {{ $shift['id'] }},
                
             
                
                data: {
                   data: {!!json_encode($shift['shift_detail'])!!},
                  
                },
                isAddReguler : true,
                isAddRamadhan :true,
                loadParams: {
                    params: {}
                },
                form_reguler :{},
                form_ramadhan :{},
                formEmpty: {
                    id:0,
                    tipe: '',
                    senin_masuk:'',
                    senin_pulang:'',
                    selasa_masuk:'',
                    selasa_pulang:'',
                    rabu_masuk:'',
                    rabu_pulang:'',
                    kamis_masuk:'',
                    kamis_pulang:'',
                    jumat_masuk:'',
                    jumat_pulang:'',
                    sabtu_masuk:'',
                    sabtu_pulang:'',
                    minggu_masuk:'',
                    minggu_pulang:'',
                    range_awal:'',
                    range_akhir:''
                   
                }

            },
            created: function() {
                this.form_reguler = JSON.parse(JSON.stringify(this.formEmpty));
                this.form_reguler.tipe = 0;
                this.form_ramadhan = JSON.parse(JSON.stringify(this.formEmpty));
                this.form_ramadhan.tipe = 1;
            },
            mounted: function() {   
                var that = this;   
                 console.log(this.data.data);      
                if (this.data.data.length>0)      {
                    if (this.data.data[0]!=undefined){
                        if (this.data.data[0].tipe=="0"){
                            this.isAddReguler=false;
                            this.form_reguler = JSON.parse(JSON.stringify(this.data.data[0]));
                        } else{
                            this.isAddRamadhan=false;
                            this.form_ramadhan = JSON.parse(JSON.stringify(this.data.data[0]));
                            var awal = moment(String(this.form_ramadhan.range_awal),'YYYY-MM-DD').format('DD-MM-YYYY');
                            var akhir = moment(String(this.form_ramadhan.range_akhir),'YYYY-MM-DD').format('DD-MM-YYYY');
                            this.form_ramadhan.range_awal = awal;
                            this.form_ramadhan.range_akhir = akhir;
                            // $('#range_awal')
                            //     .datepicker({format: 'dd-mm-yyyy',autoclose: true})
                            //     .datepicker('setDate', this.form_ramadhan.range_awal);
                            // $('#range_akhir')
                            //     .datepicker({format: 'dd-mm-yyyy',autoclose: true})
                            //     .datepicker('setDate', this.form_ramadhan.range_akhir);    
                        }
                    }
                    if (this.data.data[1]!=undefined){
                        if (this.data.data[1].tipe=="0"){
                            this.isAddReguler=false;
                            this.form_reguler = JSON.parse(JSON.stringify(this.data.data[1]));
                        } else{
                            this.isAddRamadhan=false;
                            this.form_ramadhan = JSON.parse(JSON.stringify(this.data.data[1]));
                            // console.log(moment(String(this.form_ramadhan.range_awal),'YYYY-MM-DD').format('DD-MM-YYYY'));
                            var awal = moment(String(this.form_ramadhan.range_awal),'YYYY-MM-DD').format('DD-MM-YYYY');
                            var akhir = moment(String(this.form_ramadhan.range_akhir),'YYYY-MM-DD').format('DD-MM-YYYY');
                            this.form_ramadhan.range_awal = awal;
                            this.form_ramadhan.range_akhir = akhir;
                            console.log(awal);
                            // $('#range_awal').val(this.form_ramadhan.range_awal);
                            // $('#range_awal') //.val(moment(String(this.form_ramadhan.range_awal),'YYYY-MM-DD').format('DD-MM-YYYY'));
                               // .datepicker({format: 'dd-mm-yyyy',autoclose: true})
                                // .datepicker('setDate', this.form_ramadhan.range_awalwal);
                                //'YYYY-MM-DD'
                            // $('#range_akhir')
                            //     .datepicker({format: 'dd-mm-yyyy',autoclose: true})
                            //     .datepicker('setDate', this.form_ramadhan.range_akhir);    
                        }
                    }
                }
                // $('.select2-select').select2();  
                // $('.datepicker-base').datepicker({format: 'yyyy-mm-dd'});
                
            },
            watch: {
                 
            },
            methods: {             
                formRegulerSubmitted(ev) {
                  //  console.log(ev);
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
                    // this.form.shift_id = this.data.shifId;
                   
                    var data = this.form_reguler;
                    data.shift_id = this.shifId;  
                    // console.log(data);
                    
                    if(this.form_reguler.id==0){
                        this.saveData(data);
                    }else{
                        this.updateData(data.id, data);
                    }
                },
                formRamadhanSubmitted(ev) {
                    console.log(ev);
                    ev.preventDefault();
                    // if(!this.hasAccess){
                    //     this.Web.showAlert({text: "Akses Manage Ditolak", type: "warning"});
                    //     return false;
                    // }
                    // this.form.shift_id = this.data.shifId;
                  
                    var data = this.form_ramadhan;
                    data.shift_id = this.shifId;  
                    // console.log(data);
                    data.range_awal =  moment($("#range_awal").val(),'DD-MM-YYYY').format('YYYY-MM-DD');
                    data.range_akhir =  moment($("#range_akhir").val(),'DD-MM-YYYY').format('YYYY-MM-DD');
                      
                    
                    
                    if(this.form_ramadhan.id==0){
                        this.saveData(data);
                    }else{
                        this.updateData(data.id, data);
                    }
                },
                saveData(data) {
                // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.post( apiPath,data).then((res)=>{
                      //  console.log(res);
                        showAlert({text: "Data saved"});
                        if (res.data.data.tipe==1){
                            this.form_ramadhan.id = res.data.data.id;
                            var awal = moment(String(res.data.data.range_awal),'YYYY-MM-DD').format('DD-MM-YYYY');
                            var akhir = moment(String(res.data.data.range_akhir),'YYYY-MM-DD').format('DD-MM-YYYY');
                            this.form_ramadhan.range_awal =  awal;
                            this.form_ramadhan.range_akhir =  akhir;
                        }else{
                            this.form_reguler.id = res.data.data.id;
                        }
                        // $('#formPermohonanAbsenModal').modal('hide');
                        // this.form = JSON.parse(JSON.stringify(this.formEmpty));
                        // this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        console.log(res);
                        showAlert({text: "Save data failed : "+(res.message),type: "warning"});
                    });
                },
            
                updateData(id, data) {
                    // data.harga_ssh = parseFloat(data.harga_ssh.replace(/[^0-9,]/g, "").replace(',','.')) ;
                    axios.put(apiPath + '/' + id,data).then((res)=>{
                        showAlert({text: "Data updated"});
                        // console.log(res);
                        if (data.tipe==1){
                           
                            var awal = moment(String(data.range_awal),'YYYY-MM-DD').format('DD-MM-YYYY');
                            var akhir = moment(String(data.range_akhir),'YYYY-MM-DD').format('DD-MM-YYYY');
                            this.form_ramadhan.range_awal =  awal;
                            this.form_ramadhan.range_akhir =  akhir;
                        }
                        // $('#formPermohonanAbsenModal').modal('hide');
                        // this.loadList(this.curPage,this.searchString,this.sortBy,this.sortAsc);
                    }).catch((res)=>{
                        showAlert({text: "Update data failed",type: "warning"});
                    });
                },
            } 
        });

        $('.jam_masuk').timepicker({
                    'step': 15,
                    timeFormat:'H:i',
                    zindex:9999,
                    defaultTime:'08:00',
                    scrollDefault:'08:00',
                    orientation: 'l'
                });
        $('.jam_keluar').timepicker({
                    'step': 15,
                    timeFormat:'H:i',
                    zindex:9999,
                    defaultTime:'16:00',
                    scrollDefault:'16:00',
                    orientation: 'l'
                });

                $('.periode').datepicker({
                        orientation:  'auto left',
                        autoclose:true,    
                        format: 'dd-mm-yyyy',
                         
                        zIndexOffset:9999
                    });                   
    </script>
@endsection

@section('content')
<div id="vueListShiftDetail">
    <h4 class="font-weight-bold py-3 mb-4">
        <span class="text-muted font-weight-light">Setup / <a href="{{route('master.shift.list')}}"> Shift</a> / </span> Detail Shift {{$shift['nama']}}
    </h4>

    @include("alert")

    <div class="card">

    

        <div class="card-body">
            <h4 class="card-title">Waktu Normal (Kosongkan waktu jika libur)</h4>  
            
            <div class="form-row">
                <div class="form-group col-md-3 bgg-info pb-2">
                    <label class="label-control">Senin</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" >Masuk </span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_reguler.senin_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.senin_masuk =  this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_reguler.senin_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.senin_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                <div class="form-group col-md-3 bgg-info-2">
                    <label class="label-control">Selasa</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk"  v-model="form_reguler.selasa_masuk" placeholder="" 
                                onchange="vueListShiftDetail.form_reguler.selasa_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar"  v-model="form_reguler.selasa_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.selasa_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->

                <div class="form-group col-md-3 bgg-info">
                    <label class="label-control">Rabu</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_reguler.rabu_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.rabu_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_reguler.rabu_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.rabu_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                <div class="form-group col-md-3 bgg-info-2">
                    <label class="label-control">Kamis</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_reguler.kamis_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.kamis_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_reguler.kamis_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.kamis_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                
            </div><!-- form-row-->

            <div class="form-row">
                <div class="form-group col-md-3 bgg-info pb-2">
                    <label class="label-control">Jumat</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_reguler.jumat_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.jumat_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_reguler.jumat_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.jumat_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                <div class="form-group col-md-3 bgg-info-2">
                    <label class="label-control">Sabtu</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_reguler.sabtu_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.sabtu_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_reguler.sabtu_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.sabtu_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->

                <div class="form-group col-md-3 bgg-info">
                    <label class="label-control">Minggu</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_reguler.minggu_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.minggu_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_reguler.minggu_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_reguler.minggu_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
               
                
            </div><!-- form-row-->
            <div class="row">
                    <div class="col text-sm-right">        
                        <button type="button" class="btn btn-primary " @click="formRegulerSubmitted">Save</button>
                    </div>
                </div>
        </div>  <!-- card-body-->   
         
     

<!-- =============================================PISAH form aja lah biar cepet utk ramadhan ----------->
 
        <div class="card-body" style="margin-top: -30px;/*background-color: aliceblue;*/">
            <h4 class="card-title">Waktu Ramadhan  (Kosongkan waktu jika libur)</h4>
            <div class="form-row">
                <div class="form-group col-md-5 bgg-info pb-2">
                    <label class="label-control">Berlaku</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Dari</span>
                                </div>
                                <input type="text" name="range_awal" id="range_awal" v-model="form_ramadhan.range_awal"  class="form-control periode "
                                onchange="vueListShiftDetail.form_ramadhan.range_awal = this.value" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Sampai</span>
                                </div>
                                <input type="text" class="form-control periode" name="range_akhir" id="range_akhir" v-model="form_ramadhan.range_akhir" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.range_akhir = this.value"  autocomplete="off" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3 bgg-info pb-2">
                    <label class="label-control">Senin</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.senin_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.senin_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.senin_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.senin_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                <div class="form-group col-md-3 bgg-info-2">
                    <label class="label-control">Selasa</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.selasa_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.selasa_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.selasa_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.selasa_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->

                <div class="form-group col-md-3 bgg-info">
                    <label class="label-control">Rabu</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.rabu_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.rabu_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.rabu_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.rabu_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                <div class="form-group col-md-3 bgg-info-2">
                    <label class="label-control">Kamis</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.kamis_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.kamis_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.kamis_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.kamis_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                
            </div><!-- form-row-->

            <div class="form-row">
                <div class="form-group col-md-3 bgg-info pb-2">
                    <label class="label-control">Jumat</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.jumat_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.jumat_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.jumat_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.jumat_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                <div class="form-group col-md-3 bgg-info-2">
                    <label class="label-control">Sabtu</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.sabtu_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.sabtu_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.sabtu_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.sabtu_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->

                <div class="form-group col-md-3 bgg-info">
                    <label class="label-control">Minggu</label>
                    <div class="row">
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Masuk</span>
                                </div>
                                <input type="text" class="form-control jam_masuk" v-model="form_ramadhan.minggu_masuk" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.minggu_masuk = this.value">
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Pulang</span>
                                </div>
                                <input type="text" class="form-control jam_keluar" v-model="form_ramadhan.minggu_pulang" placeholder=""
                                onchange="vueListShiftDetail.form_ramadhan.minggu_pulang = this.value">
                            </div>
                        </div>
                    </div> <!-- row -->
                </div><!-- form-group  col-md-3 -->
                
            </div><!-- form-row-->
            <div class="row">
                <div class="col text-sm-right">             
                    <button type="button" class="btn btn-primary"  @click="formRamadhanSubmitted" >Save Shift Ramadhan</button>
                </div>
            </div>
        </div>     
         
        
     

    </div>
</div>
@endsection