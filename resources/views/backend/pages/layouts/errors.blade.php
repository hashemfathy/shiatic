@if ($errors->any())
<div class="pcoded-content">
	<!-- [ breadcrumb ] start -->
	<div class="alert alert-danger alert-dismissible fade show col-md-12" role="alert">
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
				aria-hidden="true">×</span></button>
	</div>
</div>
@endif
@if (session()->has('success'))
<div class="pcoded-content">
	<!-- [ breadcrumb ] start -->
	<div class="alert alert-success alert-dismissible fade show col-md-12" role="alert">
		<ul>
			<li>{{ session()->get('success') }}</li>
			
		</ul>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
				aria-hidden="true">×</span></button>
	</div>
</div>
@endif