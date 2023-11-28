<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;

use Keygen\Keygen;

class KakaoController extends Controller
{
    protected $clientId;
	protected $redirectUri;
	protected $redirectHost;
	protected $hostname;
	protected $http;

    public function __construct()
    {
        $request = request();

        $this->clientId = env('KAKAO_REST_API_KEY', '');
        $this->redirectUri = env('KAKAO_REDIRECT_URI', '');
        $this->redirectHost = $request->getSchemeAndHttpHost();
        $this->hostname = 'https://kauth.kakao.com';
        $this->http = new GuzzleClient();
    }

    /**
     * Display a listing of the resource.
     */

    public function kakaoLogin()
    {
        //카카오 로그인 요청
        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectHost.$this->redirectUri,
            'response_type' => 'code',
            'state' => $this->getState()
        ]);

        $url = $this->hostname.'/oauth/authorize?'.$query;

        return redirect($url);
    }

    private function getState(){
		$state = Keygen::alphanum(12)->generate();

		session(['kakao-oauth-state' => $state]);

		logger('get state: '.session('kakao-oauth-state'));

		return $state;
	}


    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
