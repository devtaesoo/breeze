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
use Laravel\Socialite\Facades\Socialite;

use App\Models\User;
use App\Models\SocialAccount;

class SocialController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //현재 페이스북, 트위터, 링크드인, 구글, 깃허브, 깃랩 그리고 Bitbucket

    public function redirect($provider){
        $url = Socialite::driver($provider)->redirect()->getTargetUrl();

        return Inertia::location($url);
    }

    public function callback($provider){
        $socialUser  = Socialite::driver($provider)->stateless()->user();

        $socialAccount = SocialAccount::where([
            'provider' => $provider,
            'provider_user_id' => $socialUser->getId()
        ])->first();

        if($socialAccount){
            $user = User::find($socialAccount->user_id);
        }else{
            switch($provider){
                case 'github':
                    $data = [
                        'name' => $socialUser->nickname,
                        'email' => $socialUser->email,
                        'password' => md5($socialUser->id),
                        'profile_photo_path' => $socialUser->avatar
                    ];
                    break;
                case 'google':
                    $data = [];
                    break;
                case 'facebook':
                    $data = [];
                    break;
            }

            $user = User::firstOrCreate([
                'email' => $socialUser->email,
                'name' => $socialUser->nickname,
            ], $data);

            $user->socialAccounts()->updateOrCreate([
                'provider' => $provider,
                'provider_user_id' => $socialUser->getId(),
            ], [
                'github_token' => $socialUser->token,
                'github_refresh_token' => $socialUser->refreshToken
            ]);
        }

        $token = $user->createToken('AUTH_TOKEN')->plainTextToken;

        return Inertia::render('Auth/Followup/Index', [
            'access_token' => $token
        ]);
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
