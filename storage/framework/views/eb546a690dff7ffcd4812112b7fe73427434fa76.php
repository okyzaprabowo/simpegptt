<?php $__env->startSection('styles'); ?>
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo e(asset('/webdist/vendor/css/pages/authentication.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="authentication-wrapper authentication-3">
        <div class="authentication-inner">

            <!-- Side container -->
            <!-- Do not display the container on extra small, small and medium screens -->
            <div class="d-none d-lg-flex col-lg-8 align-items-center ui-bg-cover ui-bg-overlay-container p-5" style="background-image: url('/img/bg/21.jpg');">
                <div class="ui-bg-overlay bg-dark opacity-50"></div>

                <!-- Text -->
                <div class="w-100 text-white px-5">
                    <!-- <h1 class="display-2 font-weight-bolder mb-4">JOIN OUR<br>COMMUNITY</h1>
                    <div class="text-large font-weight-light">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vehicula ex eu gravida faucibus. Suspendisse viverra pharetra purus. Proin fringilla ac lorem at sagittis. Proin tincidunt dui et nunc ultricies dignissim.
                    </div> -->
                </div>
                <!-- /.Text -->
            </div>
            <!-- / Side container -->

            <!-- Form container -->
            <div class="d-flex col-lg-4 align-items-center bg-white p-5">
                <!-- Inner container -->
                <!-- Have to add `.d-flex` to control width via `.col-*` classes -->
                <div class="d-flex col-sm-7 col-md-5 col-lg-12 px-0 px-xl-4 mx-auto">
                    <div class="w-100">

                        <!-- Logo -->
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="ui-w-60">
                                <div class="w-100 position-relative" style="padding-bottom: 54%">
                                    <img class="w-100 position-absolute" src="<?php echo e(asset('assets/images/logo.gif')); ?>" />
                                </div>
                            </div>
                        </div>
                        <!-- / Logo -->

                        <h4 class="text-center text-lighter font-weight-normal mt-5 mb-0"><?php echo e(config('tenant.name')?config('tenant.name'):config('AppConfig.system.template.admin.title')); ?></h4>
                        <?php echo $__env->make('alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <!-- Form -->
                        <form class="my-5" action="<?php echo e(route('auth.login')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <div class="form-group">
                                <label class="form-label"><?php echo e(__('auth.login.usernamecaption')); ?></label>
                                <input name="username" type="text" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label d-flex justify-content-between align-items-end">
                                    <div><?php echo e(__('auth.login.passwordcaption')); ?></div>
                                </label>
                                <input name="password" type="password" class="form-control">
                            </div>

                            <div class="d-flex justify-content-between align-items-center m-0">
                                <?php if(config('AppConfig.packageLocal.moduser.login.rememberme')): ?>
                                <label class="custom-control custom-checkbox m-0">
                                    <input type="checkbox" class="custom-control-input" name="remember">
                                    <span class="custom-control-label"><?php echo e(__('auth.login.remember_me')); ?></span>
                                </label>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary"><?php echo e(__('auth.login.sigincaption')); ?></button>                            
                                <?php if(config('AppConfig.packageLocal.moduser.login.forgotpassword')): ?>
                                <a href="<?php echo e(route('auth.forgotPassword')); ?>" class="d-block small"><?php echo e(__('auth.login.forgotpassword')); ?></a>
                                <?php endif; ?>
                            </div>

                        </form>
                        <!-- / Form -->

                        <?php if(config('AppConfig.packageLocal.moduser.registration.enable')): ?>
                        <div class="text-center text-muted">
                            <?php echo e(__('auth.login.dont_have_an_account')); ?> <a href="<?php echo e(route('auth.register')); ?>"><?php echo e(__('auth.login.signupcaption')); ?></a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <!-- / Form container -->

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.layout-blank', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/app/MainApp/Modules/moduser/resources/views/auth/login.blade.php ENDPATH**/ ?>