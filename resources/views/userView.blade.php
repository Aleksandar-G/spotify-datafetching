@extends('layouts.base')
@section('body')

<button type="button" onclick="window.location='{{ url("/logout") }}'" > logout</button>

<section>
    <h2>Songs</h2>
@foreach ($userTopTracks as $track)

    <p>{{ $track->track->name }}</p>
@endforeach

@endsection