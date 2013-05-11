<?php
  session_start();
  $your_email ='benstof@gmail.com, ben@irrimaker.com, mfletcher@senninger.biz';// <<=== update to your email address
  $radius = isset($_GET['radius'])?$_GET['radius']:100;
  $units = isset($_GET['units'])?$_GET['units']:'meters';

  $errors = '';
  $name = '';
  $visitor_email = '';
  $user_message = '';
  $pass = false;


  	$name = $_POST['name'];
  	$visitor_email = $_POST['email'];
    
  	$user_message = $_POST['message'];
  	$hpoint = $_POST['hpoint'];
  	$lpoint = $_POST['lpoint'];
  	$cpoint = $_POST['cpoint'];
  	$clocation = $_POST['clocation'];
  	$units = $_POST['units_val'];
  	$radius = $_POST['radius_val'];


  	///------------Do Validations-------------
  	if(empty($name)||empty($visitor_email))
  	{
  		$errors .= "\n Name and Email are required fields. ";	
  	}
  	if(IsInjected($visitor_email))
  	{
  		$errors .= "\n Bad email value!";
  	}
  	if(empty($_SESSION['6_letters_code'] ) ||
     strcasecmp($_SESSION['6_letters_code'], $_POST['6_letters_code']) != 0)
  	{
  	//Note: the captcha code is compared case insensitively.
  	//if you want case sensitive match, update the check above to
  	// strcmp()
  		$errors .= "\n The captcha code does not match!";
  	}

    echo $errors;
  	
  	if(empty($errors))
  	{
  		//send the email
  		$to = $your_email;
  		$subject="New Pivot Height Analysis submission";
  		$from = $your_email;
  		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
  		
  		$body = "A user submitted a Pivot Height Analysis:\n".
  		"Name: $name\n".
  		"Email: $visitor_email \n".
  		"Message: \n".
  		"$user_message\n\n".
  		"Units: ".
  		"$units\n".
  		"Radius: ".
  		"$radius\n".
  		"Heighest Point Elevation: ".
  		"$hpoint\n".
  		"Lowest Point Elevation:  ".
  		"$lpoint\n".
  		"Center Point Elevation: ".
  		"$cpoint\n".
  		"Pivot Location: ".
  		"$clocation\n".
  		
  		"IP: $ip\n";	
  		
  		$headers = "From: $from \r\n";
  		$headers .= "Reply-To: $visitor_email \r\n";
  		
  		mail($to, $subject, $body,$headers);
  		
  		//header('Location: thank-you.html');
  		$pass = true;
  		
  	}


    // Function to validate against any email injection attempts
  function IsInjected($str)
  {
    $injections = array('(\n+)',
      '(\r+)',
      '(\t+)',
      '(%0A+)',
      '(%0D+)',
      '(%08+)',
      '(%09+)'
      );
    $inject = join('|', $injections);
    $inject = "/$inject/i";
    if(preg_match($inject,$str))
    {
      return true;
    }
    else
    {
      return false;
    }
  }



  ?>