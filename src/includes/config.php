<?php

$config = array(
  'servers' => array(),
  'seperator' => ':',
  'maxkeylen'           => 100,
  'count_elements_page' => 100,
  'keys' => false,
  'scansize' => 1000
);

function inject_redis_config($config) {
  $session_name = 'DaoCloudPRA';
  session_name($session_name);

  $redis_config = array(
    'name' => $_SESSION['DAO_service_name'], 
    'host' => $_SESSION['PRA_single_signon_host'],
    'port' => $_SESSION['PRA_single_signon_port'],
    'scheme' => 'tcp',
    'auth' => $_SESSION['PRA_single_signon_password']
    );

  array_push($config['servers'], $redis_config);
  return $config
}

inject_redis_config($config);
?>
