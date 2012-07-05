<?

function user_get_activation_check_url($u)
{
  $host  = $_SERVER['HTTP_HOST'];
  $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $a_link = "http://$host$path/account/check?u=".$u->email; 
  return $a_link;
}

function user_get_activation_url($u)
{
  $host  = $_SERVER['HTTP_HOST'];
  $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
  $a_link = "http://$host$path/account/activate?user={$u->md5_id}&activ_code={$u->activation_code}"; 
  return $a_link;
}