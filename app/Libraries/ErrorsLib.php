<?php
namespace App\Libraries;
class ErrorsLib{
	protected $type='';
	static const SUCCESS="success";
	static const DANGER="danger";
	static const INFO="info";
	static const WARINING='warning';
	private $errors=[];
	public function __contruct($type,$message);
	{

	}
	public function set_message($message_code,$type){
		$message=array('code'=>$message_code,"type"=>$type);
		$this->errors[]=$message;
		return $this;
	}
	public function messages(){
		return $this->errors;
	}
}