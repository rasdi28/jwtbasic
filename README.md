# JSON WEB SOCKET WITH LUMEN

1. install JWT composer

```
composer require tymon/jwt-auth

```

2. uncomment and add script in bootstrap/app.php

```
//uncomment auth in this line
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

// Uncomment this line
$app->register(App\Providers\AuthServiceProvider::class);

// Add this line
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);

```

3. generate secret key dengan install

```
php artisan jwt:secret
```

4. change User.php in models with add ymon\JWTAuth\Contracts\JWTSubject and 2 method getJWTIdentifier() and GetJWTCustomClaims() 

```
<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject; 

class User extends Model implements AuthenticatableContract, AuthorizableContract,JWTSubject
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}


```


5. create config/auth.php and add script below this

```
'defaults' => [
    'guard' => 'api',
    'passwords' => 'users',
],


'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],

```

6. create AuthController.php in controller folder

```
<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
        $this->validate($request,[
            'username'=>'required|unique:users,username,1,id',
            'password'=>'required|confirmed'
        ]);

        $username = $request->input('username');
        $password = Hash::make($request->input('password'));


        
        User::create([
            'username'=> $username,
            'password'=>$password
        ]);
        return response()->json([
            'status'=>'Success',
            'operaton'=>'create'
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['username', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
```

7. edit your router example

```
$router->group(['prefix'=>'api'], function() use ($router) {
    $router->post('register','AuthController@register');
    $router->post('login','AuthController@login');
} );

```

8. make your router

```

$router->group(['prefix'=>'api','middleware'=>'auth'],function() use($router) {
    $router->get('me','AuthController@me');
    
    
} );


$router->group(['prefix'=>'api','middleware'=>'auth'],function() use($router) {
    $router->post('product','ProductController@create'); 
    
} );


```

## have a coding....