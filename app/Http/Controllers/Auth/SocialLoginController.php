<?php
 
namespace App\Http\Controllers\Auth;
 
use Sentinel;
use Socialite;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

 
class SocialLoginController extends Controller
{
    /**
    * Handle Social login request
    * @return response
    */
    public function redirectToProvider($social)
    {
        return Socialite::driver($social)->redirect();
    }
 
   /**
    * Obtain the user information from Social Logged in.
    * @param $social
    * @return Response
    */
   public function handleProviderCallback($social)
   {
        $user = Socialite::driver($social)->stateless()->user();
        // echo "<pre>";
        // var_dump($user->name);
        // var_dump($user->user['emails'][0]["value"]);
        // var_dump($user);
 
        // $authUser = $this->findOrCreateUser($user, $provider);
        
        $authUser = User::where('provider_id', $user->id)->first();
        if ($authUser) {
            $loginUser = $authUser;
        } else {
            $loginUser = User::create([
                'name' => $user->name,
                'email' => $user->user['emails'][0]["value"],
                'password' => bcrypt($user->user['emails'][0]["value"]),
                'provider_id' => $user->id
            ]);
        }
 
        Auth::login($loginUser, true);
 
        return redirect('/');
   }
}