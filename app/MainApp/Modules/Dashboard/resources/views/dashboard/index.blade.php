@extends('layouts.layout-horizontal-sidenav')

@section('content')
    <h4 class="font-weight-bold py-3 mb-4">
        Selamat Datang
        <div class="text-muted text-tiny mt-1"><small class="font-weight-normal">{{now()->format("j M Y")}}</small></div>
    </h4>

    @include("alert")
    
    <?php if(false){ ?>
    <div class="card mb-4">
        <h6 class="card-header with-elements">
            <div class="card-header-title">Statistik Informasi Absensi</div>
        </h6>
        <div class="row no-gutters row-bordered">
            <div class="col-md-8 col-lg-12 col-xl-8">
                <div class="card-body text-center">
                    <canvas id="piecart-info-absensi" height="250" class="chartjs-dashboard"></canvas>
                </div>
            </div>

            <div class="col-md-4 col-lg-12 col-xl-4">
                <div class="card-body">

                    <!-- Numbers -->
                    <div class="row">
                        <div class="col-6 col-xl-5 text-muted mb-3">Tepat Waktu</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">123</span>
                            <sup class="text-success">+90%</sup>
                        </div>
                        <div class="col-6 col-xl-5 text-muted mb-3">Terlambat</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">0</span>
                            <sup class="text-success">0%</sup>
                        </div>
                        <div class="col-6 col-xl-5 text-muted mb-3">Ijin</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">0</span>
                            <sup class="text-success">0%</sup>
                        </div>
                        <div class="col-6 col-xl-5 text-muted mb-3">Cuti</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">0</span>
                            <sup class="text-success">0%</sup>
                        </div>
                        <div class="col-6 col-xl-5 text-muted mb-3">Dinas Luar</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">15</span>
                            <sup class="text-success">5%</sup>
                        </div>
                        <div class="col-6 col-xl-5 text-muted mb-3">Sakit</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">5</span>
                            <sup class="text-success">+3%</sup>
                        </div>
                        <div class="col-6 col-xl-5 text-muted mb-3">Tanpa Keterangan</div>
                        <div class="col-6 col-xl-7 mb-3">
                            <span class="text-big">0</span>
                            <sup class="text-success">0%</sup>
                        </div>
                    </div>
                    <!-- / Numbers -->

                </div>
            </div>
        </div>
    </div>
    
    
    <div class="card mb-4">
        <h6 class="card-header with-elements">
            <div class="card-header-title">Statistik Kehadiran</div>
        </h6>
        <div class="no-gutters row-bordered">
            <div class="card-body">
                <canvas id="graph-kehadiran" height="350" style="width: 100%" class="chartjs-dashboard"></canvas>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if(false){ ?>
    <div class="row">
        <div class="col-md-12 col-lg-8 col-xl-8"> 
            <div class="card mb-4">
                <h6 class="card-header">
                    Informasi Absensi
                </h6>                                
                <div class="card-datatable table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th style="min-width: 5rem;">Nama Pegawai</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Keluar</th>
                                    <th>Jam Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>
            </div>  
        </div>
        <div class="col-md-12 col-lg-4 col-xl-4">  
            <div class="card mb-4">
                <h6 class="card-header">
                    Informasi Izin
                </h6>
                <div class="card-datatable table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th style="min-width: 5rem;">Nama Pegawai</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
            </div>    
        </div>
    </div>
    <?php } ?>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/webdist/vendor/libs/chartjs/chartjs.js') }}"></script>
<script>
$(function() {
    // Wrap charts
    // $('.chartjs-dashboard').each(function() {
    //     $(this).wrap($('<div style="height:' + this.getAttribute('height') + 'px"></div>'));
    // });

    var graphChart = new Chart(document.getElementById('graph-kehadiran').getContext("2d"), {
        type: 'line',
        data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            label:           'Hadir',
            data:            [150, 150, 150, 150, 150, 150, 150],
            borderWidth:     1,
            backgroundColor: 'rgba(255, 193, 7, 0.3)',
            borderColor:     '#FFC107',
            borderDash:      [5, 5],
            fill: false
        }, {
            label:           'Tidak Hadir',
            data:            [5, 10, 0, 0, 2, 5, 0],
            borderWidth:     1,
            backgroundColor: 'rgba(233, 30, 99, 0.3)',
            borderColor:     '#E91E63',
        }],
        },

        // Demo
        options: {
            responsive: false,
            maintainAspectRatio: false
        }
    });

    
    var pieChart = new Chart(document.getElementById('piecart-info-absensi').getContext("2d"), {
        type: 'pie',
        data: {
        labels: [ 'Tepat Waktu', 'Terlambat', 'Ijin', 'Cuti', 'Dinas Luar', 'Sakit', 'Tanpa Keterangan' ],
        datasets: [{
            data: [ 180, 0, 0, 0, 15, 5, 0 ],
            backgroundColor: [ '#FF6384', '#36A2EB', '#FFCE56', '#FF1256', '#A51256','#BBA2EB','#36EEEB' ],
            hoverBackgroundColor: [ '#FF6384', '#36A2EB', '#FFCE56', '#FF1256', '#A51256','#BBA2EB','#36EEEB' ]
        }]
        },

        // Demo
        options: {
            responsive: false,
            maintainAspectRatio: false
        }
    });

});
</script>
@endsection
