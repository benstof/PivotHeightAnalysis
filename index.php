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

   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>
   <script language="JavaScript" type="text/javascript" src="main.js"></script>    
   <script language="JavaScript" src="scripts/gen_validatorv31.js" type="text/javascript"></script>	
   <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&key=AIzaSyCUYIZOucrwK5g72kEAlzLWB3ds5gls3jM&sensor=false"></script>
   <script language="JavaScript" type="text/javascript" src="jquery.cookie.js"></script>

   <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
   <style type="text/css">
   html { height: 100% }
   body { height: 100%; margin: 0; padding: 0 }
   #map_canvas { height: 100% }
   </style>


</head>

<body onload="initialize()" style="background: #00ff00 url('grid.png') repeat;">


<div id="main" style="width:100%; float:left;">

       
        <div id="disclaimer_box" style="padding:100px; z-index:1; position:absolute; top:70px; opacity:.9;  left:200px; 
        width: 700px; height:1000px; background-color:#888888; display:none;">

                <h2>This link distributed through Senninger irrigation is to be used as a tool to help with collecting elevation 
                data through Google Earth. Senninger Irrigation is not responsible or is liable for any data collected 
                from this site and bears no responsibility.
                </h2>
                <h2>
                  OTHER THAN AS EXPRESSLY SET OUT IN THESE TERMS OR ADDITIONAL TERMS, NEITHER GOOGLE NOR 
                  ITS SUPPLIERS OR DISTRIBUTORS MAKE ANY SPECIFIC PROMISES ABOUT THE SERVICES. FOR EXAMPLE, 
                  WE DON'T MAKE ANY COMMITMENTS ABOUT THE CONTENT WITHIN THE SERVICES, THE SPECIFIC 
                  FUNCTION OF THE SERVICES, OR THEIR RELIABILITY, AVAILABILITY, OR ABILITY TO MEET YOUR NEEDS. 
                  WE PROVIDE THE SERVICES 'AS IS'.
                  For accurate elevation data a physical survey must be done to verify the elevation differences.
                </h2>

                <div id="aggree_btn" style="background-color:#555; text-align:center; border:1px solid #000; margin:auto auto; "> <b style="font-size:20px; color:#fff; margin:auto auto;" >Agree</b></div>
        </div>



        <div id="wrapper" style="width:900px; margin: auto;">


                <div style="margin:5px; text-align:center; font-size:22px; margin-top:5px; ">
                        <h2 style="margin-bottom:10px; margin-top:20px; ">Pivot Height Analysis</h2>
                </div>

                <!-- Left Col Div -->
                <div style="width:500px; height:732px; margin:5px; text-align:center; font-size:22px; margin-top:10px; float:left;">
                        <div id="map_canvas" style="border:1px solid #888; position: relative; background-color: rgb(229, 227, 223); overflow: hidden;"></div>
                </div>
                <!-- Left Col Div -->




                <!-- Right Col Div -->
                <div style="width:350px; text-align:center;  border:0px solid #000; 
                padding:5px; margin:0px; float:left; font-family:arial; font-size:18px; " >

                        <!-- Address Div -->
                        <div style="width:350px; border:1px solid #888; background-color:#eee; padding:5px; margin:5px; float:left;  " >

                            Address:
                            <input type="radio" name="adt" id="address_rb" onchange="updateAdt()" >
                            Lat/Lng: 
                            <input type="radio" name="adt" id="location_rb"  checked onchange="updateAdt()">
                           <!-- Current: 
                            <input type="radio" name="adt" id="current_rb" onchange="updateAdt()"> -->

                            <input disabled style="margin-left: 5px; width:300px; margin-right: 5px;  font-size:14px; 
                            padding:5px;" id="address" type="textbox" value=""><br/><span style="font-size:10px;">eg. (112 Ainslie St, 11211) or (51 57' 32.48",00 55' 55.23")
                            <br/>
                            <span style="display:nokne; font-size:14px;" >
                                Lat:<input id="lat_address"  style=" margin-left: 5px; width:150px; margin-right: 5px;  font-size:14px; 
                                    padding:5px;"  type="textbox" value="28.542180934818862" ></span><br/>
                                    <span style="display:nokne;font-size:14px;" >
                                Lng:<input id="lng_address"  style="margin-left: 5px; width:150px; margin-right: 5px;  font-size:14px; 
                                            padding:5px;"  type="textbox" value="-81.6891067430725" ></span><br/>

                                            <input type="button" onclick="codeAddress()" style="margin-bottom:10px;" value="Go"></div>

                                            <div style="margin:5px;" > Radius: <br/>
                                                    <input style="margin-left: 10px; width:60px; margin-right: 5px;  font-size:14px; padding:5px;" 
                                                    id="radius" onchange="updatePivot()" type="textbox" value="<?php echo $radius; ?>">
                                                    <br/>
                                                    <input type="button" onclick="updatePivot()" style="margin-bottom:10px;"  value="Update Radius"></div>
                                                    <div style="margin:5px;" > 
                                                            Meters: 
                                                            <input type="radio" name="units" id="meters" <?php if($units == 'meters') echo 'checked'; ?> onchange="updateUnits()">
                                                            Feet: 
                                                            <input type="radio" name="units" id="feet" <?php if($units == 'feet') echo 'checked'; ?> onchange="updateUnits()">
                                                    </div>
                                            </div>

                                            <div style="width:350px; background:#fff; border:1px dotted #888; padding:5px; margin:5px;  padding-bottom:15px; float:left; text-align:center; " >
                                                  <img src="senninger_logo.jpg" style="width:200px; margin-bottom:10px;" />
                                                  <div >Highest point: <b style="color:red;" id="hpoint"></b><span class="units_lbl" style="font-size:12px; margin-left:2px;"></span></div>
                                                  <div >Center point: <b style="color:blue;" id="cpoint"></b><span class="units_lbl" style="font-size:12px; margin-left:2px;"></span></div>
                                                  <div >Lowest point: <b style="color:green;" id="lpoint"></b><span class="units_lbl" style="font-size:12px; margin-left:2px;"></span></div>
                                                  <div >Coordinates: <b style="color:green;" id="coords"></b></div>
                                            </div>
                        <!-- Address Div -->
                        </div>




                                      <!-- Share Div -->
                                      <div style="width:350px; text-align:center; border:1px solid #888; background-color:#eee; padding:5px; margin:5px; float:left; " >

                                        <b>Share with Senninger</b>

                                        <?php if(!empty($errors)){ echo "<p class='err'>".nl2br($errors)."</p>"; }?>

                                        <div id='contact_form_errorloc' class='err'></div>

                                        <form method="POST" name="contact_form" 
                                            action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>"> 

                                        <?php if(!$pass) { ?>

                                            <div style="margin:5px;" > Name:<input style="margin-left: 5px; margin-right: 5px;  font-size:14px; padding:5px;" type="textbox" name="name" value='<?php echo htmlentities($name) ?>'></div>
                                            <div style="margin:5px;" > Email: <input style="margin-left: 3px; margin-right: 5px;  font-size:14px; padding:5px;"  type="textbox" name="email" value='<?php echo htmlentities($visitor_email) ?>'></div>
                                            <div style="margin:5px;" > Pivot Notes: <textarea rows="3" cols="37" style="margin-left: 0px; margin-right: 5px;  font-size:14px; padding:5px;" name="message" value=""><?php echo htmlentities($user_message) ?></textarea></div>

                                            <p>
                                              <img src="captcha_code_file.php?rand=<?php echo rand(); ?>" id='captchaimg' ><br>
                                              <label for='message'>Enter the code above here :</label><br>
                                              <input id="6_letters_code" name="6_letters_code" type="text"><br>
                                              <small>Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh</small>
                                            </p>
                                            <input type="submit" value="Submit" name='submit'>

                                            <input type="hidden" name="hpoint" id="hpoint_val" value="0" />
                                            <input type="hidden" name="lpoint" id="lpoint_val" value="0"  />
                                            <input type="hidden" name="cpoint" id="cpoint_val" value="0"  />
                                            <input type="hidden" name="clocation" id="clocation" value="0"  />
                                            <input type="hidden" name="units_val" id="un_val" value="<?php echo $units; ?>"  />
                                            <input type="hidden" name="radius_val" id="rad_val" value="<?php echo $radius; ?>"  />
                                            <input type="hidden"  id="ip_val" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>"  />


                                        </form>


                                        <? } else { ?>

                                            <input type="hidden" name="hpoint" id="hpoint_val" value="0" />
                                            <input type="hidden" name="lpoint" id="lpoint_val" value="0"  />
                                            <input type="hidden" name="cpoint" id="cpoint_val" value="0"  />
                                            <input type="hidden" name="units_val" id="un_val" value="<?php echo $units; ?>"  />
                                            <input type="hidden" name="radius_val" id="rad_val" value="<?php echo $radius; ?>"  />
                                            <input type="hidden" name="radius_val" id="rad_val" value="100"  />

                                        </form>

                                          <p><b>Thanks for your pivot height analysis submission.</b></p>
                                          <p><b>Someone will be in touch with you shortly.</b></p>

                                        <? } ?>

                                      </div>
                                      <!-- Share Div -->


                  <script language="JavaScript">
                  // Code for validating the form
                  // Visit http://www.javascript-coder.com/html-form/javascript-form-validation.phtml
                  // for details
                  var frmvalidator  = new Validator("contact_form");
                  //remove the following two lines if you like error message box popups
                  frmvalidator.EnableOnPageErrorDisplaySingleBox();
                  frmvalidator.EnableMsgsTogether();

                  frmvalidator.addValidation("name","req","Please provide your name"); 
                  frmvalidator.addValidation("email","req","Please provide your email"); 
                  frmvalidator.addValidation("email","email","Please enter a valid email address"); 
                  </script>
                  <script language='JavaScript' type='text/javascript'>
                  function refreshCaptcha()
                  {
                        var img = document.images['captchaimg'];
                        img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
                  }
                  </script>

                <!-- Right Col Div -->
                </div>












        </div> <!-- wrapper -->
</div> <!-- main -->

</body>
</html>