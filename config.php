<?php
define('DOCUMENT_ROOT', dirname(__FILE__));
define('NAMES', 'cp1251');

define('APP_ID', 3495523);
define('VIEWER_ONPAY', 2147000001);
define('VIEWER_ID', VIEWER_ONPAY);

define('CENA_KOEF', 1); //�����������, �� ������� ������������� ��������� ����������


require_once(DOCUMENT_ROOT.'/../.vkapp/.api_next/syncro.php');
//require_once(DOCUMENT_ROOT.'/../.vkapp/.api/syncro.php');
require_once(API_PATH.'/view/_vk.php');
