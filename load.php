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

W::register_filter('portal_variables', function($vars) {
  $vars['current_user'] = W::user_current();
  return $vars;
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

W::register_action('portal_prerender', function($caps) {
  if(!W::user_is_logged_in()) return;
  W::session_extend('user_id');
});

require('models/user.php');
