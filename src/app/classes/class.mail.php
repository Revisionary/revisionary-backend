<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mail {


	// SETTERS:
	public function __construct() {

    }



	// SENDER
    public static function SEND(
    	string $to,
    	string $subject,
    	string $message,
    	string $from_name = "Revisionary"
    ) {
	    global $config;


	    // Check for the multiple emails
	    $recipients = array_unique(array_filter(array_map('trim',explode(",",$to))));


		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = 'base64';

		try {

		    //Server settings
		    //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    $mail->Host = 'mail.bilaltas.net';  // Specify main and backup SMTP servers
		    $mail->SMTPAuth = true;                               // Enable SMTP authentication
		    $mail->Username = $config['env']['smtp_user'];                 // SMTP username
		    $mail->Password = $config['env']['smtp_pass'];                           // SMTP password
		    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
		    $mail->Port = 465;                                    // TCP port to connect to

		    //Recipients
		    $mail->setFrom('notify@revisionaryapp.com', $from_name);

		    foreach ($recipients as $recipient) {

			    //$mail->addAddress($recipient, $to_name);     // Add a recipient
			    $mail->addAddress($recipient);               // Name is optional

		    }
		    $mail->addReplyTo('noReply@revisionaryapp.com', 'Do Not Reply');
		    //$mail->addCC('cc@example.com');
		    //$mail->addBCC('bcc@example.com');

		    //Attachments
		    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

		    //Content
		    $mail->isHTML(true);                                  // Set email format to HTML
		    $mail->Subject = $subject;
		    $mail->Body    = $message;
		    $mail->AltBody = $message;

		    $mail->send();

			return array(
				'status' => 'sent',
				'message' => ''
			);

		} catch (Exception $e) {

		    return array(
				'status' => 'failed',
				'message' => 'Message could not be sent. Mailer Error: '. $mail->ErrorInfo
			);

		}


    }


	// ASYNC SENDER
    public static function ASYNCSEND(
    	string $to,
    	string $subject,
    	string $message
    ) {


		// Initiate Email Sender
		$process = new Cocur\BackgroundProcess\BackgroundProcess(
			"php ".dir."/app/bgprocess/mail.php $to $subject $message ".session_id()
		);
		$process->run(logdir."/sent-mail-php.log", true);
		$process_ID = $process->getPid();


		return $process_ID;
    }


}