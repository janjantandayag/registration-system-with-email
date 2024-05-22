<?php 

// Connect database
include('connection.php');

// Include PHPMailer
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


$connection = connectToDB();

// Get email aaddress
$data = json_decode(file_get_contents('php://input'), true);
$email_address = $data['email'];


// Check email address if exists in the database
$stmt = $connection->prepare("Select * FROM users WHERE email='$email_address'");
$stmt->execute();
$row = $stmt->fetch();

if($row){	
	// Send password reset link.
	// Generate and store reset token
	$token = bin2hex(random_bytes(16));
	$reset_link = 'http://localhost/registration/update_password.php?token=' . $token;
	$message = 'Click the link below to reset your password: <br/> <a href="' . $reset_link . '">CLICK HERE</a>';

	// Update reset link
	$sql = "UPDATE users SET token='$token' WHERE email='$email_address'";
	$connection->exec($sql);

	// Send email 
	$mail = new PHPMailer(true);

	try {
		$mail->isSMTP();                                            //Send using SMTP
		$mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
		$mail->SMTPAuth   = true;                                   //Enable SMTP authentication			
	    $mail->Username   = 'janjantandayag123@gmail.com';                     //SMTP username
	    $mail->Password   = 'poorgxojdjwbrudg';                               //SMTP password
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
		$mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

	    $mail->addAddress($email_address);     //Add a recipient - From inputted email address

	    //Content
	    $mail->isHTML(true);                                  //Set email format to HTML
	    $mail->Subject = 'Password Reset';
	    $mail->Body    = $message;
	    $mail->AltBody = $message;

	    $mail->send();
	} catch (\Exception $e) {}
	echo json_encode([
  		'success' => true,
  		'message' => 'Password reset link sent to your email.'
	]);
} else {
	echo json_encode([
  		'success' => false,
  		'message' => 'Email address does not exists in the system.'
	]);
}