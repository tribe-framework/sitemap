<?php
namespace Wildfire;

$dash = new Core\Dash();
$admin = new Core\Admin();
$sql = new Core\MySQL();
$auth = new Auth\Auth();

$types = $dash->getTypes();
$menus = $dash->getMenus();
$currentUser = $auth->getCurrentUser();

//max script execution time 1 min
set_time_limit(60);

$type = 'sitemap_xml';