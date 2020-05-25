

<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
##parent-placeholder-16728d18790deb58b3b8c1df74f06e536b532695##
    <!-- Dependencies -->
    <!-- <script src="<?php echo e(asset('/webdist/vendor/libs/tableexport/tableexport.js')); ?>"></script>
    <script src="<?php echo e(asset('/webdist/vendor/libs/moment/moment.js')); ?>"></script> -->
    <script src="<?php echo e(asset('/webdist/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')); ?>"></script>
    
    <script src="<?php echo e(asset('/webdist/vendor/libs/bootstrap-table/bootstrap-table.js')); ?>"></script>
    <!-- <script src="<?php echo e(asset('/webdist/vendor/libs/bootstrap-table/extensions/export/export.js')); ?>"></script> -->
    
    <!-- <script src="<?php echo e(asset('/js/tables_bootstrap-table.js')); ?>"></script> -->
    <script>
        var apiPath = "<?php echo e(config('AppConfig.endpoint.api.moduser')); ?>";
        var vueLapRekapKehadiran = new Vue({
            el: '#vueLapRekapKehadiran',
            data: {
                formTitle: 'Tambah Baru',

                sortBy: "id",
                sortDesc: false,
                searchString: "",
                curPage: 1,
                perPage: <?php echo e($pegawai['limit']); ?>,
                perPageOption: [10,20,50,100],

                loadParams: {
                    params: {}
                },
 
                data: {
                   data: <?php echo json_encode($pegawai['data']); ?>,
                   count: <?php echo e($pegawai['count']); ?>,
                   currentPage: <?php echo e($pegawai['currentPage']); ?>,
                   limit: <?php echo e($pegawai['limit']); ?>,
                   offset: <?php echo e($pegawai['offset']); ?>,
                   pageCount: <?php echo e($pegawai['pageCount']); ?>

                },
            },
            created: function() {
                // this.form = JSON.parse(JSON.stringify(this.formEmpty));
            },
            mounted: function() {
                $('.datepicker-base').datepicker({format: 'yyyy-mm-dd',autoclose:true});
                $('[data-toggle="tooltip"]').tooltip();
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
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<div id="vueLapRekapKehadiran">
    <h4 class="d-flex justify-content-between align-items-center w-100 font-weight-bold py-1 mb-4">
        <div><span class="text-muted font-weight-light">Laporan /</span> Rekap Kehadiran</div>
    </h4>
    <?php echo $__env->make("alert", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div class="card">
        <div class="card-body">
            <form id="formFilterTanggal" action="<?php echo e(route('laporan.rekap_kehadiran')); ?>">
                <div class="row">
                    <div class="col-lg-6 col-sm-12 pb-3">       
                        Per page: &nbsp;                    
                        <select class="form-control form-control-sm d-inline-block w-auto" v-model="perPage" name="limit">
                            <option v-for="option in perPageOption" :value="option">{{option}}</option>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <div class="input-group">
                            <div class="input-group-text">
                                Dari
                            </div>
                            <input type="text" name="tanggal_start" class="datepicker-base start-date form-control" value="<?php echo e($tanggal_start); ?>"/>
                            <div class="input-group-text">
                                Ke
                            </div>
                            <input type="text" name="tanggal_end" class="datepicker-base end-date form-control" value="<?php echo e($tanggal_end); ?>"/>
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

        <div class="card-datatable table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th> 
                        <th>PIN</th> 

                        <?php 
                        foreach($jenisIjinKategori as $k => $kategori) {
                            if($k != 0 && $kategori['kategori']['tampil_rekap_kehadiran']){
                        ?>
                        <th data-toggle="tooltip" title="<?php echo e($kategori['kategori']['nama']); ?>"><?php echo e($kategori['kategori']['singkatan']); ?><br><small>Hari</small></th> 
                        <?php }} ?>

                        <th style="">Kerja<br><small>Hari</small></th> 
                        <th style="">Hadir<br><small>Hari</small></th>
                        <th style="">Alpha<br><small>Hari</small></th> 

                        <th style="">Telat<br><small>Jam</small></th> 
                        <th style="">Cepat<br><small>Jam</small></th> 
                        <th style="">+/-<br><small>Jam</small></th> 
                        <th style="">Total<br><small>Jam</small></th> 
                        <th style="">Kerja<br><small>%</small></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($pegawai['data'] as $i => $v) {
                        
                        ?>
                    <tr>
                        <td><?php echo e($i+1+(($pegawai['currentPage']-1)*$pegawai['limit'])); ?></td>
                        <td><?php echo e($v['nama']); ?></td>
                        <td><?php echo e($v['kode']); ?></td>
                        <?php 
                        foreach($jenisIjinKategori as $k => $kategori) {
                            if($k != 0 && $kategori['kategori']['tampil_kehadiran_harian']){
                        ?>
                        <td>
                            <?php echo e(isset($absensi[$v['id']]['jenisIjinKategori'][$k])?$absensi[$v['id']]['jenisIjinKategori'][$k]:0); ?>

                        </td> 
                        <?php }} ?>

                        <td>
                            <?php
                            if(isset($absensi[$v['id']]['kerja']) && isset($absensi[$v['id']]['libur'])){
                                echo $absensi[$v['id']]['kerja'];//-$absensi[$v['id']]['libur'];
                            }else{
                                echo 0;
                            }
                            ?>
                        </td>
                        <td>                        
                            <?php
                            if(isset($absensi[$v['id']]['hadir'])){
                                echo $absensi[$v['id']]['hadir'];
                            }else{
                                echo 0;
                            }
                            ?>
                        </td>
                        <td>                        
                            <?php
                            if(isset($absensi[$v['id']]['alpa'])){
                                echo $absensi[$v['id']]['alpa'] + $absensi[$v['id']]['tidaklengkap'];
                            }else{
                                echo 0;
                            }
                            ?>
                        </td>

                        <td>                       
                            <?php
                            if(isset($absensi[$v['id']]['telat_jam'])){
                                echo \Facades\App\MainApp\Repositories\Absensi::formatJamKerja($absensi[$v['id']]['telat_jam']);
                            }else{
                                echo '00:00:00';
                            }
                            ?>
                        </td>
                        <td>                       
                            <?php
                            if(isset($absensi[$v['id']]['cepat_jam'])){
                                echo \Facades\App\MainApp\Repositories\Absensi::formatJamKerja($absensi[$v['id']]['cepat_jam']);
                            }else{
                                echo '00:00:00';
                            }
                            ?>
                        </td>

                        <td>                      
                            <?php
                            //jumlah jam kerja seharusnya
                            if(isset($absensi[$v['id']]['total_jam']) && isset($absensi[$v['id']]['jam_kerja'])){
                                //total_jam = total jam kerja yg dilakukan
                                //jam_kerja = jam kerja seharusnya
                                echo \Facades\App\MainApp\Repositories\Absensi::formatJamKerja(
                                    $absensi[$v['id']]['total_jam']-$absensi[$v['id']]['jam_kerja']
                                );
                            }else{
                                echo '00:00:00';
                            }
                            ?> 
                        
                        </td>
                        <td>                      
                            <?php
                            //total jam kerja yg dilakukan
                            if(isset($absensi[$v['id']]['total_jam'])){
                                echo \Facades\App\MainApp\Repositories\Absensi::formatJamKerja(
                                    $absensi[$v['id']]['total_jam']);
                            }else{
                                echo '00:00:00';
                            }
                            ?>                        
                        </td>
                        <td>                      
                            <?php
                            if(isset($absensi[$v['id']]['total_jam']) && isset($absensi[$v['id']]['jam_kerja']) && !empty($absensi[$v['id']]['jam_kerja'])){
                                echo floor(($absensi[$v['id']]['total_jam']/$absensi[$v['id']]['jam_kerja'])*100).' %';
                            }else{
                                echo '0 %';
                            }
                            ?> 
                        
                        </td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>        

        <div class="card-body"> 
            <div class="row">  
                <div class="col-sm text-sm-left text-center pt-0">
                    <span class="text-muted">Page {{data.currentPage}} of {{data.pageCount}}</span>
                </div>
                <div class="col-sm pt-0">  
                    <?php echo e($pagination->links()); ?>

                </div>
            </div>       
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layout-horizontal-sidenav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/app/MainApp/Modules/Laporan/resources/views/laporan/rekap_kehadiran.blade.php ENDPATH**/ ?>