<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['google_client_id'] = 'YOUR_CLIENT_ID';
$config['google_client_secret'] = 'YOUR_CLIENT_SECRET';
$config['google_redirect_uri'] = 'http://localhost/gbp/oauth/callback';
$config['google_scopes'] = [
    'https://www.googleapis.com/auth/business.manage'
];
?>
