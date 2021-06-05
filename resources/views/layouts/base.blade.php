<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="/css/main.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body>
        @yield('body')

        <section id="section-songs">
            <h2>Songs</h2>
        @foreach ($userTopTracks as $track)
        
            <p>{{ $track->name }}</p>
        @endforeach
        </section>
        
        <section id="section-artists">
            <h2>Artists</h2>
            @foreach ($userArtists as $artist)
            
                <p>{{ $artist->name }}</p>
            @endforeach
        </section>
        
    </body>
</html>
