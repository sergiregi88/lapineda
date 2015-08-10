<?php

namespace App;
use App\Libraries\Utils;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use App\BaseModel;
class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password','activation_code'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    /**
     *
     *
     * @var string
     */
    /**
     * @metodh
     * family
     *
     */
    public function family()
    {
        return $this->hasOne('App\Family');
    }
    public function deactivateByEmail()
    {
        $this->attributes['activation_date']=date('Y-m-d H:i:s');
        $this->attributes['activation_code']=sha1(md5(microtime()));
        $this->attributes['activated']=0;
        return $this->save();
    }
    public function activateByEmail()
    {
        $this->attributes['activated']=1;
        $this->attributes['activation_date']=NULL;
        $this->attributes['activation_code']=NULL;
        return $this->save();
    }
    public function check_activate_email()
    {
       return $this->attributes['activated'];
    }
    public function checkEmailExists($email)
    {
        return $this->where('email','=',$email)->exists();
    }
    public function checkActiveUser()
    {
        return $this->attributes['active'];
    }
    public function getEmailForPasswordReset() {
        return $this->email;
    }

}
