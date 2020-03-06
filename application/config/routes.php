<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['AccountManagement/(:any)'] = "mobile-application-controllers/AccountManagement/$1";

