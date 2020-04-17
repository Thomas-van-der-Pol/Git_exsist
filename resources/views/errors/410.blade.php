@extends('errors::illustrated-layout')

@section('code', '410')
@section('title', __('Missing'))

@section('image')
    <div style="background-image: url({{ asset('/svg/410.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
    </div>
@endsection

@section('message', __("Sorry, we couldn't find the page you are looking for."))
