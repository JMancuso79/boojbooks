@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
        	<!-- Back button -->
	        <div>
	            <a href="/" class="btn btn-secondary">
	                <i class="fas fa-chevron-left"></i> Back to Books
	            </a> 
	        </div>
	        <!-- Reading list component -->
            <reading-list></reading-list>
        </div>
    </div>
</div>
@endsection
