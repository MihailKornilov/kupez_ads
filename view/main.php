<?php
function _header() {
	return
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
	'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">'.
	'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251">'.
		'<title>������ ������ - ������ ����������</title>'.

		'<script type="text/javascript" src="/.vkapp/.js/errors.js"></script>'.

		'<script type="text/javascript" src="/.vkapp/.js/jquery-2.0.3.min.js"></script>'.
		'<script type="text/javascript" src="/.vkapp/kupez/js/G_values.js"></script>'.

		'<script type="text/javascript">'.
			'var URL="http://'.DOMAIN.'/kupez";'.
		'</script>'.
		'<script type="text/javascript" src="js/main.js"></script>'.

		'<link rel="stylesheet" type="text/css" href="css/main.css" />'.
	'</head>'.

    '<body>';
}//_header()
function _kupez_footer() {
	return
		'</body>'.
	'</html>';
}//_footer()



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
}//_body()

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
}//menu()

function forma() {
	$sql = "SELECT * FROM `setup_ob_dop`";
	$q = query($sql);
	$dop = array();
	while($r = mysql_fetch_assoc($q))
		$dop[$r['id']] = $r;

	$sql = "SELECT * FROM `setup_rubric` ORDER BY `sort`";
	$q = query($sql);
	$rubric = '';
	while($r = mysql_fetch_assoc($q))
		$rubric .= '<option value="'.$r['id'].'">'.$r['name'];

	return
	'<table id="ad-form"'.(AD_POSTED_ID ? ' class="dn"' : '').'>'.
//		'<tr><td class="label">�.�.�:<td><input type="text" />'.
//		'<tr><td class="label">������� ��� �����:<td><input type="text" />'.
		'<tr><td class="label">�������:'.
			'<td class="title">'.
				'<select id="rubric_id">'.
					'<option value="0">'.
					$rubric.
				'</select>'.
				'<select id="rubric_sub_id"></select>'.
		'<tr><td class="label top">�����:'.
			'<td class="title">'.
				'<textarea id="txt" autofocus></textarea>'.
				'<div id="ob-calc"></div>'.
		'<tr><td class="label">�������������:'.
			'<td class="title">'.
				'<label><input type="checkbox" id="ramka" /> ������� � ����� <em>(+'.$dop[1]['cena'].' ���.)</em></label>'.
				'<label><input type="checkbox" id="black" /> ׸���� ��� <em>(+'.$dop[2]['cena'].' ���.)</em></label>'.
		'<tr><td class="label">�������:<td class="title"><input type="text" id="telefon" placeholder="8 900 123 45 67" />'.
		'<tr><td class="label top">������ ������:<td class="title">'.gazetaNomer().
	'</table>';
}
function gazetaNomer() {
	$sql = "SELECT *
			FROM `gazeta_nomer`
			WHERE `day_print`>DATE_FORMAT(NOW(),'%Y-%m-%d')
			ORDER BY `general_nomer`
			LIMIT 4";
	$q = query($sql);
	$spisok = '';
	while($r = mysql_fetch_assoc($q)) {
		$spisok .=
			'<table class="nomer dis" val="'.$r['general_nomer'].'">'.
				'<tr><td class="td-head">'.
						'<span>����� <b>'.$r['week_nomer'].'</b></span>'.
						'<span class="gn">('.$r['general_nomer'].')</span>.'.
						'<span class="print">���� ������: '.FullData($r['day_public'], 1).'</span>'.
						'<span class="cena"></span>'.
			'</table>';
	}
	return '<div id="gazeta-nomer">'.$spisok.'</div>';
}//gazetaNomer()

function forma_sended() {//��������� �� �������� ���������� ����������
	$paid = '<div id="pay-wait">��� �������� �������...</div>';
	if(AD_POSTED_ID) {
		$sql = "SELECT * FROM `gazeta_money` WHERE `zayav_id`=".AD_POSTED_ID." AND `viewer_id_add`=".VIEWER_ONPAY." LIMIT 1";
		if($r = query_assoc($sql)) {
			$paid = '<div id="pay-success">����� �� ����� '.round($r['sum']).' ���. ��������.</div>';
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
}//forma_sended()

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
				'���� ���������� ����������� ����� � 4 ������, �� ���� ����� ���������� ����������.'.
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
}//instruction()

function terms() {
	$sql = "SELECT * FROM `setup_ob_dop`";
	$q = query($sql);
	$dop = array();
	while($r = mysql_fetch_assoc($q))
		$dop[$r['id']] = $r;

	$g = query_assoc("SELECT * FROM `setup_global`");

	return
	'<div class="menu" id="menu3">'.
		'<div id="tab-head">������� ����������</div>'.
		'<div class="menu-info">'.
			'<p>���������� ���������� � ������ ������ �������� ������� �������.'.
			'<p>���������� ��������� ���������� ������������ �� ��������� �����:'.
			'<table>'.
				'<tr><td>������ '.$g['txt_len_first'].' ��������:<td>'.$g['txt_cena_first'].' ���.'.
				'<tr><td>������ ��������� '.$g['txt_len_next'].' ��������:<td>'.$g['txt_cena_next'].' ���.'.
				'<tr><td>������� � �����:<td>'.$dop[1]['cena'].' ���.'.
				'<tr><td>�������� �� ������ ����:<td>'.$dop[2]['cena'].' ���.'.
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
}//terms()

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
}//contact()



