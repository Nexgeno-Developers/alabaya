<?php

Class Notify_Email
{


    protected $contents;
    protected $values = array();

    public static function _init()
    {
        global $config;
        $sysEmail = 'accounts@makent.in';
        $sysCompany = $config['CompanyName'];
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        //check for smtp
//        $e = ORM::for_table('sys_emailconfig')->find_one('1');
//        if($e['method'] == 'smtp'){
//            $mail->IsSMTP();
//            $mail->Host = $e['host'];
//            $mail->SMTPAuth = true;
//            $mail->Username = $e['username'];
//            $mail->Password = $e['password'];
//            $mail->SMTPSecure = $e['secure'];
//            $mail->Port = $e['port'];
//        }
        $mail->SetFrom($sysEmail, $sysCompany);
        $mail->AddReplyTo($sysEmail, $sysCompany);
				// var_dump($config);exit;
        return $mail;
    }


    public static function _log($userid, $email, $subject, $message, $iid='0')
    {
        $date = date('Y-m-d H:i:s');
        $d = ORM::for_table('sys_email_logs')->create();
        $d->userid = $userid;
        $d->sender = '';
        $d->email = $email;
        $d->subject = $subject;
        $d->message = $message;
        $d->date = $date;
        $d->iid = $iid;
        $d->save();
        $id = $d->id();
        return $id;

    }
    public static function _logproforma($userid, $email, $subject, $message, $iid='0')
    {
        $date = date('Y-m-d H:i:s');
        $d = ORM::for_table('sys_proforma_email_logs')->create();
        $d->userid = $userid;
        $d->sender = '';
        $d->email = $email;
        $d->subject = $subject;
        $d->message = $message;
        $d->date = $date;
        $d->iid = $iid;
        $d->save();
        $id = $d->id();
        return $id;

    }

  public static function _send_multi($name,$to,$subject,$message,$uid='0',$iid='0',$cc='',$bcc='',$attachment_path=array(),$attachment_file=array()){

        global $_app_stage;

        self::_log($uid,$to,$subject,$message,$iid);

        if($_app_stage == 'Demo'){

            return true;

        }

        $e = ORM::for_table('sys_emailconfig')->find_one(1);

        $method = $e->method;

        if(($method == 'smtp') || ($method == 'phpmail')){

            $mail = self::_init();


            if($method == 'smtp'){
                $mail->IsSMTP();
                $mail->Host = $e['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $e['username'];
                $mail->Password = $e['password'];
                $mail->SMTPSecure = $e['secure'];
                $mail->Port = $e['port'];
            }

            $mail->AddAddress($to, $name);

            if($cc != ''){
                $mail->AddAddress($cc);
            }

            if($bcc != ''){
                $mail->AddBCC($bcc);
            }

            if($attachment_path != ''){
                $mail->AddAttachment($attachment_path, $attachment_file,  'base64', 'application/pdf');
            }

            // check for attachment



            $mail->Subject = $subject;
            $mail->MsgHTML($message);

                $mail->Send();

        }
        else{

            global $config;
            $sysEmail = $config['sysEmail'];
            $sysCompany = $config['CompanyName'];

            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

// Additional headers

            $headers .= 'From: '.$sysCompany.' <'.$sysEmail.'>' . "\r\n";

            if($cc == ''){

              //  $headers .= 'To: '.$name.' <'.$to.'>' . "\r\n";

            }
            else{
                $cc_parts = explode('@',$cc);
                $cc_username = $cc_parts[0];
                $headers .= 'To: '.$cc_username.' <'.$cc.'>' . "\r\n";
            }

            if($bcc != ''){

                $headers .= 'Bcc: '.$bcc.'' . "\r\n";

            }


            mail($to, $subject, $message, $headers);


        }

    }
		
    public static function _send($name,$to,$subject,$message,$uid='0',$iid='0',$cc='',$bcc='',$attachment_path=array(),$attachment_file=array()){
			global $_app_stage;
			self::_logproforma($uid,$to,$subject,$message,$iid);

			if($_app_stage == 'Demo'){

					return true;

			}

			$e = ORM::for_table('sys_emailconfig')->find_one(1);

			$method = $e->method;

			if(($method == 'smtp') || ($method == 'phpmail')){

				$mail = self::_init();

				if($method == 'smtp'){
					$mail->IsSMTP();
					$mail->Host = $e['host'];
					$mail->SMTPAuth = true;
					$mail->Username = $e['username'];
					$mail->Password = $e['password'];
					$mail->SMTPSecure = $e['secure'];
					$mail->Port = $e['port'];
				}

				$mail->AddAddress($to, $name);
				$mail->AddAddress('arif@makent.in', 'Arif');

				if($cc != ''){
					$mail->AddAddress($cc);
				}

				if($bcc != ''){
					$mail->AddBCC($bcc);
				}

				if($attachment_path != ''){
					$mail->AddAttachment($attachment_path, $attachment_file,  'base64', 'application/pdf');
				}
				
				if(!empty($files)){ 
					for($i=0;$i<count($files);$i++){ 
						if(is_file($files[$i])){ 
							$file_name = basename($files[$i]); 
							$file_size = filesize($files[$i]); 
							 
							$message .= "--{$mime_boundary}\n"; 
							$fp =    @fopen($files[$i], "rb"); 
							$data =  @fread($fp, $file_size); 
							@fclose($fp); 
							$data = chunk_split(base64_encode($data)); 
							$message .= "Content-Type: application/octet-stream; name=\"".$file_name."\"\n" .  
							"Content-Description: ".$file_name."\n" . 
							"Content-Disposition: attachment;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .  
							"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
						} 
					} 
				}

				$mail->Subject = $subject;
				$mail->MsgHTML($message);
				$mail->Send();

			}else{

				global $config;
				$sysEmail = $config['sysEmail'];
				$sysCompany = $config['CompanyName'];

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

				$headers .= 'From: '.$sysCompany.' <'.$sysEmail.'>' . "\r\n";

				if($cc == ''){

					//  $headers .= 'To: '.$name.' <'.$to.'>' . "\r\n";

				}
				else{
					$cc_parts = explode('@',$cc);
					$cc_username = $cc_parts[0];
					$headers .= 'To: '.$cc_username.' <'.$cc.'>' . "\r\n";
				}

				if($bcc != ''){

					$headers .= 'Bcc: '.$bcc.'' . "\r\n";

				}

				mail($to, $subject, $message, $headers);

			}

    }

    public static function _test()
    {
        $mail = self::_init();
        $email = 'sadia@ibilling.io';
        $mail_subject = 'Test Email';
        $name = 'Sadia';
        $body = 'Hello this is test email body';
        $mail->AddAddress($email, $name);
        $mail->Subject = $mail_subject;
        $mail->MsgHTML($body);
        $mail->Send();

    }

    public static function _otp($otp,$name,$email)
    {
        $mail = self::_init();
        global $config;

        $sysCompany = $config['CompanyName'];
        $mail_subject = $sysCompany . ' OTP (One Time Password)';

        $body = 'Your '.$sysCompany.' password has been verified and OTP is required to proceed further. Your current session OTP is '.$otp.' and only valid for this session. If you didn\'t login, please contact us immediately.';
        $mail->AddAddress($email, $name);
        $mail->Subject = $mail_subject;
        $mail->MsgHTML($body);
        $mail->Send();

    }




   

   }
	 