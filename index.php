<?php
require_once('config.php');

//setcookie('debug', 1, time() + 3600, '/');

require_once('view/main.php');

$send = _header();
$send .= _body();
$send .= _kupez_footer();

echo $send;
