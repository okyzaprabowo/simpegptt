<!DOCTYPE html>

<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="default-style layout-fixed layout-navbar-fixed">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('AppConfig.system.title')); ?></title>

    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    <?php if(config('AppConfig.system.web_admin.assets_template.font.fontawesome')): ?>
    <link rel="stylesheet" href="<?php echo e(asset('/dist/vendor/fonts/fontawesome.css')); ?>">
    <?php endif; ?>
    <?php if(config('AppConfig.system.web_admin.assets_template.font.ionicons')): ?>
    <link rel="stylesheet" href="<?php echo e(asset('/dist/vendor/fonts/ionicons.css')); ?>">
    <?php endif; ?>
    <?php if(config('AppConfig.system.web_admin.assets_template.font.linearicons')): ?>
    <link rel="stylesheet" href="<?php echo e(asset('/dist/vendor/fonts/linearicons.css')); ?>">
    <?php endif; ?>
    <?php if(config('AppConfig.system.web_admin.assets_template.font.open-iconic')): ?>
    <link rel="stylesheet" href="<?php echo e(asset('/dist/vendor/fonts/open-iconic.css')); ?>">
    <?php endif; ?>
    <?php if(config('AppConfig.system.web_admin.assets_template.font.pe-icon-7-stroke')): ?>
    <link rel="stylesheet" href="<?php echo e(asset('/dist/vendor/fonts/pe-icon-7-stroke.css')); ?>">
    <?php endif; ?>
    
    <link href="<?php echo e(asset('/dist/css/bootstrap.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('/dist/css/appwork.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('/dist/css/theme-app.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('/dist/css/colors.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('/dist/css/uikit.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('/dist/css/style.css')); ?>" rel="stylesheet">
    
    <?php if(config('AppConfig.system.web_admin.assets_link')): ?>
    <?php $__currentLoopData = config('AppConfig.system.web_admin.assets_link'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <link rel="stylesheet" href="<?php echo e(asset($value)); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

</head>
<body>

    <!-- Splash screen -->
    <div class="app-splash-screen" style="background: #fff; position: fixed; z-index: 99999999; top: 0; right: 0; bottom: 0; left: 0; opacity: 1; -webkit-transition: opacity .3s; transition: opacity .3s;">
      <div class="app-splash-screen-content" style="position: absolute; top: 50%; left: 50%; -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
        <span class="text-large font-weight-bolder"><?php echo e(config('AppConfig.system.title')); ?></span>
      </div>
    </div>
    <!-- / Splash screen -->

    <div id="app"></div>

    <?php if(config('AppConfig.system.web_admin.assets_js')): ?>
    <?php $__currentLoopData = config('AppConfig.system.web_admin.assets_js'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <script src="<?php echo e(asset($value)); ?>"></script>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <!-- Layout helpers -->
    <script src="<?php echo e(asset('/dist/vendor/js/layout-helpers.js')); ?>"></script>
    <script src="<?php echo e(asset('/dist/app.js')); ?>"></script>

</body>
</html>
<?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/resources/views/layouts/admin/main.blade.php ENDPATH**/ ?>