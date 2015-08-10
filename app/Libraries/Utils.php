<?php
namespace App\Libraries;
use Mail;
use Log;
class Utils {
	public static function sendEmail(array $data){
		$required_keys=['subject','user','had_attached_files',
		'data','from','to','message','template'];

		if (count(array_diff($required_keys, array_keys($data))) == 0)
		{

			Mail::send($data['template'],$data['data'],function($m) use ($data){
				$m->subject($data['subject']);
				$m->from($data['from'],$data['user']);
				$m->to($data['to']);
		//		$m->format($data['format']);
				if($data['had_attached_files']){
					if($data['attached_files']<1){
						if(array_key_exists('attached_file_options', $data['attached_files'][0]))
							$m->attach($data['attached_files'][0]['name'],$data['attached_files'][0]['attached_file_options']);
						else
							$m->attach($data['attached_files'][0]['name']);
					}
					else
					{

					}
				}
			});
			Log::info('Email Send Ok');
			return true;
   		}
		else{

			Log::error('Falen arfuments');
			return false;
		}
	}


}