<?

$config = array(
  'requires'=>array('swiftmail', 'eval_php', 'db', 'activerecord', 'meta', 'template', 'string', 'date'),
  'after_activation_url'=>'/',
  'after_login_url'=>'/',
  'recaptcha'=>array(
    'public_key'=>'',
    'private_key'=>'',
  ),
  'should_allow_open_registration'=>true,
  'observes'=>array(
    'find_login',
    'add_globals',
  ),
);
