@extends('layouts.layout-horizontal-sidenav')

@section('styles')
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css') }}">
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
    </style>
@endsection

@section('scripts')
@parent
    <!-- Dependencies -->
    <script src="{{ asset('/webdist/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>    
    <script src="{{ asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js') }}"></script>
    <script>
        var apiPath = "{{config('AppConfig.endpoint.api.moduser')}}";
        var vueLapJejakKehadiran = new Vue({
            el: '#vueLapJejakKehadiran',
            data: {
                formTitle: 'Tambah Baru',

                loadParams: {
                    params: {}
                },
 
                data: {
                   data: {!!json_encode($absensi['data'])!!},
                   count: {{$absensi['count']}},
                   currentPage: {{$absensi['currentPage']}},
                   limit: {{$absensi['limit']}},
                   offset: {{$absensi['offset']}},
                   pageCount: {{$absensi['pageCount']}}
                },
            },
            mounted: function() {   
                $('.select2-select').select2();  
            },
            created: function() {
                // this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },
            watch: {
            },
            methods: {
            }
        });

        $(document).ready(function(){
            $(document).on('change','#filterUser',function(){
                $('#filterPegawaiId').val($(this).val());
                // $('#formFilterTanggal').submit();
            });
            // $(document).on('change','#filterBulan',function(){
            //     $('#formFilterTanggal').submit();
            // });
            // $(document).on('change','#filterTahun',function(){
            //     $('#formFilterTanggal').submit();
            // });
            
        });
    </script>
@endsection


@section('content')
<?php
$statusAbsen = [
    '<span class="badge badge-danger">Alpha</span>',//belum ada data, anggap alpha
    '<span class="badge badge-success">Hadir</span>',
    '<span class="badge badge-danger">Alpha</span>',
    '<span class="badge badge-warning">Belum absen masuk atau pulang</span>',
    '',//'Ijin',
    'Libur'
];
?>
<div id="vueLapJejakKehadiran">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">Laporan /</span> Jejak Kehadiran</div>
    </h4>
    @include("alert")
    <div class="card"> 
        <div class="card-body">  
            <form action="{{route('laporan.jejak_kehadiran')}}" id="formFilterTanggal">
                <div class="input-group">
                    
                    @if(!$isPegawai)
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            Pilih User :
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <select name="pegawaiId" class="select2-select" id="filterUser" style="width: 200px !important;">
                            @foreach($pegawai['data'] as $pegItem)
                                <option value="{{$pegItem['id']}}" {!!$pegItem['id']==$pegawaiId?'selected="true"':''!!}>{{$pegItem['nama']}}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="pegawaiId" id="filterPegawaiId" value="{{$pegawaiId}}">
                    @endif
                    <select class="custom-select flex-grow-1" name="bulan" id="filterBulan">
                        @foreach($bulanList as $key => $bulanItem)
                            <option value="{{$key}}" {{$key==ltrim($bulan,'0')?'selected="true"':''}}>{{$bulanItem}}</option>
                        @endforeach
                    </select>              

                    <select class="custom-select flex-grow-1" name="tahun" id="filterTahun">
                        @foreach($tahunList as $key => $tahunItem)
                            <option value="{{$key}}" {{$key==$tahun?'selected="true"':''}}>{{$tahunItem}}</option>
                        @endforeach
                    </select>
                    <span class="input-group-append">
                        <button class="btn btn-success" type="submit">Go</button>
                    </span>
                </div>
            </form>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Hari</th> 
                        <?php /*<th>Jam Masuk</th>
                        <th style="">Jam Keluar</th> */ ?>
                        <th>Scan Masuk</th>
                        <th>Scan Keluar</th> 
                        <th>Datang<br>Terlambat</th> 
                        <th>Pulang<br>Cepat</th> 
                        <th>Lebih</th> 
                        <th>Kurang</th> 
                        <th>Status</th> 
                    </tr>
                    <tr>
                        <th colspan="12" style="text-align: center;"> 
                            <h4 class="p-0 m-0">{{$bulanList[ltrim($bulan,'0')].' '.$tahun}}</h4>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $curDate = now()->format('Y-m-d');
                    for($i=1;$i<=$daysInMonth;$i++) {
                        $tgl = $i<=9?('0'.ltrim($i,'0')):$i;
                        $tanggal = $tahun.'-'.$bulan.'-'.$tgl;
                        $tglTmp = new \Carbon\Carbon($tanggal);
                        $hariId = $tglTmp->dayOfWeek;
                        $hari = $hariList[$tglTmp->format('N')];
                        $class = $class2 = '';
                        if($hariId==0){
                            $class = 'bg-danger text-white';
                            $class2 = 'bg-danger-lighter';
                        }else if($hariId==6){
                            $class = 'bg-warning-lighter';
                            $class2 = 'bg-warning-lighter';
                        }
                        $ketHariLibur = '';
                        if(isset($hariLibur[$tanggal])){
                            $class = 'bg-danger text-white';
                            $class2 = 'bg-danger-lighter';
                            $ketHariLibur = '<ul class="m-0">'.$hariLibur[$tanggal].'</ul>';
                        }
                        ?>
                    <tr>
                        <td class="{{$class}}">{{$i}}</td>
                        <td class="{{$class}}">{{$hari}}</td>
                        @if(isset($absensi['data'][$tanggal]))

                            @if($absensi['data'][$tanggal]['jam_masuk'])
                            <?php /*
                                <td class="{{$class2}}">{{$absensi['data'][$tanggal]['jam_masuk']?(new \Carbon\Carbon($absensi['data'][$tanggal]['jam_masuk']))->format('H:i:s'):''}}</td>
                                <td class="{{$class2}}">{{$absensi['data'][$tanggal]['jam_keluar']?(new \Carbon\Carbon($absensi['data'][$tanggal]['jam_keluar']))->format('H:i:s'):''}}</td>*/ ?>
                                
                                <?php 
                                if($absensi['data'][$tanggal]['status'] == 4 || isset($jenisIjin[$absensi['data'][$tanggal]['jenis_ijin_id']])){
                                    if($jenisIjin[$absensi['data'][$tanggal]['jenis_ijin_id']]['is_show_scanner']){ 
                                ?>
                                    <td class="{{$class2}}">
                                        {{$absensi['data'][$tanggal]['scan_masuk']?(new \Carbon\Carbon($absensi['data'][$tanggal]['scan_masuk']))->format('H:i:s'):(new \Carbon\Carbon($absensi['data'][$tanggal]['jam_masuk']))->format('H:i:s')}}
                                    </td>
                                    <td class="{{$class2}}">
                                        {{$absensi['data'][$tanggal]['scan_keluar']?(new \Carbon\Carbon($absensi['data'][$tanggal]['scan_keluar']))->format('H:i:s'):(new \Carbon\Carbon($absensi['data'][$tanggal]['jam_keluar']))->format('H:i:s')}}
                                    </td>
                                <?php }else{ ?>
                                    <td class="{{$class2}}"></td>
                                    <td class="{{$class2}}"></td>

                                <?php    
                                    }
                                }else{ 
                                ?>
                                <td class="{{$class2}}">
                                    {{$absensi['data'][$tanggal]['scan_masuk']?(new \Carbon\Carbon($absensi['data'][$tanggal]['scan_masuk']))->format('H:i:s'):''}}
                                </td>
                                <td class="{{$class2}}">
                                    {{$absensi['data'][$tanggal]['scan_keluar']?(new \Carbon\Carbon($absensi['data'][$tanggal]['scan_keluar']))->format('H:i:s'):''}}
                                </td>
                                <?php } ?>
                                <td class="{{$class2}}">{{$absensi['data'][$tanggal]['status'] != 4 && $absensi['data'][$tanggal]['keterlambatan_jam']?\Facades\App\MainApp\Repositories\Absensi::formatJamKerja($absensi['data'][$tanggal]['keterlambatan_jam']):0}}</td>
                                <td class="{{$class2}}">{{$absensi['data'][$tanggal]['status'] != 4 && $absensi['data'][$tanggal]['pulang_cepat_jam']?\Facades\App\MainApp\Repositories\Absensi::formatJamKerja($absensi['data'][$tanggal]['pulang_cepat_jam']):0}}</td>
                                <td class="{{$class2}}">{{$absensi['data'][$tanggal]['status'] != 4 && $absensi['data'][$tanggal]['kelebihan_jam']?\Facades\App\MainApp\Repositories\Absensi::formatJamKerja($absensi['data'][$tanggal]['kelebihan_jam']):0}}</td>
                                <td class="{{$class2}}">{{$absensi['data'][$tanggal]['status'] != 4 && $absensi['data'][$tanggal]['kekurangan_jam']?\Facades\App\MainApp\Repositories\Absensi::formatJamKerja($absensi['data'][$tanggal]['kekurangan_jam']):0}}</td>
                            @else
                                <td class="{{$class2}}" colspan="6"></td>
                            @endif

                            <td class="{{$class2}}">
                                @if($tanggal<=$curDate || $absensi['data'][$tanggal]['status'] !=0 )
                                    {!!isset($statusAbsen[$absensi['data'][$tanggal]['status']])?$statusAbsen[$absensi['data'][$tanggal]['status']]:''!!}
                                @else
                                    New Data
                                @endif

                                <?php if(isset($statusAbsen[$absensi['data'][$tanggal]['status']]) && $absensi['data'][$tanggal]['status'] == 4 && isset($jenisIjin[$absensi['data'][$tanggal]['jenis_ijin_id']])){
                                    echo '<b>'.$jenisIjin[$absensi['data'][$tanggal]['jenis_ijin_id']]['nama'].'</b>';
                                } ?>

                                {!! $ketHariLibur !!}
                            
                            </td>
                        @else
                        <?php /*<td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>*/ ?>
                        <td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>
                        <td class="{{$class2}}"></td>
                        @endif
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>        
        @if(isset($rekapAbsensi['kerja']))
        <div class="card-body"> 

            <div class="row">            
                <div class="col-md-3 col-sm-6">
                    Hari Kerja : <?php echo $rekapAbsensi['kerja'] ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    Masuk : <?php echo $rekapAbsensi['hadir'] ?>
                </div>
                <div class="col-md-3 col-sm-6">
                    Alpha : <?php echo $rekapAbsensi['alpa'] + $rekapAbsensi['tidaklengkap'] ?>
                </div>

                <?php 
                foreach($jenisIjinKategori as $k => $v ){
                    if($k!=0){
                        foreach($v['jenisIjin'] as $v2 ){ ?>
                    <div class="col-md-3 col-sm-6">
                        {{$v2['nama']}} <i>({{isset($v['kategori']['nama'])?$v['kategori']['nama']:''}})</i> : {{isset($rekapAbsensi['jenisijin'][$v2['id']])?$rekapAbsensi['jenisijin'][$v2['id']]:0}}
                    </div>
                <?php } }} ?> 

            </div>
            <hr>
            <div class="row">          
                <div class="col-md-3 col-sm-6">
                    Terlambat : 
                    <?php 
                    echo $rekapAbsensi['telat_jam']?
                    \Facades\App\MainApp\Repositories\Absensi::formatJamKerja($rekapAbsensi['telat_jam']):
                    0 ?>
                </div>       
                <div class="col-md-3 col-sm-6">
                    Pulang Cepat : 
                    <?php 
                    echo $rekapAbsensi['cepat_jam']?
                    \Facades\App\MainApp\Repositories\Absensi::formatJamKerja($rekapAbsensi['cepat_jam']):
                    0 ?>
                </div>       
                <div class="col-md-3 col-sm-6">
                    Kelebihan Jam : 
                    <?php 
                    echo $rekapAbsensi['kelebihan_jam']?
                    \Facades\App\MainApp\Repositories\Absensi::formatJamKerja($rekapAbsensi['kelebihan_jam']):
                    0 ?>
                </div>
                       
                <div class="col-md-3 col-sm-6">
                    Total Jam Kerja : 
                    <?php 
                    echo $rekapAbsensi['total_jam']?
                    \Facades\App\MainApp\Repositories\Absensi::formatJamKerja($rekapAbsensi['total_jam']):
                    0 ?>
                </div>   

                <div class="col-md-3 col-sm-6">
                    Persentase : 
                    <?php 
                    echo $rekapAbsensi['kerja']?
                    (floor(($rekapAbsensi['total_jam']/$rekapAbsensi['jam_kerja'])*100).' %'):
                    '0 %' ?>
                </div>
            </div>
        </div> 
        @endif
    </div>
</div>
@endsection