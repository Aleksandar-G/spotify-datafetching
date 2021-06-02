<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Cache;
use Session;
use SpotifyWebAPI;
use View;


class LoginController extends Controller
{

    private $spotifyApi;
    private $spotifyClient;
    private $spotifyChart;
    private $tracks;

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
        }
           $options = [
            'scope' => [
                'user-read-email',
                'user-top-read',
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

               if ($code != ''){
                $this->spotifyClient->requestAccessToken($code);
            
                $accessToken = $this->spotifyClient->getAccessToken();
                $refreshToken = $this->spotifyClient->getRefreshToken();

                $api = new SpotifyWebAPI\SpotifyWebAPI();

                $api->setAccessToken($accessToken);
                $haide = $api->getMytop('tracks');

                $this->tracks = $haide->items;
                #print_r(
                #    $api->getMytop('tracks')
                #);
               }


    }

    public function auth()
    {
        $options = [
            'scope' => [
                'user-read-email',
                'user-top-read',
            ],
        ];

        $response = $this->spotifyClient->getAuthorizeUrl($options);
        return redirect($response);

    }

    public function index()
    {

        #$playlist = $this->spotifyApi->getTrack('3yraHvsUkmnJjGhOrx1CSg?si=2b0ceb2b2fa74f35');
        $test = Cache::get('accessToken');
            $of = array($this->tracks);
                print_r(
                    $of
                );
        
        #$mytop = $this->spotifyApi->getMyCurrentTrack();
        return view('welcome');
    }

}
