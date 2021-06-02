<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Cache;
use Session;
use SpotifyWebAPI;
use View;
use App\Models\Track;


class LoginController extends Controller
{

    private $spotifyApi;
    private $spotifyClient;
    private $spotifyChart;
    private $userTracks;

    public function __construct()
    {
        // Attempt to get access token
        if (!Cache::has('accessToken')) {
            // Create the Spotify Client
            $this->spotifyClient = new SpotifyWebAPI\Session(
                env('SPOTIFY_ID'),
                env('SPOTIFY_SECRET'),
                env('REDIRECT_URI')
            );

            $response = $this->spotifyClient->getAuthorizeUrl($options);

            $js_code = 'console.log(' . json_encode($response, JSON_HEX_TAG) .
                ');';
            if (true) {
                $js_code = '<script>' . $js_code . '</script>';
            }
            echo $js_code;

            $code = request('code');

            if ($code != '') {
                $this->spotifyClient->requestAccessToken($code);

                $accessToken = $this->spotifyClient->getAccessToken();
                $refreshToken = $this->spotifyClient->getRefreshToken();

                $api = new SpotifyWebAPI\SpotifyWebAPI();

                $api->setAccessToken($accessToken);

                Cache::put('accessToken', $accessToken);
                #$usersTopTracks = $api->getMytop('tracks');
                $usersTopTracks = $api->getMyRecentTracks();
                $this->userTracks = $usersTopTracks->items; #track-> name

                # foreach ($usersTopTracks->items as $key) {
                #   Track::store($key);
                #}
                return redirect('/');
            }
            #$this->tracks = $usersTopTracks->items;
            #print_r(
            #    $api->getMytop('tracks')
            #);
        }
    }

    public function auth()
    {

        $this->spotifyClient = new SpotifyWebAPI\Session(
            env('SPOTIFY_ID'),
            env('SPOTIFY_SECRET'),
            env('REDIRECT_URI')
        );

        $options = [
            'scope' => [
                'user-read-email',
                'user-top-read',
                'user-read-recently-played'
            ],
        ];

        $response = $this->spotifyClient->getAuthorizeUrl($options);

        $js_code = 'console.log(' . json_encode($response, JSON_HEX_TAG) .
            ');';
        if (true) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;

        $code = request('code');

        if ($code != '') {
            $this->spotifyClient->requestAccessToken($code);

            $accessToken = $this->spotifyClient->getAccessToken();
            $refreshToken = $this->spotifyClient->getRefreshToken();

            $api = new SpotifyWebAPI\SpotifyWebAPI();

            $api->setAccessToken($accessToken);

            Cache::put('accessToken', $accessToken);

            return redirect($response);
        
        }
        else
        {
            return redirect('/');
        }

    }

    public function index()
    {

        #$playlist = $this->spotifyApi->getTrack('3yraHvsUkmnJjGhOrx1CSg?si=2b0ceb2b2fa74f35');
        $test = Cache::get('accessToken');

        $api = new SpotifyWebAPI\SpotifyWebAPI();
        $api->setAccessToken(Cache::get('accessToken'));

        $usersTopTracks = $api->getMyRecentTracks();
        $this->userTracks = $usersTopTracks->items;
        
        foreach ($usersTopTracks->items as $key) {

            $track = Track::firstOrCreate([
                'name' => $key->track->name
            ]);
        }

        $js_code = 'console.log(' . json_encode($this->userTracks, JSON_HEX_TAG) .
            ');';
        if (true) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
        #$of = array($this->tracks);

        #$mytop = $this->spotifyApi->getMyCurrentTrack();
        return view('welcome', ['userTopTracks' => $this->userTracks]);
    }
}
