@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Not authenticated -->
    @guest
        <div class="row justify-content-center">
            <div class="col-sm-12 text-center pt-5 pb-5">
                <h1>Welcome to the BOOJ Book Demo!</h1>
                <p>Sign in to add books about surfing to your reading list!</p>
                <p class="mt-4">
                    <a href="/login" class="btn btn-primary">Sign In</a>
                    <a href="/register" class="btn btn-secondary">Sign Up</a>
                </p>
            </div>
        </div>
    <!-- Authenticated -->
    @else
        <div>
            <book-list ></book-list>
        </div>
    @endguest
</div>
@endsection