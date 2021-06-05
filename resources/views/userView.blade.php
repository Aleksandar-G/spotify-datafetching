@extends('layouts.base')
@section('body')

<button type="button" class="btn btn-primary mt-5 mx-5" id="button" onclick="window.location='{{ url("/signout") }}'">logout</button>

@endsection