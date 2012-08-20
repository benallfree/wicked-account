<?php 


require(dirname(__FILE__)."/../lib/util.php");
$err = array();

foreach($_GET as $key => $value) {
	$get[$key] = W::user_filter($value); //get variables are filtered.
}

if (W::p('doLogin')=='Login')
{

  foreach($_POST as $key => $value) {
    $data[$key] = W::user_filter($value); // post variables are filtered
  }
  
  
  $user_email = $data['usr_email'];
  $pass = $data['pwd'];
  
  $user = W::filter('find_login', null, $user_email);

  // Match row found with more than 1 results  - the user is authenticated. 
  if ( $user ) 
  { 
  	if(!$user->is_activated) 
  	{
  	 $s = "Account not activated. Please check your email for activation code. ";
  	 $s .= "<a href='{$user->activation_check_url}'>Resend</a>";
    	$err[] = $s;
    } else {
      if ($user->is_password($pass))
      { 
        W::user_login($user, W::p('r',$config['after_login_url']));
      } else {
      	$err[] = "Your password is incorrect. Please try again or use Forgot Password.";
      }
    } 
  } else {
    $err[] = "Error - Invalid login. No such user exists";
  }		
}
					 
					 

?>
  <script>
  $(document).ready(function(){
    $("#logForm").validate();
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
    <td width="732" valign="top"><p>&nbsp;</p>
      <h3 class="titlehdr">Login Users 
      </h3>  
	  <p>
	  <?php
	  /******************** ERROR MESSAGES*************************************************
	  This code is to show error messages 
	  **************************************************************************/
	  if(!empty($err))  {
	   echo "<div class=\"msg\">";
	  foreach ($err as $e) {
	    echo "$e <br>";
	    }
	  echo "</div>";	
	   }
	  /******************************* END ********************************/	  
	  ?></p>
      <form method="post" name="logForm" id="logForm" >
        <table width="65%" border="0" cellpadding="4" cellspacing="4" class="loginform">
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td width="28%">Username / Email</td>
            <td width="72%"><input name="usr_email" type="text" class="required" id="txtbox" size="25"></td>
          </tr>
          <tr> 
            <td>Password</td>
            <td><input name="pwd" type="password" class="required password" id="txtbox" size="25"></td>
          </tr>
          <tr> 
            <td colspan="2"><div align="center">
                <input name="remember" type="checkbox" id="remember" value="1">
                Remember me</div></td>
          </tr>
          <tr> 
            <td colspan="2"> <div align="center"> 
                <p> 
                  <input name="doLogin" type="submit" id="doLogin3" value="Login">
                </p>
                <p>
                  <? if($config['should_allow_open_registration']): ?>
                  <a href="register">Register Free</a><font color="#FF6600"> | </font> 
                  <? endif; ?>
                  <a href="reset">Reset Password</a> <font color="#FF6600"> 
                  </font></p>
              </div></td>
          </tr>
        </table>
        <div align="center"></div>
        <p align="center">&nbsp; </p>
      </form>
      <p>&nbsp;</p>
	   
      </td>
    <td width="196" valign="top">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
