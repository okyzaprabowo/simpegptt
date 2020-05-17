<?php if(Session::has('alert') || $errors->any()): ?>
<?php 
$alert = Session::get('alert');
Session::forget('alert');
Session::save();
$availbleAlert = ['success','info','warning','danger'];
$alertType = in_array($alert['type'],$availbleAlert)?$alert['type']:'info';
?>
<div class="alert alert-dark-<?php echo e($errors->any()?'warning':$alertType); ?> alert-dismissible fade show my-4">
	<button type="button" class="close" data-dismiss="alert">×</button>
	<?php echo $alert['message']; ?>

	<?php if($errors->any()): ?>
	<ul>
	<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<li><?php echo e($error); ?></li>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</ul>
	<?php endif; ?>
</div>
<?php endif; ?><?php /**PATH /Users/arisoftindonesia/Documents/Tekmira/home/resources/views/alert.blade.php ENDPATH**/ ?>