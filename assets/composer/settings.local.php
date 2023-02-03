<?php

if (getenv('OPENID_CONNECT_PARAMS') == true) {
  $openid_config_params = json_decode(getenv('OPENID_CONNECT_PARAMS'), true);
  $config['openid_connect.settings']['always_save_userinfo'] = $openid_config_params['always_save_userinfo'];
  $config['openid_connect.settings']['connect_existing_users'] = $openid_config_params['connect_existing_users'];
  $config['openid_connect.settings']['override_registration_settings'] = $openid_config_params['override_registration_settings'];
  $config['openid_connect.settings']['end_session_enabled'] = $openid_config_params['end_session_enabled'];
  $config['openid_connect.settings']['user_login_display'] = $openid_config_params['user_login_display'];
  foreach($openid_config_params['clients'] as $client => $value) {
    $config['openid_connect.client.' . $client]['status'] = $value['status'];
    $config['openid_connect.client.' . $client]['settings']['tenant'] = $value['settings']['tenant'];
    $config['openid_connect.client.' . $client]['settings']['client_id'] = $value['settings']['client_id'];
    $config['openid_connect.client.' . $client]['settings']['client_secret'] = $value['settings']['client_secret'];
  }
}
