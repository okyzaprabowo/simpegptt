<?php $routeName = Route::currentRouteName(); ?><div id="layout-sidenav" class="{{ isset($layout_sidenav_horizontal) ? 'layout-sidenav-horizontal sidenav-horizontal container-p-x flex-grow-0' : 'layout-sidenav sidenav-vertical' }} sidenav bg-sidenav-theme">

    <!-- Inner -->
    <ul class="sidenav-inner{{ empty($layout_sidenav_horizontal) ? ' py-1' : '' }}">

        
        @if(\UserAuth::hasAccess('Absensi.approval'))
        <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen.approval') === 0 ? ' active' : '' }}">
            <a href="{{ route('permohonan_absen.approval') }}" class="sidenav-link"><i class="sidenav-icon ion ion-ios-speedometer"></i><div>Dashboard</div></a>
        </li> 
        @else
        <li class="sidenav-item{{ Request::is('/') ? ' active' : '' }}">
            <a href="{{ route('dashboard') }}" class="sidenav-link"><i class="sidenav-icon ion ion-ios-speedometer"></i><div>Dashboard</div></a>
        </li>
        @endif  

        <!-- Setup -->
        @if(\UserAuth::hasAccess('Master'))
        <li class="sidenav-item{{ strpos($routeName, 'master') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-build"></i><div>Setup</div></a>

            <ul class="sidenav-menu">
                
                @if(\UserAuth::hasAccess('Master.jenisijin'))
                <li class="sidenav-item{{ strpos($routeName, 'master.jenis_ijin') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.jenis_ijin.list') }}" class="sidenav-link"><div>Jenis Permohonan</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.shift'))
                <li class="sidenav-item{{ strpos($routeName, 'master.shift') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.shift.list') }}" class="sidenav-link"><div>Shift</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.jabatan'))
                <li class="sidenav-item{{ strpos($routeName, 'master.jabatan') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.jabatan.list') }}" class="sidenav-link"><div>Jabatan</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.instansi'))
                <li class="sidenav-item{{ strpos($routeName, 'master.instansi') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.instansi.list') }}" class="sidenav-link"><div>Satuan Kerja</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.harilibur'))
                <li class="sidenav-item{{ strpos($routeName, 'master.hari_libur') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.hari_libur.list') }}" class="sidenav-link"><div>Hari Libur</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Master.mesinabsen'))
                <li class="sidenav-item{{ strpos($routeName, 'master.mesin_absen') === 0 ? ' active' : '' }}">
                    <a href="{{ route('master.mesin_absen.list') }}" class="sidenav-link"><div>Mesin Absen</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('moduser'))
                <li class="sidenav-item{{ strpos($routeName, 'user.') === 0 ? ' active' : '' }}">
                    <a href="{{ route('user.list') }}" class="sidenav-link"><div>Managemen User</div></a>
                </li>
                @endif
               
            </ul>
        </li>
        @endif


        <!-- Pages 
        <li class="sidenav-item{{ strpos($routeName, 'pages.') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle">
                <i class="sidenav-icon ion ion-md-document"></i>
                <div>Kepegawaian</div>
            </a>
            <ul class="sidenav-menu">

                <li class="sidenav-item{{ strpos($routeName, 'pages.articles.') === 0 ? ' active open' : '' }}">
                    <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><div>Biografi</div></a>

                    <ul class="sidenav-menu">
                        <li class="sidenav-item{{ $routeName == 'pages.articles.list' ? ' active' : '' }}">
                            <a href="#" class="sidenav-link"><div>Data Alamat</div></a>
                        </li>
                        <li class="sidenav-item{{ $routeName == 'pages.articles.edit' ? ' active' : '' }}">
                            <a href="#" class="sidenav-link"><div>Riwayat Pendidikan</div></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
-->

        <!-- Kepegawaian -->
        @if(\UserAuth::hasAccess('Pegawai'))
        <li class="sidenav-item{{ strpos($routeName, 'pegawai') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-contact"></i><div>Kepegawaian</div></a>

            <ul class="sidenav-menu">
                @if(\UserAuth::hasAccess('Pegawai.profile'))
                <li class="sidenav-item{{ $routeName == 'pegawai.profile' ? ' active' : '' }}">
                    <a href="{{ route('pegawai.profile') }}" class="sidenav-link"><div>Data Pribadi</div></a>
                </li>
                @endif
              <!--  <li class="sidenav-item{{ $routeName == 'dashboards.dashboard-2' ? ' active' : '' }}">
                    <a href="{{ route('permohonan_absen.index') }}" class="sidenav-link"><div>Data Alamat</div></a>
                </li>
                <li class="sidenav-item{{ $routeName == 'dashboards.dashboard-3' ? ' active' : '' }}">
                    <a href="#" class="sidenav-link"><div>Riwayat Pendidikan</div></a>
                </li>
                <li class="sidenav-item{{ $routeName == 'dashboards.dashboard-4' ? ' active' : '' }}">
                    <a href="#" class="sidenav-link"><div>Riwayat Jabatan</div></a>
                </li> --> 
                <!-- khusus utk non pegawai muncul ini -->
                @if(\UserAuth::hasAccess('Pegawai.master'))
                <li class="sidenav-item{{ strpos($routeName, 'pegawai') === 0 && $routeName != 'pegawai.profile' ? ' active' : '' }}">
                    <a href="{{ route('pegawai.list') }}" class="sidenav-link"><div>Daftar Pegawai</div></a>
                </li>
                @endif
               
            </ul>
        </li>
        @endif
        
        @if(\UserAuth::hasAccess('Absensi'))
        <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen') === 0 && $routeName != 'permohonan_absen.approval' ? ' active open' : '' }}">

            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-finger-print"></i><div>Absensi</div></a>
            <ul class="sidenav-menu">
                @if(\UserAuth::hasAccess('Absensi.permohonan'))
                <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen') === 0 && strpos($routeName, 'permohonan_absen.approval') !== 0 ? ' active' : '' }}">
                    <a href="{{ route('permohonan_absen.index') }}" class="sidenav-link"><div>Pengajuan ketidakhadiran</div></a>
                </li>
                @endif
                <?php /*@if(\UserAuth::hasAccess('Absensi.approval'))
                <li class="sidenav-item{{ strpos($routeName, 'permohonan_absen.approval') === 0 ? ' active' : '' }}">
                    <a href="{{ route('permohonan_absen.approval') }}" class="sidenav-link"><div>Approval Pengajuan ketidakhadiran</div></a>
                </li> 
                @endif */ ?>
                @if(\UserAuth::hasAccess('Absensi.shift'))
                <li class="sidenav-item{{ strpos($routeName, 'shift_personal.index') === 0 ? ' active' : '' }}">
                    <a href="{{ route('shift_personal.index') }}" class="sidenav-link"><div>Manage Shift per Pegawai</div></a>
                </li> 
                @endif   
                @if(\UserAuth::hasAccess('Master.mesinabsen'))
                <li class="sidenav-item{{ strpos($routeName, 'absensi_upload') === 0 ? ' active' : '' }}">
                    <a href="{{ route('absensi_upload') }}" class="sidenav-link"><div>Upload Data Absensi</div></a>
                </li>
                @endif            
            </ul>
        </li>
        @endif

        @if(\UserAuth::hasAccess('Laporan'))
        <li class="sidenav-item{{ strpos($routeName, 'laporan.') === 0 ? ' active open' : '' }}">
            <a href="javascript:void(0)" class="sidenav-link sidenav-toggle"><i class="sidenav-icon ion ion-md-document"></i><div>Laporan</div></a>
            <ul class="sidenav-menu">
                @if(\UserAuth::hasAccess('Laporan.kehadiranharian'))
                <li class="sidenav-item{{ $routeName == 'laporan.kehadiran_harian' ? ' active' : '' }}">
                    <a href="{{ route('laporan.kehadiran_harian') }}" class="sidenav-link"><div>Kehadiran Harian</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Laporan.rekapkehadiran'))
                <li class="sidenav-item{{ $routeName == 'laporan.rekap_kehadiran' ? ' active' : '' }}">
                    <a href="{{ route('laporan.rekap_kehadiran') }}" class="sidenav-link"><div>Rekap Kehadiran</div></a>
                </li>
                @endif
                @if(\UserAuth::hasAccess('Laporan.jejakkehadiran'))
                <li class="sidenav-item{{ $routeName == 'laporan.jejak_kehadiran' ? ' active' : '' }}">
                    <a href="{{ route('laporan.jejak_kehadiran') }}" class="sidenav-link"><div>Jejak Kehadiran</div></a>
                </li>
                @endif                 
            </ul>
        </li>
        @endif

    </ul>
</div>
