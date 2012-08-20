<?php 

require_once(dirname(__FILE__)."/../lib/recaptchalib.php");
$err = array();
					 
if(W::p('doRegister')== 'Register') 
{ 
  /******************* Filtering/Sanitizing Input *****************************
  This code filters harmful script code and escapes data of all POST data
  from the user submitted form.
  *****************************************************************/
  foreach($_POST as $key => $value) {
  	$data[$key] = W::user_filter($value);
  }
  
  /********************* RECAPTCHA CHECK *******************************
  This code checks and validates recaptcha
  ****************************************************************/
  if($config['recaptcha']['public_key'])
  {
        $resp = recaptcha_check_answer ($config['recaptcha']['private_key'],
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);
  
        if (!$resp->is_valid) {
          $err[] = "Image Verification failed.";			
        }
  }       
  
  // Validate User Name
  if (!W::user_isUserID($data['user_name'])) {
    $err[] = "ERROR - Invalid user name. It can contain alphabet, number and underscore.";
  }
  
  // Validate Email
  if(!W::user_isEmail($data['usr_email'])) {
    $err[] = "ERROR - Invalid email address.";
  }
  // Check User Passwords
  if (!W::user_checkPwd($data['pwd'],$data['pwd2'])) {
    $err[] = "ERROR - Invalid Password or mismatch. Enter 5 chars or more";
  }
  	  
  // Check for duplicate username
  $user_name = $data['user_name'];
  $rs_duplicate = mysql_query("select count(*) as total from users where login='$user_name'") or die(mysql_error());
  list($total) = mysql_fetch_row($rs_duplicate);
  
  if ($total > 0)
  {
    $err[] = "ERROR - The username already exists. Please try again with different username or use the reset password feature.";
  }
  
  if(empty($err))
  {
    $user_ip = $_SERVER['REMOTE_ADDR'];
    
    $usr_email = $data['usr_email'];
    
    // stores sha1 of password
    $salt = md5($user_name.uniqid().microtime(true));
    
    $u = new User( array(
      'attributes'=>array(
        'login'=>$user_name,
        'email'=>$usr_email,
        'users_ip'=>$user_ip,
        'salt'=>$salt,
      ),
    ));
    $u->set_password($data['pwd']);
    if($u->is_valid)
    {
      W::flash("Please check your email.");
      W::redirect_to('/account/check', array('u'=>$user_name, 'e'=>$usr_email, '_r'=>microtime(true)));
    } else {
      $err = $u->errors;
    }
    
  }					 
}

?>
  <script src="<?=$config['vpath']?>/assets/js/jquery.observe_field.js"></script>
  <script src="<?=$config['vpath']?>/assets/js/jquery.validate.js"></script>
  <script>
  $(document).ready(function(){
    $.validator.addMethod("username", function(value, element) {
        return this.optional(element) || /^[a-z0-9\_]+$/i.test(value);
    }, "Username must contain only letters, numbers, or underscore.");

    $("#regForm").validate();
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
    <td width="732" valign="top"><p>
	<?php 
	 if (isset($_GET['done'])) { ?>
	  <h2>Thank you</h2> Your registration is now complete and you can <a href="login.php">login here</a>";
	 <?php exit();
	  }
	?></p>
      <h3 class="titlehdr">Free Registration / Signup</h3>
      <?=W::filter('se', "Registration is quick and free! Please note that fields marked <span class='required'>*</span> are required.")?>
	 <?php	
	 if(!empty($err))  {
	   echo "<div class=\"msg\"><ul>";
	  foreach ($err as $e) {
	    echo "<li>".W::h($e);
	    }
	  echo "</ul></div>";	
	   }
	 ?> 
	 
	  <br>
      <form action="register" method="post" name="regForm" id="regForm" >
        <table width="95%" border="0" cellpadding="3" cellspacing="3" class="forms">
          <tr> 
            <td>Username<span class="required"><font color="#CC0000">*</font></span></td>
            <td><input name="user_name" type="text" id="user_name" class="required username" minlength="5" value="<?=W::p('user_name')?>" > 
              <input name="btnAvailable" type="button" id="btnAvailable"			  value="Check Availability"> 
              <script>
                $(function() {
                  var check = function()
                  {
                    $("#checkid").html("Please wait..."); 
                    $.get("/account/checkuser",{ 
                      cmd: "check", 
                      user: $("#user_name").val() 
                      } ,function(data){  
                        $("#checkid").html(data);
                      }
                    ); 
                  }
                  $('#user_name').blur(function() {
                    check();
                  });
                  $('#btnAvailable').click(function() {
                    check();
                  });
                });
              </script>
			    <span style="color:red; font: bold 12px verdana; " id="checkid" ></span> 
            </td>
          </tr>
          <tr> 
            <td>Your Email<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="usr_email" type="text" id="usr_email3" class="required email" value="<?=W::p('usr_email')?>"> 
          </tr>
          <tr> 
            <td>Password<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="pwd" type="password" class="required password" minlength="5" id="pwd"  value="<?=W::p('pwd')?>"> 
              <div class="example">** 5 chars minimum</div></td>
          </tr>
          <tr> 
            <td>Retype Password<span class="required"><font color="#CC0000">*</font></span> 
            </td>
            <td><input name="pwd2"  id="pwd2" class="required password" type="password" minlength="5" equalto="#pwd"  value="<?=W::p('pwd2')?>"></td>
          </tr>
          <tr> 
            <td colspan="2">&nbsp;</td>
          </tr>
          <? if($config['recaptcha']['public_key']): ?>
            <tr> 
              <td width="22%"><strong>Image Verification </strong></td>
              <td width="78%"> 
                <?php 
  				echo recaptcha_get_html($config['recaptcha']['public_key']);
  			?>
              </td>
            </tr>
          <? endif; ?>
        </table>
        <p align="center">
          <input name="doRegister" type="submit" id="doRegister" value="Register">
        </p>
      </form>
      </td>
    <td width="196" valign="top">&nbsp;</td>
  </tr>
  <tr> 
    <td colspan="3">&nbsp;</td>
  </tr>
</table>
