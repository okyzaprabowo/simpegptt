@if(Session::has('alert') || $errors->any())
<?php 
$alert = Session::get('alert');
Session::forget('alert');
Session::save();
$availbleAlert = ['success','info','warning','danger'];
$alertType = in_array($alert['type'],$availbleAlert)?$alert['type']:'info';
?>
<div class="alert alert-dark-{{ $errors->any()?'warning':$alertType }} alert-dismissible fade show my-4">
	<button type="button" class="close" data-dismiss="alert">×</button>
	{!! $alert['message'] !!}
	@if($errors->any())
	<ul>
	@foreach ($errors->all() as $error)
		<li>{{ $error }}</li>
	@endforeach
	</ul>
	@endif
</div>
@endif