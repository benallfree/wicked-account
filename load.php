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
