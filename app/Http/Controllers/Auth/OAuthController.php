<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Keygen\Keygen;
use Inertia\Inertia;

class OAuthController extends Controller
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

        $this->clientId = env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID', '');
        $this->redirectUri = env('PASSPORT_CALLBACK_REDIRECT_URI', '');
        $this->clientSecret = env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET', '');
        $this->redirectHost = $request->getSchemeAndHttpHost();
        $this->hostname = env('APP_URL', '');
        $this->http = new GuzzleClient();
    }

    /**
     * Display a listing of the resource.
     */

    public function redirect(){
        $request = request();
        $request->session()->put('state', $state = Str::random(40));

        $query = http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectHost.$this->redirectUri,
            'response_type' => 'code',
            'scope' => '',
            'state' => $state,
            // 'prompt' => '', // "none", "consent", or "login"
        ]);


        $url = $this->hostname.'/oauth/authorize?'.$query;

        return Inertia::location($url);
    }

    public function callback(){
        dd('her');
        $request = request();
        $inputs = $request->input();
        $state = $request->session()->pull('state');

        $validator = Validator::make($inputs, [
            'code' => ['required', 'string']
        ], [
            'code' => '인가코드가 필요합니다.'
        ]);

        if($validator->fails()) {
            return ['success' => false, 'message' => trans($validator->errors()->first()), 'state' => $state];
            //return view('error', $validator->errors()->first());
        }

        //access token 요청
        $response = $this->getToken();

        if($response->successful()){

        }

        $accessToken = $response['data']['access_token'];

        // $userInfo = $this->getUserInfo($accessToken);

        //token 으로 유저정보 조회

        // $provider = OAuthProvider::where('slug', 'kakao')->first();

        // $oauthProvider = OAuthAccount::where([
        //     'provider_id' => $provider->id,
        //     'access_token' => $accessToken
        // ])->first();

        // $user = User::firstOrCreate([
        //     'id' => $oauthProvider->user_id
        // ],[
        //     'name' => 'kakao'.Keygen::alphanum(12)->generate,
        //     'email' => '',
        //     'email_verified_at' => '',
        //     'password' => Hash::make($oauthProvider->user_id)
        // ]);


        Auth::login($user);

        return Inertia::render('/registration-followup');
    }

    public function getToken(){
        $request = request();
        $state = $request->session()->pull('state');

        throw_unless(
            strlen($state) > 0 && $state === $request->state,
            InvalidArgumentException::class,
            'Invalid state value.'
        );

        dd($request);

        $response = Http::asForm()->post(env('APP_URL', '').'/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectHost.$this->redirectUri,
            'code' => $request->code,
        ]);

        return $response->json();
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
