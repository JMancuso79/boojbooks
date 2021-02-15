@extends('layouts.app')

@section('content')

	<div class="container">
	    <!-- Not authenticated -->
	    @guest
			<div class="alert alert-dark" role="alert">
				<a href="/login">Click here</a> to sign in.
			</div>
	    <!-- Authenticated -->
	    @else
		    <div class="row justify-content-center">
		        <div class="col-12">
		        	<!-- Back button -->
			        <div>
			            <a href="/" class="btn btn-secondary">
			                <i class="fas fa-chevron-left"></i> Back to Books
			            </a> 
			        </div>
			        <!-- Details component -->
		        	<book-details id="{{$id}}"></book-details>
		        </div>
		    </div>
		@endguest
	</div>

@endsection
