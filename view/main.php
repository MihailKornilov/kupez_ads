<?php
function _kupez_header() {
	define('VERSION_SCRIPT', 1);
	return
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
	'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">'.
	'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251">'.
		'<title>������ ������ - ������ ����������</title>'.

		'<script src="/.vkapp/.js/errors.js"></script>'.
		'<script src="/.vkapp/.js/jquery-2.1.4.min.js"></script>'.
		'<script src="'.API_HTML.'/js/xd_connection.min.js"></script>'.

		'<script>'.
			'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false};'.
			'var URL="http://'.DOMAIN.'/kupez",'.
				'APP_ID='.APP_ID.','.
				'VIEWER_ID='.VIEWER_ID.','.
				'CENA_KOEF='.CENA_KOEF.';'.
		'</script>'.

		'<link rel="stylesheet" type="text/css" href="'.API_HTML.'/css/vk.css?'.VERSION_SCRIPT.'" />'.
		'<script src="'.API_HTML.'/js/vk.js?'.VERSION_SCRIPT.'"></script>'.

		'<script src="'.API_HTML.'/js/values/app_'.APP_ID.'.js?'.VERSION_SCRIPT.'"></script>'.
		'<script src="js/main.js?'.VERSION_SCRIPT.'"></script>'.

		'<link rel="stylesheet" type="text/css" href="css/main.css?'.VERSION_SCRIPT.'" />'.
	'</head>'.

    '<body>';
}
function _kupez_footer() {
	return
		'</body>'.
	'</html>';
}



function _body() {
	define('AD_POSTED_ID', _num(@$_COOKIE['ad_posted_id']));
	setcookie('ad_posted_id', 0, time() - 100000, '/');
	return
	'<table id="table-main">'.
		'<tr><td id="td-logo">'.
			'<div id="logo"></div>'.
			'<div id="logo-podpis">������������ ������</div>'.
		'<tr><td id="td-menu">'.menu().
		'<tr><td id="td-main">'.
			'<div id="content">'.
				'<div class="menu" id="menu1">'.
					'<div id="tab-head">�������� ������ ����������</div>'.
					forma().
					forma_sended().
				'</div>'.
				instruction().
				terms().
				contact().
			'</div>'.
	(!AD_POSTED_ID ?
		'<tr id="tr-submit">'.
			'<td id="td-submit">'.
				'<div id="div-submit">'.
					'<div id="submit">'.
						'<span></span>'.
						'<em>�������� �����, ����������, ��������...</em>'.
					'</div>'.
				'</div>'
	: '').
	'</table>';
}

function menu() {
	return
	'<div id="menu">'.
		'<table>'.
			'<tr>'.
				'<td val="1" class="s">����� ����������'.
				'<td val="2">����������'.
				'<td val="3">������� ����������'.
				'<td val="4">��������'.
		'</table>'.
	'</div>';
}

function forma() {
	$sql = "SELECT *
			FROM `_setup_gazeta_ob_dop`
			WHERE `app_id`=".APP_ID;
	$dop = query_arr($sql);

	return
	'<table id="ad-form"'.(AD_POSTED_ID ? ' class="dn"' : '').'>'.
//		'<tr><td class="label">�.�.�:<td><input type="text" />'.
//		'<tr><td class="label">������� ��� �����:<td><input type="text" />'.
		'<tr><td class="label">�������:'.
			'<td class="title">'.
				'<input type="hidden" id="rubric_id" />'.
				'<input type="hidden" id="rubric_id_sub" />'.
		'<tr><td class="label top">�����:'.
			'<td class="title">'.
				'<textarea id="txt" autofocus></textarea>'.
				'<div id="ob-calc"></div>'.
		'<tr><td class="label">�������������:'.
			'<td class="title">'.
				_check('ramka', '������� � ����� <em>(+'.round($dop[1]['cena'] * CENA_KOEF).' ���.)</em>').
				_check('black', '׸���� ��� <em>(+'.round($dop[2]['cena'] * CENA_KOEF).' ���.)</em>').
		'<tr><td class="label">�������:<td class="title"><input type="text" id="telefon" placeholder="8 900 123 45 67" />'.
		'<tr><td class="label top">������ ������:<td class="title">'.gazetaNomer().
	'</table>';
}
function gazetaNomer() {
	$sql = "SELECT *
			FROM `_setup_gazeta_nomer`
			WHERE `app_id`=".APP_ID."
			  and `day_print`>DATE_FORMAT(NOW(),'%Y-%m-%d')
			ORDER BY `general_nomer`
			LIMIT 4";
	$q = query($sql);
	$spisok = '';
	while($r = mysql_fetch_assoc($q)) {
		$spisok .=
			'<table class="nomer dis" val="'.$r['id'].'">'.
				'<tr><td class="td-head">'.
						'<span>����� <b>'.$r['week_nomer'].'</b></span>'.
						'<span class="gn">('.$r['general_nomer'].')</span>.'.
						'<span class="print">���� ������: '.FullData($r['day_public'], 1).'</span>'.
						'<span class="cena"></span>'.
			'</table>';
	}
	return '<div id="gazeta-nomer">'.$spisok.'</div>';
}

function forma_sended() {//��������� �� �������� ���������� ����������
	$paid = '<div id="pay-wait">��� �������� �������...</div>';
	if(AD_POSTED_ID) {
		$sql = "SELECT *
				FROM `_money_income`
				WHERE `zayav_id`=".AD_POSTED_ID."
				  AND `viewer_id_add`=".VIEWER_ONPAY."
				LIMIT 1";
		if($r = query_assoc($sql)) {
			$sql = "SELECT *
					FROM `_money_onpay`
					WHERE `income_id`=".$r['id'];
			if($mo = query_assoc($sql))
				$paid = '<div id="pay-success">����� �� ����� '.round($mo['order.from_amount']).' ���. ��������.</div>';
		}
	}
	return
	'<div id="ad-form-sended"'.(!AD_POSTED_ID ? ' class="dn"' : '').'>'.
		'<div id="msg">���� ���������� � <b>'.AD_POSTED_ID.'</b> ������� ���������!</div>'.
		'<div id="info">'.
			'����� ���������� ������� � �������� ����������� ������ ���������� ����������, '.
			'��� ����� ������������ � ��������� ���� ������� ������.'.
		'</div>'.
		$paid.
	'</div>';
}

function instruction() {
	return
	'<div class="menu" id="menu2">'.
		'<div id="tab-head">������� ������ ����������</div>'.
		'<div class="menu-info">'.
			'<p>��� ����������� ���������� ����������� ������ ���� ��������� ��� ����. '.
			'<h1>�����</h1>'.
			'<p>������������ ����� ������� ������� ���� ����� ��� ������. '.
				'� �������� ��������� ��������� ��������������� ��������� ���������� � ��� �����. '.
				'� ���������� �� ����������� �������: �����, �������, ����� � ������� �������. '.
				'�� ���������� ����� ��������, ��� ����� ���� ��������� ����.'.
			'<h1>�������������� ���������</h1>'.
			'<p>���������� ����� ��������, ������ ���� �� �������������� ����������:'.
			'<dl>'.
				'<dd>- ������� � �����'.
				'<dd>- �� ������ ����'.
			'</dl>'.
			'<p>�������� ����������� �� ���� ��������� ������� ������.'.
			'<h1>������ ������</h1>'.
			'<p>���������� ����� ������ � ��������� 4 ������ ������. '.
				'���� ���������� ����������� ����� � 3 ������, �� ��������� ����� ���������.'.
			'<p>��� �������, ��������� ����� ������ ������� ������ �������. '.
				'������� ���� ������ ���������� �� ������� ����� - �����������.'.
			'<h1>�������� � ������</h1>'.
			'<p>����� ����, ��� ��������� ��� ���� � ������� ������ ������, '.
				'������ �������� ���������� ���������� ������ � � ��� ������� ������������� ��������� ����������. '.
				'����� ��� ����� ���������� ���������� ������ � ������� ����� �������� Onpay ����� �� ������� ��� ��� ��������. '.
			'<p>����� �������� ������� ����������� ������ ������ ���������� ����� ��������� � ������, � ����� ��������� � ����������. '.
				'���������� ����� �������� ���������� ����������, ���� � �� ����� ������� ������, ���� ��������� ���������� ����������, '.
				'���� ��� �������� �������.'.

		'</div>'.
	'</div>';
}

function terms() {
	$sql = "SELECT *
			FROM `_setup_gazeta_ob_dop`
			WHERE `app_id`=".APP_ID;
	$dop = query_arr($sql);

	$sql = "SELECT `key`,`value`
			FROM `_setup_global`
			WHERE `app_id`=".APP_ID;
	$g = query_ass($sql);

	return
	'<div class="menu" id="menu3">'.
		'<div id="tab-head">������� ����������</div>'.
		'<div class="menu-info">'.
			'<p>���������� ���������� � ������ ������ �������� ������� �������.'.
			'<p>���������� ��������� ���������� ������������ �� ��������� �����:'.
			'<table>'.
				'<tr><td>������ '.$g['TXT_LEN_FIRST'].' ��������:'.
					'<td class="r">'.round($g['TXT_CENA_FIRST'] * CENA_KOEF).' ���.'.
				'<tr><td>������ ��������� '.$g['TXT_LEN_NEXT'].' ��������:'.
					'<td class="r">'.round($g['TXT_CENA_NEXT'] * CENA_KOEF).' ���.'.
				'<tr><td>������� � �����:'.
					'<td class="r">'.round($dop[1]['cena'] * CENA_KOEF).' ���.'.
				'<tr><td>�������� �� ������ ����:'.
					'<td class="r">'.round($dop[2]['cena'] * CENA_KOEF).' ���.'.
			'</table>'.
			'<h1>���������� ������ ����� �������� � ���������� ���������� ���������� ����������:</h1>'.
			'<ul><li>������, ������������ � (���) ���������� ������� ��������� ����������������� ���������� ���������;'.
				'<li>������������� ��������, ������������ �������� � ����������;'.
				'<li>���������� ������� � ����������, �� ����������� ��������������� �������;'.
				'<li>������� � (���) ������ �������� � �������� �������� �����-�������;'.
				'<li>�������, ���������� ��������������� �����������, � ������ ���������� ����� �����������;'.
				'<li>�������, ���������� ������������ ������������ ��� ����� ������������� ������������� '.
					'������������ ����������� ����������� �����������, � ������ ���������� ����� ������������ '.
					'��� ������������� ������ ������������;'.
				'<li>������, �� ������������ � (���) ���������� ������� ��������� ��������� �������� '.
					'��� ���� ����������� ����������, � ������ ���������� ����� ����������.'.
			'</ul>'.
		'</div>'.
	'</div>';
}

function contact() {
	return
	'<div class="menu" id="menu4">'.
		'<div id="tab-head">���������� ����������</div>'.
		'<div class="menu-info">'.
			'<p>�� �������  ���� ����������.'.
			'<p><b>������</b> - ������������ ������ ������� � ����������.'.
			'<p>���� �������� � ������: ������ �������.'.
			'<p>���� ������ ������: ������ �������.'.
			'<p>�����: 13000 ���. ������� ���������������: �������, ���������.'.
			'<p>������ ���������������� ���������.'.
			'<table>'.
				'<tr><td>������� ��������:<td>8 81838 63777'.
				'<tr><td>�����:<td>�.�������, ��.������ �.34, 2 ����, ���� 1'.
				'<tr><td>����� ������:<td>��-�� 10-18'.
				'<tr><td>E-mail:<td>kupez29@mail.ru'.
				'<tr><td>������ ���������:<td><a href="http://vk.com/kupez_info" target="_blank">vk.com/kupez_info</a>'.
				'<tr><td>���������� ���������:<td><a href="http://vk.com/kupezz" target="_blank">vk.com/kupezz</a>'.
			//	'<tr><td>���������� ����:<td>'.
			'</table>'.

		'</div>'.
	'</div>';
}



