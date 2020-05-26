

<?php $__env->startSection('layout-content'); ?>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-1 layout-without-sidenav">
    <div class="layout-inner">

        <!-- Layout navbar -->
        <?php echo $__env->make('layouts.includes.layout-navbar', ['hide_layout_sidenav_toggle' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- Layout container -->
        <div class="layout-container">

            <!-- Layout content -->
            <div class="layout-content">
              <!-- Layout sidenav -->
                <?php echo $__env->make('layouts.includes.layout-sidenav', ['layout_sidenav_horizontal' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <!-- Content -->
                <div class="container-fluid flex-grow-1 container-p-y">
                    <?php echo $__env->yieldContent('content'); ?>
                </div>
                <!-- / Content -->

                <!-- Layout footer -->
                <?php echo $__env->make('layouts.includes.layout-footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <!-- Layout content -->

        </div>
        <!-- / Layout container -->

    </div>
</div>
<!-- / Layout wrapper -->
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.application', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/app/MainApp/resources/views/layouts/layout-horizontal-sidenav.blade.php ENDPATH**/ ?>