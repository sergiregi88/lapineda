<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Log;
class BaseModel extends Model{

	protected $errors=[];
	protected $messages=[];
/**
	 * set_message
	 *
	 * Set a message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_message($message)
	{
		$this->messages[] = $message;

		return $message;
	}

	/**
	 * messages
	 *
	 * Get the messages
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function messages()
	{
		$_output = '';
		foreach ($this->messages as $message)
		{
			$messageLang = trans($message) ? trans($message) : '##' . $message . '##';
			Log::info($messageLang);
			$_output .= $this->message_start_delimiter . $messageLang . $this->message_end_delimiter;
		}

		return $_output;
	}

	/**
	 * set_error
	 *
	 * Set an error message
	 *
	 * @return void
	 * @author Ben Edmunds
	 **/
	public function set_error($error,$data=false)
	{
		if(isset($data) and is_array($data))
		$this->errors[] = array("error"=>$error,'data'=>$data);
		else
		$this->errors[] = $error;
		return $error;
	}
	public function errors()
	{
		$_output = '';
		foreach ($this->errors as $error)
		{
			$errorLang = trans($error) ? is_array($error)?trans($error['name'],$error['data']):trans($error) : '##' . $error . '##';
			Log::error($errorLang);
			$_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
		}

		return $_output;
	}

}
