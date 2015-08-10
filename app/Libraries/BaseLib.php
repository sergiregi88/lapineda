<?php
namespace App\Libraries;
use Log;

class BaseLib{
	protected $messages=[];
	protected $errors=[];

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
 protected $error_start_delimiter="";
 protected $error_end_delimiter="";

 protected $message_start_delimiter="";
 protected $message_end_delimiter="";

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
			if(is_array($error))
				$errorLang = trans($error['name']) ? Lang::get($error['name'],$error['data']) : '##' . $error['name'] . '##';
			else
				$errorLang = trans($error) ? trans($error) : "##".$error."##";
			Log::error($errorLang);
			$_output .= $this->error_start_delimiter . $errorLang . $this->error_end_delimiter;
		}

		return $_output;
	}

}
