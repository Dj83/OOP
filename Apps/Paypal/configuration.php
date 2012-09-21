<?php

 defined('_JEXEC') or die;

final class JConfig
{
	public $theme = 'default';
	//public $secret = '';
 	public $sess_lifetime = '150';
	//public $sess_handler = 'database';
 	public $force_ssl = '0';
 	//public $site_uri = '';
	//public $media_uri = '/';
 	public $caching = '0';

 	public $PayPalAPIUsername = 'daniel_1329156502_biz_api1.utoledo.edu';
 	public $PayPalAPIPassword = '1329156538';
 	public $PayPalAPISignature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31ALo4NZCta67rXXsahJm9LekyaY9z ';
 	public $PayPalAPISubject = '';
 	public $PayPalAPIVersion = '2.3';
 	public $PayPalAPIAuthorizationMode = '3TOKEN';
 	public $PayPalAPIUrl = '';
 	public $PayPalAPIEndPoint = 'https://api-3t.sandbox.paypal.com/nvp';
}