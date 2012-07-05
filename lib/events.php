<?

function account_add_globals($g)
{
  $current_user = new User();
  if(isset($_SESSION['user_id']) && $_SESSION['user_id'])
  {
    $current_user = User::find_by_id($_SESSION['user_id']);
  }
  
  $g['current_user'] = $current_user;
  return $g;
}


function account_find_login($junk,$login)
{
  $user = User::find( array(
    'conditions'=>array('email = ? or login = ?', $login, $login),
  ));
  return $user;
}
