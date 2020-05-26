<!DOCTYPE html>

<html lang="<?php echo e(app()->getLocale()); ?>" class="default-style">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('AppConfig.system.template.admin.title')); ?></title>

    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/fonts/fontawesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/fonts/ionicons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/fonts/linearicons.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/fonts/open-iconic.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/fonts/pe-icon-7-stroke.css')); ?>">

    <!-- Core stylesheets -->
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/css/bootstrap.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/css/appwork.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/css/theme-corporate.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/css/colors.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/css/uikit.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/assets/css/style.css')); ?>">

    <!-- Load polyfills -->
    <script src="<?php echo e(mix('/webdist/vendor/js/polyfills.js')); ?>"></script>
    <script>
        document['documentMode']===10&&document.write('<script src="https://polyfill.io/v3/polyfill.min.js?features=Intl.~locale.en"><\/script>');
        var localUrl = {
            logout: "<?php echo e(route('auth.logout')); ?>",
            login: "<?php echo e(route('auth.login')); ?>"
        };
    </script>

    <!-- Layout helpers -->
    <script src="<?php echo e(mix('/webdist/vendor/js/layout-helpers.js')); ?>"></script>

    <!-- Libs -->

    <!-- `perfect-scrollbar` library required by SideNav plugin -->
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/vendor/libs/toastr/toastr.css')); ?>">

    <?php echo $__env->yieldContent('styles'); ?>

    <!-- Application stylesheets -->
    <link rel="stylesheet" href="<?php echo e(mix('/webdist/css/application.css')); ?>">

</head>
<body>

    <?php echo $__env->yieldContent('layout-content'); ?>

    <!-- Core scripts -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->

    <script src="<?php echo e(mix('/webdist/vendor/libs/jquery/3.2.1/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(mix('/webdist/vendor/libs/popper/popper.js')); ?>"></script>
    <script src="<?php echo e(mix('/webdist/vendor/js/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(mix('/webdist/vendor/js/sidenav.js')); ?>"></script>

    <!-- Libs -->

    <!-- `perfect-scrollbar` library required by SideNav plugin -->
    <script src="<?php echo e(mix('/webdist/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
    <script src="<?php echo e(mix('/webdist/vendor/libs/toastr/toastr.js')); ?>"></script>

    <!-- Application javascripts -->
    <script src="<?php echo e(mix('/webdist/js/application.js')); ?>"></script>

    <?php echo $__env->make("alertModal", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        <?php if(UserAuth::isLogin()): ?>
        //set token di LocalApi
        window.axios.defaults.headers.common['Authorization'] = 'Bearer <?php echo UserAuth::getToken('api_token') ?>'; 
        <?php endif; ?>
        //convert array to query string
        function params(object) {
            var parameters = [];
            for (var property in object) {
                if (object.hasOwnProperty(property)) {
                    parameters.push(encodeURI(property + '=' + object[property]));
                }
            }

            return parameters.join('&');
        }

        //parsing error local api
        function localApiErrorParse(res) {
            
            let err = { status: 400, message: "request error" , errors: []};
            //jika error server
            if (!res.data) {
                err.message = res.message;
            } else {
                err.message = res.data.message;
                err.status = res.data.status;

                if(res.data.errors){
                    err.errors = res.data.errors;
                    _.forEach(res.data.errors,(v,i)=>{
                        if(v!=true)err.message += "<br> - " + v;
                    });
                }
            }

            return err;
        }
    </script>
    
    <?php echo $__env->yieldContent('scripts'); ?>

</body>
</html>
<?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/simpegptt/app/MainApp/resources/views/layouts/application.blade.php ENDPATH**/ ?>