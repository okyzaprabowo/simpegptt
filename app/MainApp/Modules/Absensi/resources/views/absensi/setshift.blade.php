@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
    
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <style>
        .bg-warning-lighter {
            background-color: rgba(255,217,80,0.5);
        }
        .bg-danger-lighter {
            background-color: rgba(217,83,79,0.5);
        }
        .bg-success-lighter {
            background-color: rgba(2,179,113,0.5);
        }

        
        .btn-info-lighter {
            background-color: rgba(40,195,215,0.2);
            cursor:unset;
        }
        .btn-danger-lighter {
            background-color: rgba(217,83,79,0.2);
            cursor:unset;
        }

        .bg-warning-lighter.done {
            background-color: rgba(255,217,80,0.2) !important;
        }
        .bg-warning.done {
            background-color: rgba(255,217,80,0.5) !important;
        }
        .bg-danger-lighter.done {
            background-color: rgba(217,83,79,0.2) !important;
        }
        .bg-danger.done {
            background-color: rgba(217,83,79,0.5) !important;
        }
        .bg-success-lighter.done, .bg-success.done {
            background-color: rgba(2,179,113,0.2) !important;
        }

        .day-cols {
            width: 65px;
            max-width: 65px;
        }

        .popover-body ul {
            padding-left: 10px; margin-bottom: 0;
        }

        .wrapper1 {
            height: 20px;
            overflow-x: auto;
            overflow-y:hidden;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 9999;
        }
        .div1 {
            display: block;
            width:1000px;
            height: 20px;
        }
    </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <!-- <script src="{{ asset('/webdist/vendor/libs/tableexport/tableexport.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/moment/moment.js') }}"></script> -->
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>
    
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    <script>
        var apiPath = "{{config('AppConfig.endpoint.api.moduser')}}";
        var vueSetShiftPersonal = new Vue({
            el: '#vueSetShiftPersonal',
            data: {
                formTitle: 'Tambah Baru',

                sortBy: "id",
                sortDesc: false,
                searchString: "{{$q}}",
                curPage: 1,
                perPage: {{$pegawai['limit']}},
                perPageOption: [10,20,50,100],

                tahun: {{$tahun}},
                bulan: {{$bulan}},

                loadParams: {
                    params: {}
                },
 
                data: {
                   data: {!!json_encode($pegawai['data'])!!},
                   count: {{$pegawai['count']}},
                   currentPage: {{$pegawai['currentPage']}},
                   limit: {{$pegawai['limit']}},
                   offset: {{$pegawai['offset']}},
                   pageCount: {{$pegawai['pageCount']}}
                },
                form: {
                    pegawai_id:0,
                    shift_id:0,
                    libur:0,
                    start:null,
                    end:null
                }
            },
            created: function() {
                // this.form = JSON.parse(JSON.stringify(this.formEmpty));
                this.initScrollbar();
            },
            mounted: function() {
                var firstDate = moment(this.tahun+'-'+this.bulan+'-01');
                var lastDate = firstDate.endOf('month').format('YYYY-MM-DD');
                firstDate = firstDate.startOf('month').format('YYYY-MM-DD');
                // $('.datepicker-base').datepicker({format: 'yyyy-mm-dd',autoclose:true});
                $('.datepicker-base').datepicker({
                    format: 'yyyy-mm-dd',
                    container: '#shift-form-modal',
                    autoclose:true,
                    startDate: firstDate,
                    endDate: lastDate
                });
                $('.select2-select').select2({
                    dropdownParent: $('#shift-form-modal')
                });
                $(".wrapper1").scroll(function(){
                    $(".table-wrapper").scrollLeft($(".wrapper1").scrollLeft());
                });
                $(".table-wrapper").scroll(function(){
                    $(".wrapper1").scrollLeft($(".table-wrapper").scrollLeft());
                }); 
            },
            watch: {
                // curPage(v) {
                //     this.loadList(v,this.searchString,this.sortBy,this.sortDesc);
                // },
                // perPage(v) {
                //     this.loadList(this.curPage,this.searchString,this.sortBy,this.sortDesc);
                // },
                // sortBy(v) {
                //     this.loadList(this.curPage,this.searchString,v,this.sortDesc);
                // },
                // sortDesc(v) {
                //     this.loadList(this.curPage,this.searchString,this.sortBy, v);
                // },    
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
                initScrollbar() {
                    var that = this;
                    $(".div1").width($(".table-content").width());
                    $(".wrapper1").width($(".table-wrapper").width());
                    setTimeout(function(){that.initScrollbar()},1000);
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
                },
                /**
                generate absensi
                 */
                setDefaultAbsensi() {
                    window.location.href = "{{route('shift_personal.setDefaultAbsensi')}}?{!!$query!!}";
                },
                showForm(pegawaiId,tanggal,shiftId=0,isLibur=0) {    
                    this.form.pegawai_id = pegawaiId;
                    this.form.shift_id = shiftId;
                    $('#input-shift_id').val(shiftId);
                    $('#input-shift_id').trigger('change');
                    this.form.libur = isLibur;
                    this.form.start = tanggal;
                    this.form.end = tanggal;
                    $('.datepicker-base').datepicker('setDate', tanggal); 
                    $('#shift-form-modal').modal('show');
                },
                formSubmitted() {
                    var start = moment(this.form.start);
                    var end = moment(this.form.end);
                    if(!start.isValid()){
                        showAlert({text: "Penanggalan keliru, pastikan tanggal 'Dari' telah diisi dengan sesuai" ,type: "warning"});
                        return false;
                    }
                    if(!end.isValid()){
                        showAlert({text: "Penanggalan keliru, pastikan tanggal 'Sampai' telah diisi dengan sesuai" ,type: "warning"});
                        return false;
                    }
                    start = start.format('YYYY-MM-DD');
                    end = end.format('YYYY-MM-DD');
                    if(end < start){
                        showAlert({text: "Penanggalan keliru, pastikan tanggal 'Sampai' tidak kurang dari tanggal 'Dari'" ,type: "warning"});
                    }else{
                        this.form.start = start;
                        this.form.end = end;
                        $('#shift-form-modal').modal('hide');
                        $('#shift-form-modal').submit();
                    }
                }
            }
        });

        $(document).ready(function(){
            // $(document).on('change','#filterUser',function(){
            //     $('#filterPegawaiId').val($(this).val());
            //     $('#formFilterTanggal').submit();
            // });
            $(document).on('change','#start-date',function(){
                // $('#formFilterTanggal').submit();
            });
            $(document).on('change','#end-date',function(){
                // $('#formFilterTanggal').submit();
            });
            
            $('#shift-form-modal').on('shown.bs.modal', function() {
                
            });             

            if ($('html').attr('dir') === 'rtl') {
                $('.popover-demo [data-placement=right]').attr('data-placement', 'left').addClass('rtled');
                $('.popover-demo [data-placement=left]:not(.rtled)').attr('data-placement', 'right').addClass('rtled');
            }
            $('[data-toggle="popover"]').popover();
            
        });
    </script>
@endsection


@section('content')
<div id="vueSetShiftPersonal">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">Absensi /</span>Manage Shift per Pegawai</div>
    </h4>
    @include("alert")
    <div class="card">
    
        <div class="wrapper1">
            <div class="div1"></div>
        </div>

        <div class="card-body">
            <form id="formFilterTanggal" action="{{route('shift_personal.index')}}">
                <div class="row">
                    <div class="col">       
                        Per page: &nbsp;                    
                        <select class="form-control form-control-sm d-inline-block w-auto" name="limit" v-model="perPage">
                            <option v-for="option in perPageOption" :value="option">@{{option}}</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <select class="form-control form-control-xl d-inline-block w-auto" name="bulan" id="filterBulan">
                                @foreach($bulanList as $key => $bulanItem)
                                    <option value="{{$key}}" {{$key==ltrim($bulan,'0')?'selected="true"':''}}>{{$bulanItem}}</option>
                                @endforeach
                            </select>                            
                            <select class="form-control form-control-xl d-inline-block w-auto" name="tahun" id="filterTahun">
                                @foreach($tahunList as $key => $tahunItem)
                                    <option value="{{$key}}" {{$key==$tahun?'selected="true"':''}}>{{$tahunItem}}</option>
                                @endforeach
                            </select>
                            <input v-model="searchString" name="q" type="text" class="form-control" placeholder="Search...">
                            <span class="input-group-append">
                                <button class="btn btn-success" type="submit">Go</button>
                            </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <hr>

        <div class="row mb-2">
            <div class="col-10">   
                <h4 class="text-center">{{$bulanList[ltrim($bulan,'0')].' '.$tahun}}</h4>
            </div>
            <div class="col-2 text-right">
                <button class="btn btn-info ml-2 mr-2" @click="setDefaultAbsensi">Set Default Shift</button>
            </div>
        </div>

        <div class="card-datatable table-responsive table-wrapper">
            <table class="table table-striped table-bordered mb-0 table-content">
                <thead>
                    <tr>
                        <th class="f-cols" rowspan="2">No</th>
                        <th class="f-cols" rowspan="2" style="width: 200px;min-width: 200px;">Nama</th> 
                        <th class="f-cols" rowspan="2">PIN</th> 
                        <?php 
                        $tglTmp = $hariId = $addsClass = $tanggal = $isEditable = [];
                        for($day=1;$day<=$daysInMonth;$day++) {
                            $tgl = $day<=9?('0'.ltrim($day,'0')):$day;
                            $tanggal[$day] = $tahun.'-'.$bulan.'-'.$tgl;
                            $tglTmp[$day] = new \Carbon\Carbon($tanggal[$day]);
                            $hariId[$day] = $tglTmp[$day]->format('N');
                            $hari = $hariList[$hariId[$day]];
                            $ketHariLibur = '';
                            if(isset($hariLibur[$tanggal[$day]])){
                                $hariLibur[$day] = $hariLibur[$tanggal[$day]];
                                $class = 'bg-danger';
                                $ketHariLibur = '<button type="button" class="btn btn-xs btn-info" data-toggle="popover" data-placement="top" data-state="info" data-html="true" data-content="<ul>'.$hariLibur[$tanggal[$day]].'</ul>" title="Hari Libur">Libur</button>';
                            }else{
                                $class = $hariId[$day]==6?'bg-warning':($hariId[$day]==7?'bg-danger':'bg-success-lighter');
                            }
                            $addsClass[$day] = '';
                            $isEditable[$day] = true;
                            //jika sudah kelewat maka tandai sebagai tidak bisa diedit
                            if($tglTmp[$day]->lessThan(now()->format('Y-m-d'))){
                                $addsClass[$day] = 'done font-weight-normal';
                                $isEditable[$day] = false;
                            }else{
                                $addsClass[$day] = 'font-weight-bold';
                            }
                            $class .= ' '.$addsClass[$day];
                            if($day==1)$class .= ' f-first-cols';
                        ?>              
                        <th class="day-cols {{$class}}">
                            {!!$ketHariLibur!!}
                        </th>     
                        <?php } ?>
                    </tr>
                    <tr>
                        <?php 
                        for($day=1;$day<=$daysInMonth;$day++) {
                            $hari = $hariList[$hariId[$day]];
                            if(isset($hariLibur[$day])){
                                $class = 'bg-danger';
                                $ketHariLibur = '<br><span class="badge badge-default">'.$hariLibur[$day].'</span>';
                            }else{
                                $class = $hariId[$day]==6?'bg-warning':($hariId[$day]==7?'bg-danger':'bg-success-lighter');
                            }
                            $class .= ' '.$addsClass[$day];
                            if($day==1)$class .= ' f-first-cols';
                        ?>             
                        <th class="day-cols {{$class}}">
                            {{$day}}
                            <br><small>{{$hari}}</small>
                        </th> 
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($pegawai['data'] as $i => $v) {
                        
                        ?>
                    <tr>
                        <td class="f-cols">{{$i+1+(($pegawai['currentPage']-1)*$pegawai['limit'])}}</td>
                        <td class="f-cols" style="width: 200px;min-width: 200px;">{{ $v['nama'] }}</td>
                        <td class="f-cols">{{ $v['kode'] }}</td>

                        <?php 
                        for($day=1;$day<=$daysInMonth;$day++) {
                            if(isset($hariLibur[$day])){
                                $class = 'bg-danger-lighter';
                            }else{
                                $class = $hariId[$day]==6?'bg-warning-lighter':($hariId[$day]==7?'bg-danger-lighter':'');
                            }
                            
                            $class .= ' '.$addsClass[$day];
                            if($day==1)$class .= ' f-first-cols';
                        ?>
                        <td class="day-cols {{$class}}">
                        <?php
                        if(isset($v['absensi'][$day-1])){
                            $isLibur = 0;
                            if($v['absensi'][$day-1]['shift']){
                                $shiftId = $v['absensi'][$day-1]['shift']['id'];
                                $shiftName = $v['absensi'][$day-1]['shift']['nama'];
                            }else{
                                $shiftId = 0;
                                $shiftName = 'Custom';
                            }
                            
                            $TmpIsEditable = $isEditable[$day];
                            //jika sudah kelewat maka cek apakah masih belum ada kalkulasi, kalo belum maka boleh diedit
                            if(!$TmpIsEditable && in_array($v['absensi'][$day-1]['status'],[0,5,6]) && $v['absensi'][$day-1]['jenis_ijin_id']==0){
                                $TmpIsEditable = true;
                            }

                            //jika libur
                            if($v['absensi'][$day-1]['status']==5||$v['absensi'][$day-1]['status']==6){
                                $name ='Libur';
                                $shiftId = 0;
                                //jika libur dari shift
                                if($v['absensi'][$day-1]['status']==5){
                                    $name = $shiftName.' : Libur';
                                    $shiftId = $v['absensi'][$day-1]['shift']['id'];
                                }else{
                                    $isLibur = 1;//1 berarti libur dengan status 6
                                }
                                
                                if($TmpIsEditable){
                                    echo '<div @click="showForm('.$v['id'].',\''.$tanggal[$day].'\','.$shiftId.','.$isLibur.')" class="btn btn-danger btn-xs">'.$name.'</div>';
                                }else{
                                    echo '<b class="btn btn-danger-lighter btn-xs text-light">'.$name.'</b>';
                                }
                            }else{
                                if($TmpIsEditable){
                                    echo '<div @click="showForm('.$v['id'].',\''.$tanggal[$day].'\','.$shiftId.','.$isLibur.')" class="btn btn-info btn-xs">'.$shiftName.'</div>';
                                }else{
                                    echo '<b class="btn btn-info-lighter btn-xs text-light">'.$shiftName.'</b>';
                                }
                            }
                        }else{
                            echo '<b class="badge badge-default">BELUM<br>DISET</b>';
                        }
                         ?>
                        </td> 
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>        

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
    
    <!-- Event modal -->
    <form method="POST" v-on:submit.prevent="formSubmitted" action="{{route('shift_personal.update')}}" class="modal modal-top fade" id="shift-form-modal">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="pegawai_id" v-model="form.pegawai_id">
        <input type="hidden" name="limit" v-model="data.limit">
        <input type="hidden" name="offset" v-model="data.offset">
        <input type="hidden" name="q" v-model="searchString">
        <input type="hidden" name="orderBy" v-model="sortBy">
        <input type="hidden" name="orderType" :value="sortDesc?'DESC':'ASC'">
        <input type="hidden" name="tahun" value="{{$tahun}}">
        <input type="hidden" name="bulan" value="{{$bulan}}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Shift</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Shift</label>
                        <select id="input-shift_id" class="select2-select form-control" style="width: 100%" data-allow-clear="true" name="shift_id" onchange="vueSetShiftPersonal.form.shift_id = this.value">
                        <?php foreach($shift as $v ){ ?>
                            <option value="{{$v['id']}}">{{$v['nama']}}</option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Libur</label>
                        <select class="custom-select" name="libur" v-model="form.libur">
                            <option value="0">Sesuai Shift</option>
                            <option value="1">Libur</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="form-group col">
                            <label class="form-label">Dari</label>
                            <input type="text" id="start-date" name="start" v-model="form.start" class="form-control datepicker-base" onchange="vueSetShiftPersonal.form.start = this.value">
                        </div>
                        <div class="form-group col">
                            <label class="form-label">Sampai</label>
                            <input type="text" id="end-date"  name="end" v-model="form.end" class="form-control datepicker-base" onchange="vueSetShiftPersonal.form.end = this.value">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="formSubmitted">Save</button>
                </div>
            </div>
        </div>
    </form>
    <!-- / Event modal -->
</div>
@endsection