<?
define ("ADMIN_LEVEL", 5);
define ("USER_LEVEL", 1);
define ("GUEST_LEVEL", 0);
define("COOKIE_TIME_OUT", 10); //specify cookie timeout in days (default is 10 days)
define('SALT_LENGTH', 9); // salt for password

W::add_mixin('AccountMixin');

W::register_filter('find_login', function($u, $login) {
  $user = User::find( array(
    'conditions'=>array('email = ? or login = ?', $login, $login),
  ));
  return $user;
});


W::register_action('render_widgets', function() {
  W::haml_eval_file(dirname(__FILE__)."/templates/widget.haml");
});


W::register_filter('admin_links', function($links) {
  if(!has_capability('account_manage_users', $current_user->has_capability('account_manage_users'))) return $links;
  $links[] = array('href'=>'/account/manage', 'title'=>'Users');
  return $links;
});

W::register_filter('capabilities', function($caps) {
  $caps[] = array('id'=>'account_manage_users', 'name'=>'Manage Users', 'description'=>'Manage site users via admin tool.');
  return $caps;
});

User::add_property('activation_check_url', function($u) {
  $host  = $_SERVER['HTTP_HOST'];
  $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $a_link = "http://$host$path/account/check?u=".$u->email; 
  return $a_link;
});

User::add_property('activation_url', function($u) {
  $host  = $_SERVER['HTTP_HOST'];
  $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $a_link = "http://$host$path/account/activate?user={$u->md5_id}&activ_code={$u->activation_code}"; 
  return $a_link;
});

User::add_property('name', function($u) {
  return $u->login;
});

User::add_property('available_roles', function($u) {
  $roles = Role::find_all( array(
    'order'=>'name',
  ));
  return $roles;
});

User::add_property('is_activated', function($u) {
  return $u->activated_at && !$u->is_banned;
});

User::add_function('send_password_reset', function($u) {
  $request = W::request();
  $host  = $request['host'];
  $reset_link = W::sc_create_url('W::user_reset', $u->id);
  $reset_link = "http://{$host}{$reset_link}";

  list($subject,$body) = W::template('account.forgot', array('reset_link'=>$reset_link));
  W::swiftmail_send($u->email, $subject, $body);
});