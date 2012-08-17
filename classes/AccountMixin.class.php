<?

class AccountMixin extends Mixin
{
  static $__prefix = 'user';
  
  protected static $current_user = null;
  
  static function current()
  {
    if(self::$current_user) return self::$current_user;

    self::$current_user = new User();
    if(W::session_get('user_id'))
    {
      self::$current_user = User::find_by_id(W::session_get('user_id'));
    }
    return self::$current_user;
  }
  
  static function is_logged_in()
  {
    return W::session_get('user_id');
  }

  static function login($user, $location=null)
  {
    global $__wicked;
    $config = $__wicked['modules']['account'];
    
    // this sets variables in the session 
    $_SESSION['user_id']= $user->id;  
    $_SESSION['user_name'] = $user->login;
    $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
  
    //update the timestamp and key for cookie
    $stamp = time();
    $ckey = GenKey();
    $user->ctime = $stamp;
    $user->ckey = $ckey;
    if(!$user->is_active)
    {
      flash_next("Your account has been undeleted. Welcome back!");
    }
    $user->is_active=true;
    $user->save();
    
    global $current_user;
    $current_user = $user;
    
    //set a cookie 
    
    if(isset($_POST['remember']))
    {
      setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*COOKIE_TIME_OUT, "/");
      setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
      setcookie("user_name",$_SESSION['user_name'], time()+60*60*24*COOKIE_TIME_OUT, "/");
    }
    event('login', $current_user);
    if($location===null) $location = $config['after_login_url'];
    if($location) redirect_to($location);
  }
  
  
  static function logout()
  {
    if(!is_logged_in()) return;
  
    /************ Delete the sessions****************/
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_level']);
    unset($_SESSION['HTTP_USER_AGENT']);
    
    /* Delete the cookies*******************/
    setcookie("user_id", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
    setcookie("user_name", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
    setcookie("user_key", '', time()-60*60*24*COOKIE_TIME_OUT, "/");
    
    flash_next("You have been logged out.");
    redirect_to('/');
  }
  
  
  static function get($user_email)
  {
    if (strpos($user_email,'@') === false) {
        $user_cond = "user_name='$user_email'";
    } else {
          $user_cond = "user_email='$user_email'";
        
    }
    
    	
    $result = mysql_query("SELECT * FROM users WHERE 
               $user_cond
    			AND `banned` = '0'
    			") or die (mysql_error()); 
    $num = mysql_num_rows($result);
    if($num==0) return null;
    $user = mysql_fetch_assoc($result);  
    return $user;
  }
  
  static function reset($id)
  {
    $user = User::find_by_id($id);
  
    if ( !$user ) { 
    	W::flash_next("Sorry no such account exists or reset code is invalid.");
    	W::redirect_to('/');
    } else {
      self::login($user, '/account');
    }
  }

  function activate($id)
  {
    $u = User::find_by_id($id);
    $u->activated_at = time();
    $u->save();
    W::flash_next("Thank you. Your account has been activated.");
    
    list($subject,$body) = template('account.welcome');
    W::swiftmail($u->email, $subject, $body, true);
    
    W::action('account_activated', $u);
    self::login($u);
    W::redirect_to(W::filter('after_activation_url', $config['after_activation_url'], $u));
  }
  
}