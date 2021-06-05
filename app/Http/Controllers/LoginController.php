<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Models\Artist;
use SpotifyWebAPI;
use App\Models\Track;
use App\Models\User;


class LoginController extends Controller
{
    private $spotifyClient;
    private $userTracks;
    private $userArtists;

    public function __construct()
    {
        # Attempt to get access token
        if (!session()->has('accessToken')) {
            # Create the Spotify Client
            $this->spotifyClient = new SpotifyWebAPI\Session(
                env('SPOTIFY_ID'),
                env('SPOTIFY_SECRET'),
                env('REDIRECT_URI')
            );

            $options = [
                'scope' => [
                    'user-read-email',
                    'user-top-read',
                    'user-read-recently-played',
                    'user-follow-read'
                ],
            ];

            $response = $this->spotifyClient->getAuthorizeUrl($options);

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
                'user-read-recently-played',
                'user-follow-read'
            ],
        ];

        $response = $this->spotifyClient->getAuthorizeUrl($options);

        return redirect($response);

    }

    public function index()
    {
        $code = request('code');

        if ($code != '') {

            $this->spotifyClient->requestAccessToken($code);

            $accessToken = $this->spotifyClient->getAccessToken();
            $refreshToken = $this->spotifyClient->getRefreshToken();
    
            $api = new SpotifyWebAPI\SpotifyWebAPI();
    
            $api->setAccessToken($accessToken);
    
            session(['accessToken' => $accessToken]);

            return redirect('/');
        }

        if (session()->has('accessToken')) {
            $token = session()->get('accessToken');

            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(session()->get('accessToken'));

            #gets the email of the user
            $user = $api->me();

            $user = User::firstOrCreate(

                ['email' => $user->email],
                ['code' => $token]

            );

            # get the tracks from the user
            $usersTopTracks = $api->getMyRecentTracks();
            $dataUserTracks= [];
            $dataUserArtists= [];

            $userArtists = $api->getUserFollowedArtists();

            $js_code = 'console.log(' . json_encode($userArtists, JSON_HEX_TAG) . 
            ');';
                if (true) {
                    $js_code = '<script>' . $js_code . '</script>';
                }
                echo $js_code;

            foreach ($usersTopTracks->items as $key) {

                $track = Track::firstOrCreate([
                    'name' => $key->track->name
                ]);
                array_push($dataUserTracks, $key->track);
            }

            foreach ($userArtists->artists->items as $key) {

                $artist = Artist::firstOrCreate([
                    'name' => $key->name
                ]);
                array_push($dataUserArtists, $key);
            }

            $this->userTracks = $dataUserTracks;
            $this->userArtists = $dataUserArtists;

            return view('userView', ['userTopTracks' => $this->userTracks , 'userArtists' => $this->userArtists]);
        } else {

            $this->userTracks = Track::all();
            $this->userArtists = Artist::all();

            return view('welcome', ['userTopTracks' => $this->userTracks , 'userArtists' => $this->userArtists]);
        }
    }

    public function signout()
    {
        session()->flush();
        return redirect('/');
    }
}
