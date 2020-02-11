<?php
//If the form is submitted
if(isset($_POST['submit'])) {

	//Check to make sure that the name field is not empty
	if(trim($_POST['contactname']) == '') {
		$hasError = true;
	} else {
		$name = trim($_POST['contactname']);
	}



	//Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) == '')  {
		$hasError = true;
	} else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email']))) {
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}

	//Check to make sure comments were entered
	if(trim($_POST['message']) == '') {
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['message']));
		} else {
			$comments = trim($_POST['message']);
		}
	}

	//If there is no error, send the email
	if(!isset($hasError)) {
		$emailTo = 'trishbellardine@yahoo.com'; //Put your own email address here
		$body = "Name: $name \n\nEmail: $email \n\nSubject: $subject \n\nComments:\n $comments";
		$headers = 'From: My Site <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

		mail($emailTo, $subject, $body, $headers);
		$emailSent = true;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TrishBellardine</title>
<link href="css/global.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen" />

<script type="text/javascript">
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

</script>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects,builder"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
</head>

<body>

<div class="container">
  
  <div class="sidebar1">
  <div id="logo"><a href="index.php"><img src="images/trish_logo.jpg" alt="Trish - Interactive Designer" width="179" height="113" /></a></div>
    <ul class="nav">
     <li class="resume"><a href="index.php">Web</a></li>
      <li class="resume"><a href="graphics.php">Graphics</a></li>
      <li class="resume"><a href="interactive.php">Video/Audio</a></li>
   
     
    </ul>
  
    <!-- end .sidebar1 --></div>
  <div class="content">
     <div id="inside_content" class="pinkline">  
     <div class="header"><a href="resume.php">Resume</a> |	<span class="toppink">Contact</span></div>
      <div id="display"> 
        <p>CONTACT TRISH BELLARDINE</p>
       
			<?php if(isset($hasError)) { //If errors are found ?>
		
        <p class="error">Please fill all the fields with valid information.</p>
			<?php } ?>

			<?php if(isset($emailSent) && $emailSent == true) { //If email is sent ?>
				<p><strong>Email Successfully Sent!</strong></p>
				<p>Thank you <strong><?php echo $name;?></strong> ! Your email was successfully sent.</p>
			<?php } ?>

		  <form method="post" action="<?php echo $_SERVER['website/PHP_SELF']; ?>" id="contactform">
			
	  		  <p>
	  		    <label for="name"><strong>Name:</strong></label><br/>
  		   
  		      <input type="text" size="50" name="contactname" id="contactname" value="" class="required" />
  		    </p>
	  		  <p>
	  		    <label for="email"><strong>Email:</strong></label><br/>
  		    
	  		    <input type="text" size="50" name="email" id="email" value="" class="required email" />
  		    </p>
	  		  
	  		  <p>
	  		    <label for="message"><strong>Message:</strong></label><br/>
  		    
	  		    <textarea rows="9" cols="50" name="message" id="message" class="required"></textarea>
  		    </p>
	  		  <p>
	  		    <input name="submit" type="submit" class="button" value="Send Message" />
  		    </p>
          </form>
          <p>
          
          </p>
      </div>
    </div>
    <h4>&nbsp;</h4>
    <!-- end .content --></div>
  <div class="footer">
    <p>Copyright © 2014. trishid.com All Rights Reserved </p>
    <!-- end .footer --></div>
  <!-- end .container --></div>
</body>
</html>