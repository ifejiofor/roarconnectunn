<?php
error_reporting(1);
require_once "Mail.php";
$from= "roarconnect@roarconnectunn.com";
$to=$email;
$subject="FOOD ORDER";
$body= "We Received your food order,
          our delivery agents will contact you shortly." ;

$host="server176.web-hosting.com";
$smtp="465";
$username="roarconnect@roarconnectunn.com";
$password="hero75100";
$headers=array('From'=>$from,
			   'To'=>$to,
			   'Subject'=>$subject);
$smtp=Mail::factory('smtp',
		array('host'=>$host,
		      'auth'=>true,
			  'username'=>$username,
			  'password'=>$password));
$mail=$smtp->send($to, $headers, $body);
		  
		if(PEAR::isError($mail)){
		echo("<p>".$mail->getMessage(). "</p>");
		}else {
			echo 'Message successfully Sent!';
				}

?>