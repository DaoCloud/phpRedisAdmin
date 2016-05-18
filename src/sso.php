<?php

require_once dirname(__FILE__) . '/sso.config.php';

error_reporting(0);
session_set_cookie_params(1500, '/', '', false);
/* Create signon session */
$session_name = 'DaoCloudPRA';
$redirect_uri = 'https://dashboard.daocloud.io/services';
session_name($session_name);
session_start();

if (isset($_POST['token']) && isset($_POST['uuid']) && isset($_POST['org'])) {
    $uri = sprintf('https://api.daocloud.io/v1/service-instances/%s', $_POST['uuid']);
    $auth_header = array(sprintf('Authorization: %s', $_POST['token']),
                         sprintf('UserNameSpace: %s', $_POST['org'])
                         );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $auth_header);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $service_info = json_decode($response, true);
    $redis_conf = array();
    foreach ($service_info['config'] as $config) {
        switch ($config['config_name']) {
            case '6379/tcp':
                preg_match('/(?P<host>(\d{1,3}\.){3}\d{1,3}):(?P<port>\d+)/', $config['config_value'], $matchd);
                $redis_conf['host'] = $matchd['host'];
                $redis_conf['port'] = $matchd['port'];
                break;

            case 'PASSWORD':
                $redis_conf['password'] = $config['config_value'];
                break;
        }
    }
    $_SESSION['PRA_single_signon_password'] = $redis_conf['password'];
    $_SESSION['PRA_single_signon_host'] = $redis_conf['host'];
    $_SESSION['PRA_single_signon_port'] = $redis_conf['port'];
    $_SESSION['DAO_service_name'] = $service_info['service_instance_name'];
    
    session_write_close();
    header('Location: ./index.php');
    exit();
} else {
    session_destroy();
    header(sprintf('Location: %s', $redirect_uri));
}
?>
