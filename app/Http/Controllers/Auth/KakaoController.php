<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Validator;

use App\Models\OAuthProvider;
use App\Models\OAuthAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Keygen\Keygen;
use Inertia\Inertia;
use Illuminate\Support\Str;


class KakaoController extends Controller
{
    protected $clientId;
	protected $redirectUri;
    protected $clientSecret;
	protected $redirectHost;
	protected $hostname;
	protected $http;

    public function __construct()
    {
        $request = request();

        $this->clientId = env('KAKAO_REST_API_KEY', '');
        $this->redirectUri = env('KAKAO_REDIRECT_URI', '');
        $this->clientSecret = env('KAKAO_CLIENT_SECRET_KEY', '');
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

        return Inertia::location($url);
    }


    public function kakaoLoginCallback(){
        $request = request();
        $inputs = $request->input();

        $validator = Validator::make($inputs, [
            'code' => ['required', 'string']
        ], [
            'code' => '카카오 인가코드가 필요합니다.'
        ]);

        if($validator->fails()) {
            return ['success' => false, 'message' => trans($validator->errors()->first()), 'state' => session('kakao-oauth-state')];
            //return view('error', $validator->errors()->first());
        }

        $tokenData = $this->getToken();

        if($tokenData['success'] == false && $tokenData['code'] != 200){
            return ['success' => false, 'message' => '오류가 발생하였습니다.', 'state' => session('kakao-oauth-state')];
        }

        $accessToken = $tokenData['data']['access_token'];

        //token 으로 유저정보 조회
        $provider = OAuthProvider::where('slug', 'kakao')->first();

        $oauthProvider = OAuthAccount::where([
            'provider_id' => $provider->id,
            'access_token' => $accessToken
        ])->first();

        $kakaoUser = $this->getUserInfo($accessToken);

        $user = User::firstOrCreate([
            'id' => $oauthProvider->user_id
        ],[
            'name' => 'kakao'.Keygen::numeric(12),
            'email' => $kakaoUser['data']->kakao_account->email,
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(32))
        ]);

        Auth::user($user);

        return Inertia::render('RegisterFollowUp');
    }

    public function getToken(){
        $request = request();
        $inputs = $request->input();

        $validator = Validator::make($inputs, [
            'code' => ['required', 'string']
        ], [
            'code' => '카카오 인가코드가 필요합니다.'
        ]);

        if($validator->fails()){
            return ['success' => false, 'message' => $validator->errors()->first()];
        }

        //토큰 발급 시, 보안을 강화하기 위해 추가 확인하는 코드
        $url = 'https://kauth.kakao.com/oauth/token';

        $response = $this->http->request('POST', $url, [
            'headers' => [
                'Content-type' => 'application/x-www-form-urlencoded;charset=utf-8'
            ],
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectHost.$this->redirectUri,
                'code' => $inputs['code'],
                'client_secret' => $this->clientSecret
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $responseData = json_decode($response->getBody());

            $result = [
                'success' => true,
                'code' => $response->getStatusCode(),
                'data' => [
                    'access_token' => $responseData->access_token,
                    'refresh_token' => $responseData->refresh_token,
                    'expires_in' => $responseData->expires_in,
                    'refresh_token_expires_in' => $responseData->refresh_token_expires_in
                ]
            ];
        }else{
            $result = [
                'success' => false,
                'code' => $response->getStatusCode()
            ];
        }

        return $result;
    }

    public function getUserInfo($accessToken){
        $url = 'https://kapi.kakao.com/v2/user/me';

        $response = $this->http->request('POST', $url, [
            'headers' => [
                'Content-type' => 'application/x-www-form-urlencoded;charset=utf-8',
                'Authorization' => 'Bearer '.$accessToken
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $responseData = json_decode($response->getBody());

            $result = [
                'success' => true,
                'code' => $response->getStatusCode(),
                'data' => $responseData->kakao_account
            ];
        }else{
            $result = [
                'success' => false,
                'code' => $response->getStatusCode()
            ];
        }

        return $result;
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
