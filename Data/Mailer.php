<?php
$subject = $_GET["subject"];
$from = $_GET["from"];
$to = $_GET["to"];
$name = $_GET["name"];
$phoneNumber = $_GET["phoneNumber"];
class Mail {
	public function Generate($from, $to, $subject, $msg) {
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";
		$headers .= "From: <$from>";
		return mail($to, $subject, $msg, $headers);
	}
}

# Action
$msg = "";
switch($subject) {
    case "New Proposal":
        $msg = "<h3>You have received a new proposal!</h3>";
        $msg .= "<p>Name: $name</p>";
		$msg .= "<p>Email: $from</p>";
		$msg .= "<p>Phone Number: $phoneNumber</p>";
        break;
}
$MAIL = new Mail();
$MAIL->Generate($from, $to, $subject, $msg);
header("Location: ../../approved-submission.html?requestedInfo=true.html?requestedInfo=true");
?>