  function refreshCaptcha()
  {
  	var img = document.images['captchaimg'];
  	img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
  }