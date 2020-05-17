@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
    
    <style>
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

        
        .bg-warning-lighter {
            background-color: rgba(255,217,80,0.5);
        }
        .bg-danger-lighter {
            background-color: rgba(217,83,79,0.5);
        }
        .bg-success-lighter {
            background-color: rgba(2,179,113,0.5);
        }
        .bg-info-lighter {
            background-color: rgba(128,217,184,0.5);
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
    <!-- <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js') }}"></script> -->
    
    <!-- <script src="{{ asset('/js/tables_bootstrap-table.js') }}"></script> -->
    
    <script>
        var apiPath = "{{config('AppConfig.endpoint.api.moduser')}}";
        var vueLapRekapKehadiran = new Vue({
            el: '#vueLapRekapKehadiran',
            data: {
                formTitle: 'Tambah Baru',

                sortBy: "id",
                sortDesc: false,
                searchString: "{{$q}}",
                curPage: 1,
                perPage: {{$pegawai['limit']}},
                perPageOption: [10,20,50,100],

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
            },
            created: function() {
                // this.form = JSON.parse(JSON.stringify(this.formEmpty));
                this.initScrollbar();
            },
            mounted: function() {
                $('.datepicker-base').datepicker({format: 'yyyy-mm-dd',autoclose:true,endDate:new Date()});

                $('[data-toggle="tooltip"]').tooltip();

                $(".wrapper1").scroll(function(){
                    $(".table-wrapper").scrollLeft($(".wrapper1").scrollLeft());
                });
                $(".table-wrapper").scroll(function(){
                    $(".wrapper1").scrollLeft($(".table-wrapper").scrollLeft());
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
                }
            }
        });

        $(document).ready(function(){
            // $(document).on('change','#filterUser',function(){
            //     $('#filterPegawaiId').val($(this).val());
            //     $('#formFilterTanggal').submit();
            // });
            // $(document).on('change','.start-date',function(){
            //     $('#formFilterTanggal').submit();
            // });
            // $(document).on('change','.end-date',function(){
            //     $('#formFilterTanggal').submit();
            // });
            
        });
    </script>
@endsection


@section('content')
<div id="vueLapRekapKehadiran">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">Laporan /</span>Kehadiran Harian</div>
    </h4>
    @include("alert")
    <div class="card">
    
        <div class="wrapper1">
            <div class="div1"></div>
        </div>

        <div class="card-body">
            <form id="formFilterTanggal" action="{{route('laporan.kehadiran_harian')}}">
                <div class="row">                
                    <div class="col-lg-6 col-sm-12 pb-3">    
                        Per page: &nbsp;                    
                        <select class="form-control form-control-sm d-inline-block w-auto" v-model="perPage" name="limit">
                            <option v-for="option in perPageOption" :value="option">@{{option}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12">                    
                        <div class="input-group">
                            <div class="input-group-text">
                                Dari
                            </div>
                            <input type="text" name="tanggal_start" class="datepicker-base start-date form-control" value="{{$tanggal_start}}"/>
                            <div class="input-group-text">
                                Ke
                            </div>
                            <input type="text" name="tanggal_end" class="datepicker-base end-date form-control" value="{{$tanggal_end}}"/>
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
            <div class="col-12">   
                <h4 class="text-center"><?php 
                    $tglStart = new \Carbon\Carbon($tanggal_start);
                    $tglEnd = new \Carbon\Carbon($tanggal_end);

                    echo $tglStart->format('j').' '.$bulanList[$tglStart->format('n')].' '.$tglStart->format('Y').' &nbsp;&nbsp;&nbsp; - &nbsp;&nbsp;&nbsp; ';
                    echo $tglEnd->format('j').' '.$bulanList[$tglEnd->format('n')].' '.$tglEnd->format('Y');
                    ?></h4>
            </div>
        </div>

        <div class="card-datatable table-responsive table-wrapper">
            <table class="table table-striped table-bordered mb-0 table-content">
                <thead>
                    <tr>
                        <th class="bg-light">No</th>
                        <th class="bg-light" style="min-width: 200px;">Nama</th> 
                        <th class="bg-light">PIN</th> 

                        <?php 
                        foreach($periode as $tanggal) {
                            $tgl = $tanggal->format('d');
                            $hari = $tanggal->dayOfWeek;
                            $class = $hari==0?'bg-danger':($hari==6?'bg-warning':'');
                        ?>
                        <th class="{{$class}}">{{$tgl}}<br><small>{{$weekMap[$hari]}}</small></th> 
                        <?php } ?>

                        <th class="bg-info-lighter" style="">Kerja<br><small>Hari</small></th> 
                        <th class="bg-info-lighter" style="">Hadir<br><small>Hari</small></th>
                        <th class="bg-info-lighter" style="">Alpha<br><small>Hari</small></th> 
                        
                        <?php 
                        foreach($jenisIjinKategori as $k => $kategori) {
                            if($k != 0 && $kategori['kategori']['tampil_kehadiran_harian']){
                        ?>
                        <th data-toggle="tooltip" class="bg-success-lighter" title="{{$kategori['kategori']['nama']}}">{{$kategori['kategori']['singkatan']}}<br><small>Hari</small></th> 
                        <?php }} ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($pegawai['data'] as $i => $v) {
                        
                        ?>
                    <tr>
                        <td>{{$i+1+(($pegawai['currentPage']-1)*$pegawai['limit'])}}</td>
                        <td><a href="{{route('laporan.jejak_kehadiran',['pegawaiId'=>$v['id']])}}">{{ $v['nama'] }}</a></td>
                        <td>{{ $v['kode'] }}</td>
                        
                        <?php 
                        foreach($periode as $tanggal) {
                            $tgl = $tanggal->format('Y-m-d');
                            $hari = $tanggal->dayOfWeek;
                            $class = $hari==0?'bg-danger-lighter':($hari==6?'bg-warning-lighter':'');
                            $tmp = [];
                            foreach($v['absensi'] as $abs) {
                                $tmp[$abs['tanggal']] = $abs;
                            }
                            $v['absensi'] = $tmp;
                        ?>
                        <td class="{{$class}}">
                        <?php
                        if(isset($v['absensi'][$tgl])){
                            //jika libur
                            if($v['absensi'][$tgl]['status']==5||$v['absensi'][$tgl]['status']==6){
                                // echo '<b data-toggle="tooltip" class="text-light">Libur</b>';
                            //jika telat/kurang jam kerja/scan tidak komplit
                            }else if($v['absensi'][$tgl]['status']==3){
                                echo '<b data-toggle="tooltip" class="badge badge-warning" title="Belum absen masuk atau pulang"> &nbsp; </b>';
                            //jika alpha
                            }else if($v['absensi'][$tgl]['status']==2){
                                echo '<b data-toggle="tooltip" class="badge badge-danger" title="Alpha"> &nbsp; </b>';
                            //jika ijin
                            }else if(isset($jenisIjin[$v['absensi'][$tgl]['jenis_ijin_id']])){
                                echo '<b data-toggle="tooltip" class="badge" style="background-color: '.$jenisIjin[$v['absensi'][$tgl]['jenis_ijin_id']]['warna'].';" title="'.$jenisIjin[$v['absensi'][$tgl]['jenis_ijin_id']]['nama'].'"> &nbsp; </b>';
                            //jika masuk
                            }else if($v['absensi'][$tgl]['status']==1){                                
                                echo '<b data-toggle="tooltip" class="badge badge-success" title="Masuk"> &nbsp; </b>';                            
                            //jika belum ada data
                            }else if($v['absensi'][$tgl]['status']==0){                        
                                echo '<b data-toggle="tooltip" class="badge badge-danger" title="Alpha - Belum ada data scan"> &nbsp; </b>';
                            //jika ijin tapi tidak ada id izin/permohonan nya
                            }else if($v['absensi'][$tgl]['status']==4){                        
                                echo '<b data-toggle="tooltip" class="badge badge-danger" title="Alpha - Jenis permohonan tidak ditemukan"> &nbsp; </b>';
                            }
                        }else{
                            //belum ada data, anggap alpa
                            echo '<b data-toggle="tooltip" class="badge badge-danger" title="Alpha - Belum ada data"> &nbsp; </b>';
                        }
                         ?>
                        
                        </td> 
                        <?php } ?>

                        <td>
                            <?php
                            if(isset($absensi[$v['id']]['kerja']) && isset($absensi[$v['id']]['libur'])){
                                echo $absensi[$v['id']]['kerja'];//-$absensi[$v['id']]['libur'];
                            }
                            ?>
                        </td>
                        <td>                        
                            <?php
                            if(isset($absensi[$v['id']]['hadir'])){
                                echo $absensi[$v['id']]['hadir'];
                            }
                            ?>
                        </td>
                        <td>                        
                            <?php
                            if(isset($absensi[$v['id']]['alpa'])){
                                echo $absensi[$v['id']]['alpa'] + (isset($absensi[$v['id']]['tidaklengkap'])?$absensi[$v['id']]['tidaklengkap']:0);
                            }
                            ?>
                        </td>
                        
                        <?php 
                        foreach($jenisIjinKategori as $k => $kategori) {
                            if($k != 0 && $kategori['kategori']['tampil_kehadiran_harian']){
                        ?>
                        <td>
                            {{isset($absensi[$v['id']]['jenisIjinKategori'][$kategori['kategori']['id']]) ? $absensi[$v['id']]['jenisIjinKategori'][$kategori['kategori']['id']] : 0}}
                        </td> 
                        <?php }} ?>
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
            <hr>
            <h4>Keterangan :</h4>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <b class="badge badge-success">  &nbsp; </b> Masuk
                </div>
                <div class="col-md-6 col-sm-12">
                    <b class="badge badge-warning">  &nbsp; </b> Belum absen masuk atau pulang
                </div>
                <div class="col-md-6 col-sm-12">
                    <b class="badge badge-danger">  &nbsp; </b> Alpha
                </div>
                <?php /*foreach($jenisIjinKategori[0]['jenisIjin'] as $v2 ){ ?>
                <div class="col-md-6 col-sm-12">
                    <b class="badge" style="background-color: {{$v2['warna']}}">  &nbsp; </b> {{$v2['nama']}}
                </div>
                <?php }*/ ?>
            </div>
            <?php 
            foreach($jenisIjinKategori as $k => $v ){
                if($k!=0){
            ?>
            <div class="row">
                <div class="col-12 pt-3">
                    <h5> {{isset($v['kategori']['nama'])?$v['kategori']['nama']:''}} :</h5>
                </div>
                <?php foreach($v['jenisIjin'] as $v2 ){ ?>
                <div class="col-md-6 col-sm-12">
                    <b class="badge" style="background-color: {{$v2['warna']}}">  &nbsp; </b> {{$v2['nama']}}
                </div>
                <?php } ?>
            </div>
            <?php }} ?>  
        </div>

    </div>
</div>
@endsection