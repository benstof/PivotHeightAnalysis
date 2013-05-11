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

  if(isset($_POST['submit']))
  {
  	
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

  <!DOCTYPE html>
  <html>
  <head>

  	<title>Pivot Height Analysis by Senninger</title>



  	<script language="JavaScript" type="text/javascript" src="main.js"></script>   
  	<script language="JavaScript" src="scripts/gen_validatorv31.js" type="text/javascript"></script>	
  	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=AIzaSyCUYIZOucrwK5g72kEAlzLWB3ds5gls3jM&sensor=false"></script>
  	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
  	<script language="JavaScript" type="text/javascript" src="scripts/captcha.js"></script>   
  	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
  	<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
  	<link href='http://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>

  </head>


  <body onload="initialize()">




  	<div style="margin:auto; width: 880px; ">   <img src="senninger_logo.jpg" style="width:200px; margin-bottom:10px; float:left;" />  
  		<h1 class="header_title">Pivot Height Analysis</h1></div>

  		<div id="header_area" class="full_width">
  			<div class="page">


  				<div id="header">

<div id="light">

	<div class="close_share_btn" src="images/modal_close_bttn.png"> </div>

<div class="gform" style="margin-left:110px; margin-top:20px;">
<form id="send_form" style="color:#014e90;" onsubmit="return send_submit();">

  							<div class="box">

  								<h1>Share with Senninger</h1>

  							

  								<label id="">
  									<span>Name</span>

  									<input type="text" class="input_text" name="name" id="name"/>

  								</label>		

  								<label id="">
  									<span>Email</span>

  									<input type="text" class="input_text" name="email" id="email"/>

  								</label>	

  								<label id="" style="d">
  									<span>Pivot Notes</span>

  									 <textarea rows="3" cols="17" style="color:#167AB6; margin-left: 0px; margin-right: 5px; font-size:14px; padding:5px; width:162px; border:1px solid #ccc;" name="message" value=""></textarea>

  								</label>	

  								<label id="" style="padding:0px;">
  									<span></span>
									<img src="captcha_code_file.php?rand=<?php echo rand(); ?>" id='captchaimg' > 
  								</label>	

  								<label id="" style="padding:0px;font-size:16px;">
  									<span></span>
									Enter the code above here: <br/> 
									
  								</label>	

  								<label id="" style="padding:0px;">
  									<span></span>
									<input id="6_letters_code" class="input_text" name="6_letters_code" type="text" />
									
								</label>	

  								<label id="" style="font-size:11px;">
  									<span></span>
									<small>Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh</small>
									
								</label>	

      


  								<label style="text-align:right; width:90%;">
  									<span></span>
  									<input type="submit" class="send_submit" value=""   />
  								</label>	

  							</div>


		  <input type="hidden" name="hpoint" id="hpoint_val" value="0" />
          <input type="hidden" name="lpoint" id="lpoint_val" value="0"  />
          <input type="hidden" name="cpoint" id="cpoint_val" value="0"  />
          <input type="hidden" name="clocation" id="clocation" value="0"  />
          <input type="hidden" name="units_val" id="un_val" value=""  />
          <input type="hidden" name="radius_val" id="rad_val" value=""  />
          <input type="hidden"  id="ip_val" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>"  />

  						</form>

  					</div>




</div>
<div id="fade" onClick="lightbox_close();"></div> 


  					<div style="width:450px; height:450px; margin:50px;  float:left; border:5px #fff solid;">
  						<div id="map_canvas" style="border:1px solid #888; position: relative; background-color: rgb(229, 227, 223); overflow: hidden;"></div>
  					</div>


  					<div class="gform">


  						<form id="go_location" onsubmit="return form_submit();">

  							<div class="box">

  								<h1>Place your pivot</h1>

  								<label style="width:50%; float:left;">
  									<span>Address:</span>

  									<input type="radio" name="adtt" class="adt" id="address_rb"  onchange="updateAdt()" />

  								</label>

  								<label>
  									<span>Lat/Lng:</span>

  									<input type="radio" name="adtt" class="adt"  id="location_rb"  checked onchange="updateAdt()" />

  								</label>

  								<label id="lat_label">
  									<span>Lat *</span>

  									<input type="text" class="input_text" name="name" id="lat_address"/>

  								</label>		

  								<label id="lng_label">
  									<span>Lng *</span>

  									<input type="text" class="input_text" name="name" id="lng_address"/>

  								</label>	

  								<label id="address_label" style="display:none;">
  									<span>Address *</span>

  									<input type="text" class="input_text" name="name" id="address_text"/>

  								</label>	

  								<label  style="width:70%; float:left;">
  									<span>Radius *</span>

  									<input style="width:50px;" type="text" class="input_text" name="name" id="radius" value="<?php echo $radius; ?>"/>

  								</label>

  								<label>

  									<input type="submit" class="input_submit" value=""   />
  								</label>	

  								<label  style="width:50%; float:left;">
  									<span>Feet:</span>

  									<input name="units" id="feet" type="radio" onchange="updateUnits()" />

  								</label>

  								<label>
  									<span>Meters:</span>

  									<input name="units" id="meters" type="radio" checked onchange="updateUnits()" />

  								</label>




  							</div>
  						</form>


  						<div class="share_btn" src="images/share_btn.png"> </div>

  					</div>


  				</div>

  			</div>

  		</div>

  		<div style="width:96em; margin:auto;">

  			<div class="icon_box">  
  				<img style="width:130px;" src="images/highestc.png" /> 
  				<span class="icon_header">Highest Point:</span>
  				<br/>
  				<b class="icon_number" id="hpoint" style="color:#ce3234;" >0.0</b><span style="color:#ce3234;" class="units_lbl icon_number" style="margin-left:2px;"></span>
  			</div>

  			<div class="icon_box">  
  				<img style="width:130px;" src="images/center.png" /> 
  				<span class="icon_header">Center Point:</span>
  				<br/>
  				<b class="icon_number" id="cpoint">0.0</b><span class="units_lbl icon_number" style="margin-left:2px;"></span>
  			</div>

  			<div class="icon_box" >  
  				<img style="width:130px;" src="images/lowestc.png" /> 
  				<span class="icon_header">Lowest Point:</span>
  				<br/>
  				<b class="icon_number" style="color:#4db849;"  id="lpoint">0.0</b><span style="color:#4db849;" class="units_lbl icon_number" style="margin-left:2px;"></span>
  			</div>

  			<div class="icon_box">  
  				<img style="width:130px;" src="images/calc.png" /> 
  				<span class="icon_header">Coordinates:</span>

  				<b class="icon_number" id="coords">(23.9898, 34.0998</b>
  			</div>




  		</div>


  	</body>
  	</html>
