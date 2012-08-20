<?


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

User::add_function('is_password', function($u, $pass) {
  return $u->password === $u->hashify($pass);
});

User::add_function('hashify', function($u, $val) {
  return sha1($val . $u->salt);
});

User::add_function('set_password', function($u, $new_password) {
  $u->password = $u->hashify($new_password, $u->salt);
  $u->save();
});

User::add_function('send_password_reset', function($u) {
  $request = W::request();
  $host  = $request['host'];
  $reset_link = W::sc_create_url('W::user_reset', $u->id);
  $reset_link = "http://{$host}{$reset_link}";

  list($subject,$body) = W::template('account.forgot', array('reset_link'=>$reset_link));
  W::swiftmail_send($u->email, $subject, $body);
});