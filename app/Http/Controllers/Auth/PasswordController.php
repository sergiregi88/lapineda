<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\PasswordBroker;
use App\User;
//use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    //use ResetsPasswords;
 /**
     * Create a new password controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\Guard  $auth
     * @param  \Illuminate\Contracts\Auth\PasswordBroker  $passwords
     * @return void
     */
    public function __construct(Guard $auth, PasswordBroker $passwords)
    {
        $this->auth = $auth;
        $this->passwords = $passwords;

        // With this, when logged says: "You're logged!" and not send the email token
        //$this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postEmail(Request $request)
    {
        $validator = \Validator::make(
            ['email' => $request->get('email')],
            ['email' => 'required|email|min:6|max:255']
        );

        if($validator->passes()) {

            $user=User::where('email','=',$request->onLy('email'))->first();
            if(!$user)
            {
                 return \Response::json(['success' => 'false', 'status' => trans(\PasswordBroker::INVALID_USER)]);
            }
            else{

                if(!$user->check_activate_email())
                {
                    return \Response::json(['success' => 'false', 'status' => trans('error.auth.user_not_actived')]);
                }
            }
            $response = $this->passwords->sendResetLink($request->only('email'), function ($m) {
                $m->subject($this->getEmailSubject());
            });

            switch ($response) {
                case PasswordBroker::RESET_LINK_SENT:
                    return \Response::json(['success' => 'true']);
                    //return redirect()->back()->with('status', trans($response));

                case PasswordBroker::INVALID_USER:
                    return \Response::json(['success' => 'false', 'status' => trans(\PasswordBroker::INVALID_USER)]);
                    //return redirect()->back()->withErrors(['username' => trans($response)]);
            }
        } else {
            return \Response::json(['error' => [
                'messages' => $validator->getMessageBag(),
                'rules' => $validator->getRules()
            ]]);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  Request  $request
     * @return Response
     */
    public function postReset(Request $request)
    {
        // $this->validate($request, [
        //     'token' => 'required',
        //     'email' => 'required|email|min:6|max:255',
        //     'password' => 'required|confirmed',
        // ]);
       $validator= \Validator::make($request->all(),[
            'token' => 'required',
            'email' => 'required|email|min:6|max:255',
            'password' => 'required|confirmed',
        ]);
       if($validator->fails())
       {
             return \Response::json(['error' => [
                'messages' => $validator->getMessageBag(),
                'rules' => $validator->getRules()
            ]]);
       }
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = $this->passwords->reset($credentials, function($user, $password)
        {
            if($user->activated==0)
                return "pasword.user_not_active";
            $user->password = bcrypt($password);
            $user->save();
            $this->auth->login($user);
        });

        switch ($response)
        {
            case PasswordBroker::PASSWORD_RESET:
                return \Response::json(['success' => 'true']);
                //return redirect($this->redirectPath());

            default:
                return \Response::json(['success' => 'false', 'status' => trans($response)]);
                /*return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);*/
        }
    }
    public function getEmailSubject()
    {
        return "reset Pasword";
    }

}
