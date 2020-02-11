<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>contact</title>
<link href="css/global.css" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="container">
  
  <div class="sidebar1">
  <div id="logo"><a href="home.html"><img src="images/trish_logo.jpg" alt="Trish - Interactive Designer" width="179" height="113" /></a></div>
    <ul class="nav">
      <li class="resume"><a href="home.html">Web</a></li>
      <li class="resume"><a href="graphics.html">Graphics</a></li>
      <li class="resume"><a href="interactive.html">Interactive</a></li>
   
     
    </ul>
  
    <!-- end .sidebar1 --></div>
  <div class="content">
    <div id="inside_content" class="pinkline">  
     <div class="header"><a href="resume.html">Resume</a> |	<span class="toppink">Contact</span></div>
      <div id="display">
        <p>CONTACT TRISH BELLARDINE.......</p>
        <form action="" method="post" name="form1" id="form1">
    <p>
        <label for="name">Name:</label>
        <input name="name" type="text" class="textInput" id="name" />
    </p>
    <p>
        <label for="email">Email:</label>
        <input name="email" type="text" class="textInput" id="email" />
    </p>
    <p>
        <label for="comments">Comments:</label>
        <textarea name="comments" id="comments" cols="45" rows="5"></textarea>
    </p>
    <p>
        <input type="submit" name="send" id="send" value="Send comments" />
    </p>
</form>
<pre>
<?php if ($_POST) {print_r($_POST);} ?>
</pre>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
      </div>
    </div>
    <h4>&nbsp;</h4>
    <!-- end .content --></div>
  <div class="footer">
    <p>Copyright © 2013. trishid.com All Rights Reserved </p>
    <!-- end .footer --></div>
  <!-- end .container --></div>
</body>
</html>