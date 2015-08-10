<?php

namespace App\Http\Controllers\Auth;

use App\Libraries\Auth as LibAuth;
use App\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
    protected $auth;
    protected $request;
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    private $redirectToAfterLogin='dashboard';
    private $redirectToAfterLoginAdmin='admin/dashboard';
    public function __construct(Guard $auth,Request $request)
    {
        $this->middleware('guest', ['except' => 'getLogout']);
        $this->auth=$auth;
        $this->request=$request;
        $this->libAuth=new LibAuth;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorRegister(array $data)
    {
        return Validator::make($data, [
            'username'=>'required|max:255|unique:users',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorLogin(array $data)
    {
        return Validator::make($data, [
            'login'=>'required|max:255',
            'password' => 'required|min:6',
        ]);
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validatorResendResset(array $data)
    {
        return Validator::make($data, [
            'login' => 'required|email'
        ]);
    }
    /**
     * token generated a csrf_token
     * @return string token_string
     */
    public function token()
    {
        return response()->json(["csrf_token"=>csrf_token()],'200');
    }
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create()
    {
        $validate=$this->validatorRegister($this->request->all());
        if(!$validate->fails())
        {
            $result=$this->libAuth->register($this->request->all());
            if($result)
            {
                return response()->json([
                    'success'=>true,
                    'error'=>false,
                    'messages'=>$this->libAuth->messages()],200);
            }
            else
            {
                return response()->json([
                    'error'=>true,
                    'success'=>false,
                    'messages'=>$this->libAuth->errors()],200);
            }
        }
        else
        {
            return response()->json([
                'error'=>true,
                'success'=>false,
                "messages"=>$validate->getMessageBag()->toArray()],200);
        }
    }
    public function login()
    {
        $validate=$this->validatorLogin($this->request->all());
        if($validate)
        {
            $field = filter_var($this->request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $this->request->merge([$field => $this->request->input('login')]);
            if ($this->auth->attempt($this->request->only($field, 'password'),$this->request->has('remember')))
            {
                $user=\Auth::user();
               // dd($user->check_activate_email());
                if($user->check_activate_email())
                    return response()->json([
                    'success'=>true,
                    'error'=>false,
                    'user'=>$user,
                    'redirectUrl'=>$this->redirectToAfterLogin,
                    'messages'=>array('Login Succeesss')],200);
                else
                {
                     return response()->json([
                    'error'=>true,
                    'success'=>false,
                    "type_msg"=>"danger",
                    "redirectUrl"=>null,
                    "messages"=>array('errors'=>'Must activate account')],200);
                }

            }
            return response()->json([
                'error'=>true,
                'success'=>false,
                "type_msg"=>"danger",
                "redirectUrl"=>null,
                "messages"=>array('errors'=>'Invalid Username or Email / Password ')],200);
        }
        else
        {
            return response()->json([
                'error'=>true,
                'success'=>false,
                "type_msg"=>"danger",
                "redirectUrl"=>null,
                "messages"=>$validate->getMessageBag()->toArray()],200);
        }

    }
    public function verify_account($id,$token)
    {
        $libauth=new LibAuth;
        $result=$this->libAuth->verify_account($id,$token);

        if($result)
        {
            return response()->json([
                'success'=>true,
                'error'=>false,
                'redirectUrl'=>'login',
                'messages'=>$this->libAuth->messages()],200);
        }
        else
        {
            return response()->json([
                'error'=>true,
                'success'=>false,
                'messages'=>$this->libAuth->errors()],200);
        }
    }
    public function resend_activation_email()
    {
        $validate=$this->validatorResendResset($this->request->all());
        if(!$validate)
        {
             return response()->json([
                'error'=>true,
                'success'=>false,
                "type_msg"=>"danger",
                "redirectUrl"=>null,
                "messages"=>$validate->getMessageBag()->toArray()],200);

        }
        $field = filter_var($this->request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $this->request->merge([$field => $this->request->input('login')]);
        $result=$this->libAuth->resendEmailActivation($this->request->input($field));
        if($result)
        {
               return response()->json([
                'success'=>true,
                'error'=>false,
                'redirectUrl'=>null,
                'messages'=>$this->libAuth->messages()],200);
        }
       else
        {
            return response()->json([
                'error'=>true,
                'success'=>false,
                'redirectUrl'=>null,
                'messages'=>$this->libAuth->errors()],200);
        }
    }

}
