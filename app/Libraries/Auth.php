<?php
namespace App\Libraries;
use App\User;
use App\Family;
use App\Libraries\Utils;
use App\Libraries\BaseLib;
class Auth extends BaseLib {

	public function register(array $data)
	{
		if(empty($data))
		{
			$this->set_error('errors.agrumentsNotFound');
			return array('errors'=>$this->errors(),'type_msg'=>'danger','result'=>false);
		}
		$user=new User;
        $user->username=$data['username'];
        $user->email=$data['email'];
        $user->password= bcrypt($data['password']);
        if($user->save()){
            $family=new Family;
            $family->name=$data['name'];
            if($user->family()->save($family))
            {
				return $this->deactivateAccountSendEmail($user);
			}
            else
            {
            	$this->set_error('error_on_save');
            	return false;
            	return array('errors'=>$this->errors(),'result'=>false,'type_msg'=>'danger');
            }
        }
        else
        {
        	$this->set_error('error_on_save');
        	return false;
        	return array('errors'=>$this->errors(),'type_msg'=>'danger','result'=>false);
        }
	}
	public function verify_account($id,$code)
	{
		// check if arguments not null
        if(!isset($code) && !isset($id))
        {
        	$this->set_error("errors.agrumentsNotFound");
        	return false;
        }
        //gets the user by id
	    $user =User::where("id",'=',$id)->first();
	    // check if user found
        if(!$user)
        {
        	$this->set_error("auth.errors.verify_account_user_not_found");
    		return false;
    	}
    	// check if user not activated yet
    	if($user->activated==1){
    		$this->set_error('auth.errors.account_activated_yet');
    		return false;
    	}
    	// check if codes match
    	if(strcmp($user->activation_code,$code)!=0){
    		$this->set_error('auth.errors.token_mismatch');
    		return false;
    	}
        // check if now activation_date + confirmation_expire - now
        $now = time();
        $latest = $user->activation_date;
        $latest = strtotime($latest);
        $diff = (($latest+(config('auth.confirmation.confirmation_expire')*60))-$now);
        if($diff > 0)
        {
        	// check if activate user by email usccess
			if($user->activateByEmail())
			{
			    $this->set_message('msg.activateByEmail_successful');
			    return true;					}
			else
			{
			    $this->set_error('auth.errors.activateByEmail_unsuccessful');
			    return false;
			}
        }
        else
        {
        	$this->set_error('auth.errors.link_ressend_email');
            return false;
        }
	}
	public function resendEmailActivation($field)
	{
		if(!isset($field))
		{
        	$this->set_error("errors.agrumentsNotFound");
        	return false;
		}
		$user=User::where('email','=',$field)->orWhere('username','=',$field)->first();
		if($user)
		{

			if($user->activated==0)
			{
				return $this->deactivateAccountSendEmail($user);
			}
			else
			{
				$this->set_error('user_activated yet');
				return false;
			}
		}
		else{
		$this->set_error('resend_email_activation_unsuccessfull_not_exists');
		return false;}
	}
	public function deactivateAccountSendEmail($user)
	{
		 $deactivate=$user->deactivateByEmail();
        if($deactivate)
        {
        	if(Utils::sendEmail([
        		'template'=>'emails.activation_account_email',
        		'subject'=>'Activate Accout',
        		'from'=>'sergiregu888@gmail.com',
        		'to'=>$user->email,
        		'data'=>array('activation_code'=>$user->activation_code,"id"=>$user->id),
        		'message'=>'',
        		'user'=>'Activate Account',
        		'had_attached_files'=>false
				]))
			{
				$this->set_message('Emeil sened');
				return true;
				return array('messages'=>$this->messages(),'type_msg'=>'success','result'=>true);
			}
			else
			{
				$this->set_error('error_send_activation_email');
				return false;
				return array('errors'=>$this->errors(),'result'=>false,'type_msg'=>'danger');
			}
		}
		else
		{
			$this->set_error('error_deactivate_account');
			return false;
			return array('errors'=>$this->errors(),'result'=>false,'type_msg'=>'danger');
		}
	}
}