<?php
session_start();

//Include Google client library 
include_once 'src/Google_Client.php';
include_once 'src/contrib/Google_Oauth2Service.php';

/*
 * Configuration and setup Google API
 */
$clientId = '129643632700-vp6qqnfk0farjukoq4bqllijq006qvf3.apps.googleusercontent.com'; //Google client ID
$clientSecret = '-s5-lu_ZwKH9ptjqRMiTZgdY'; //Google client secret
$redirectURL = 'https://brandoholic.memology.me/g-login/index.php'; //Callback URL

//Call Google API
$gClient = new Google_Client();
$gClient->setApplicationName('Login to CodexWorld.com');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectURL);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>