<?

W::register_filter('find_login', function($u, $login) {
  $user = User::find( array(
    'conditions'=>array('email = ? or login = ?', $login, $login),
  ));
  return $user;
});


W::$current_user = new User();
if(W::session_get('user_id'))
{
  W::$current_user = User::find_by_id(W::session_get('user_id'));
}

