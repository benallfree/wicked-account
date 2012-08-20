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
    $config = W::module('account');
    
    $ttl = $config['ttl'];
    if(W::p('remember'))
    {
      $ttl = $config['remember_ttl'];
    }
    
    W::session_set('user_id', $user->id, $ttl);
    
    if(!$user->is_active)
    {
      W::flash_next("Your account has been undeleted. Welcome back!");
    }
    $user->is_active=true;
    $user->save();
    
    self::$current_user = $user;
    
    W::action('login', $user);
    if($location===null) $location = $config['after_login_url'];
    if($location) W::redirect_to($location);
  }
  
  
  static function logout()
  {
    W::dprint(self::is_logged_in());
    if(!self::is_logged_in()) return;
    
    W::session_delete_all();
    
    W::flash_next("You have been logged out.");
    W::redirect_to('/');
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

  static function activate($id)
  {
    $u = User::find_by_id($id);
    $u->activated_at = time();
    $u->save();
    W::flash_next("Thank you. Your account has been activated.");
    
    list($subject,$body) = W::template('account.welcome');
    W::swiftmail_send($u->email, $subject, $body, true);
    
    W::action('account_activated', $u);
    self::login($u);
    W::redirect_to(W::filter('after_activation_url', $config['after_activation_url'], $u));
  }
  
  static function gen_key($length = 7)
  {
    $password = "";
    $possible = "0123456789abcdefghijkmnopqrstuvwxyz"; 
    
    $i = 0; 
      
    while ($i < $length) { 
  
      
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
         
      
      if (!strstr($password, $char)) { 
        $password .= $char;
        $i++;
      }
  
    }
  
    return $password;
  
  }
  
  
  
  static function page_protect($flash='Please log in.',$redirect = null) {
    if(!self::is_logged_in())
    {
      W::flash_next($flash);
      if(!$redirect) 
      {
        $redirect = W::request('path');
      }
      W::redirect_to('/account/login', array('r'=>$redirect));
    }
  }  

  static function filter($data) {
  	$data = trim(W::h(strip_tags($data)));
  
  	if (get_magic_quotes_gpc())
  		$data = stripslashes($data);
  
  	$data = mysql_real_escape_string($data);
  
  	return $data;
  }    
  
  static function isUserID($username)
  {
    return preg_match('/^[a-z\d_]{5,20}$/i', $username);
  }	
  
  
  static function isEmail($email){
    return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email);
  }
  
  static function checkPwd($x,$y) 
  {
    if(empty($x) || empty($y) ) { return false; }
    if (strlen($x) < 4 || strlen($y) < 4) { return false; }
    
    if (strcmp($x,$y) != 0) {
     return false;
     } 
    return true;
  }
  

}