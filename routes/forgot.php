<?php 


/******************* ACTIVATION BY FORM**************************/
if (p('doReset')=='Reset')
{
  $err = array();
  $msg = array();
  
  if(!isEmail(p('user_email'))) {
    $err[] = "ERROR - Please enter a valid email"; 
  }
  
  $user = event('find_login', null, p('user_email'));
  
  if ( !$user ) { 
    $err[] = "Error - Sorry no such account exists or registered.";
  } else {
    $activ_code = rand(1000,9999);
    $user->activation_code = $activ_code;
    $user->save();
    
    $host  = $_SERVER['HTTP_HOST'];
    $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $reset_link = create_secure_callback_url('reset_user', $user->id);
    $reset_link = "http://{$host}{$path}{$reset_link}";

    list($subject,$body) = template('account.forgot', array('reset_link'=>$reset_link));
    swiftmail($user->email, $subject, $body);
    

    flash_next("Your password reset link has been sent. Please check your email (and bulk mail).");
    
    header("Location: /");  
    exit();
  }
}
?>
  <script>
  $(document).ready(function(){
    $("#actForm").validate();
  });
  </script>
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="main">
  <tr> 
    <td colspan="3">&nbsp;</td>
  </tr>
  <tr> 
    <td width="160" valign="top"><p>&nbsp;</p>
      <p>&nbsp; </p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p></td>
    <td width="732" valign="top">
<h3 class="titlehdr">Forgot Password</h3>

      <p> 
        <?php
	  /******************** ERROR MESSAGES*************************************************
	  This code is to show error messages 
	  **************************************************************************/
	if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "* $e <br>";
	    }
	  echo "</div>";	
	   }
	   if(!empty($msg))  {
	    echo "<div class=\"msg\">" . $msg[0] . "</div>";

	   }
	  /******************************* END ********************************/	  
	  ?>
      </p>
      <p>Give us the email address you used when you signed up and we'll send you a password reset link.</p>
	 
      <form action="forgot" method="post" name="actForm" id="actForm" >
        <table width="65%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="36%">Your Email</td>
            <td width="64%"><input name="user_email" type="text" class="required email" id="txtboxn" size="25"></td>
          </tr>
          <tr> 
            <td colspan="2"> <div align="center"> 
                <p> 
                  <input name="doReset" type="submit" id="doLogin3" value="Reset">
                </p>
              </div></td>
          </tr>
        </table>
        <div align="center"></div>
        <p align="center">&nbsp; </p>
      </form>
	  
      <p>&nbsp;</p>
	   
      <p align="left">&nbsp; </p></td>
    <td width="196" valign="top">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
