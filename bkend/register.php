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

// Get data
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'];
$email = $data['email'];
$og_pass = $data['password'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);


// Check if user email already exists
$stmt = $connection->prepare("Select * FROM users WHERE email='$email'");
$stmt->execute();
$row = $stmt->fetch();

if($row){
	echo json_encode([
  		'success' => false,
  		'message' => 'Email already exists'
	]);
} else {
	try {
		// Insert to database
		$now = date('Y-m-d H:i:s');
		$sql = "INSERT INTO users(name, email, password, date_created, date_updated)
			VALUES ('$name', '$email', '$password', '$now', '$now')
		";
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

		    $mail->addAddress($email);     //Add a recipient - From inputted email address

		    //Content
		    $mail->isHTML(true);                                  //Set email format to HTML
		    $mail->Subject = 'Registration Successfull at ' . date('F d,Y h:i:s A');
		    $mail->Body    = '<strong>We have successfully received your registration. Password is:  '. $og_pass . '</strong>';
		    $mail->AltBody = 'We have successfully received your registration. Password is: ' . $og_pass;

		    $mail->send();
		} catch (\Exception $e) {}

		echo json_encode([
	  		'success' => true,
	  		'message' => '<strong>' . $email . '</strong> successfully added. Please check your email for confirmation registration.'
		]);
	} catch (Exception $e) {		
		echo json_encode([
	  		'success' => false,
	  		'message' => $e->getMessage()
		]);
	}	
}


