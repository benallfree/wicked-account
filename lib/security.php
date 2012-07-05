<?

function page_protect($flash='Please log in.',$redirect = null) {
  if($redirect===null) $redirect = $_SERVER['REQUEST_URI'];
  global $db; 
  
  /* Secure against Session Hijacking by checking user agent */
  if (isset($_SESSION['HTTP_USER_AGENT']))
  {
      if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
      {
          logout();
          exit;
      }
  }
  
  // before we allow sessions, we need to check authentication key - ckey and ctime stored in database
  
  /* If session not set, check for cookies set by Remember me */
  if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_name']) ) 
  {
  	if(isset($_COOKIE['user_id']) && isset($_COOKIE['user_key'])){
  	/* we double check cookie expiry time against stored in database */

  	$cookie_user_id  = account_filter($_COOKIE['user_id']);
  	$rs_ctime = mysql_query("select `ckey`,`ctime` from `users` where `id` ='$cookie_user_id'") or die(mysql_error());
  	list($ckey,$ctime) = mysql_fetch_row($rs_ctime);
  	// coookie expiry
  	if( (time() - $ctime) > 60*60*24*COOKIE_TIME_OUT) {
  
  		logout();
  		}
  /* Security check with untrusted cookies - dont trust value stored in cookie. 		
  /* We also do authentication check of the `ckey` stored in cookie matches that stored in database during login*/
  
  	 if( !empty($ckey) && is_numeric($_COOKIE['user_id']) && isUserID($_COOKIE['user_name']) && $_COOKIE['user_key'] == sha1($ckey)  ) {
  	 	  session_regenerate_id(); //against session fixation attacks.
  	
  		  $_SESSION['user_id'] = $_COOKIE['user_id'];
  		  $_SESSION['user_name'] = $_COOKIE['user_name'];
  		/* query user level from database instead of storing in cookies */	
  		  list($user_level) = mysql_fetch_row(mysql_query("select user_level from users where id='$_SESSION[user_id]'"));
  
  		  $_SESSION['user_level'] = $user_level;
  		  $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
  		  
  	   } else {
  	   logout();
  	   }
  
    } else {
      flash_next($flash);
      redirect_to("/account/login?r=".h($redirect));
  	}
  }
}