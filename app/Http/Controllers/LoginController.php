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
        }
    }

    public function auth()
    {
        #adding the scoped to the spotify client
        $options = [
            'scope' => [
                'user-read-email',
                'user-top-read',
                'user-read-recently-played',
                'user-follow-read'
            ],
        ];

        #get the link for the autorization
        $response = $this->spotifyClient->getAuthorizeUrl($options);

        #redirect to the link for autorization
        return redirect($response);
    }

    public function index()
    {
        #autorization code
        $code = request('code');

        #save the code to the session and redirect back to root 
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

            #cretes a spotify API
            $api = new SpotifyWebAPI\SpotifyWebAPI();
            $api->setAccessToken(session()->get('accessToken'));

            #gets the email of the user
            $user = $api->me();

            # creates a new User
            $user = User::firstOrCreate(

                ['email' => $user->email],
                ['code' => $token]

            );

            # get the tracks from the user
            $usersTopTracks = $api->getMyRecentTracks();

            #get the artists from the user
            $userArtists = $api->getUserFollowedArtists();

            #data to be send to the view
            $dataUserTracks = [];
            $dataUserArtists = [];

            #saves new tracks
            foreach ($usersTopTracks->items as $key) {

                $track = Track::firstOrCreate([
                    'name' => $key->track->name
                ]);
                array_push($dataUserTracks, $key->track);
            }

            #saves new artists
            foreach ($userArtists->artists->items as $key) {

                $artist = Artist::firstOrCreate([
                    'name' => $key->name
                ]);
                array_push($dataUserArtists, $key);
            }

            $this->userTracks = $dataUserTracks;
            $this->userArtists = $dataUserArtists;

            return view('userView', ['userTopTracks' => $this->userTracks, 'userArtists' => $this->userArtists]);
        } else {

            #fetches data from the DB 
            $this->userTracks = Track::all();
            $this->userArtists = Artist::all();

            return view('welcome', ['userTopTracks' => $this->userTracks, 'userArtists' => $this->userArtists]);
        }
    }

    public function signout()
    {
        #too simple
        session()->flush();
        return redirect('/');
    }
}
