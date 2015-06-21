<?php
function _header() {
	return
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
	'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">'.
	'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251">'.
		'<title>Газета КупецЪ - подача объявления</title>'.

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
			'<div id="logo-podpis">ЕЖЕНЕДЕЛЬНАЯ ГАЗЕТА</div>'.
		'<tr><td id="td-menu">'.menu().
		'<tr><td id="td-main">'.
			'<div id="content">'.
				'<div class="menu" id="menu1">'.
					'<div id="tab-head">Создание нового объявления</div>'.
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
						'<em>Отправка формы, пожалуйста, ожидайте...</em>'.
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
				'<td val="1" class="s">Новое объявление'.
				'<td val="2">Инструкция'.
				'<td val="3">Условия размещения'.
				'<td val="4">Контакты'.
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
//		'<tr><td class="label">Ф.И.О:<td><input type="text" />'.
//		'<tr><td class="label">Телефон для связи:<td><input type="text" />'.
		'<tr><td class="label">Рубрика:'.
			'<td class="title">'.
				'<select id="rubric_id">'.
					'<option value="0">'.
					$rubric.
				'</select>'.
				'<select id="rubric_sub_id"></select>'.
		'<tr><td class="label top">Текст:'.
			'<td class="title">'.
				'<textarea id="txt" autofocus></textarea>'.
				'<div id="ob-calc"></div>'.
		'<tr><td class="label">Дополнительно:'.
			'<td class="title">'.
				'<label><input type="checkbox" id="ramka" /> Обвести в рамку <em>(+'.$dop[1]['cena'].' руб.)</em></label>'.
				'<label><input type="checkbox" id="black" /> Чёрный фон <em>(+'.$dop[2]['cena'].' руб.)</em></label>'.
		'<tr><td class="label">Телефон:<td class="title"><input type="text" id="telefon" placeholder="8 900 123 45 67" />'.
		'<tr><td class="label top">Номера газеты:<td class="title">'.gazetaNomer().
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
						'<span>Номер <b>'.$r['week_nomer'].'</b></span>'.
						'<span class="gn">('.$r['general_nomer'].')</span>.'.
						'<span class="print">День выхода: '.FullData($r['day_public'], 1).'</span>'.
						'<span class="cena"></span>'.
			'</table>';
	}
	return '<div id="gazeta-nomer">'.$spisok.'</div>';
}//gazetaNomer()

function forma_sended() {//сообщение об успешном размещении объявления
	$paid = '<div id="pay-wait">Идёт проверка платежа...</div>';
	if(AD_POSTED_ID) {
		$sql = "SELECT * FROM `gazeta_money` WHERE `zayav_id`=".AD_POSTED_ID." AND `viewer_id_add`=".VIEWER_ONPAY." LIMIT 1";
		if($r = query_assoc($sql)) {
			$paid = '<div id="pay-success">Платёж на сумму '.round($r['sum']).' руб. зачислен.</div>';
		}
	}
	return
	'<div id="ad-form-sended"'.(!AD_POSTED_ID ? ' class="dn"' : '').'>'.
		'<div id="msg">Ваше объявление № <b>'.AD_POSTED_ID.'</b> успешно размещено!</div>'.
		'<div id="info">'.
			'После зачисления платежа и проверки сотрудником газеты содержания объявления, '.
			'оно будет опубликовано в выбранных Вами номерах газеты.'.
		'</div>'.
		$paid.
	'</div>';
}//forma_sended()

function instruction() {
	return
	'<div class="menu" id="menu2">'.
		'<div id="tab-head">Порядок подачи объявления</div>'.
		'<div class="menu-info">'.
			'<p>При составлении объявления обязательно должны быть заполнены все поля. '.
			'<h1>Текст</h1>'.
			'<p>Постарайтесь более понятно описать свой товар или услугу. '.
				'В процессе написания выводится предварительная стоимость объявления и его длина. '.
				'В содержании не учитываются символы: точки, запятые, слеши и двойные кавычки. '.
				'Не указывайте номер телефона, для этого есть отдельное поле.'.
			'<h1>Дополнительные параметры</h1>'.
			'<p>Объявление можно выделить, выбрав один из дополнительных параметров:'.
			'<dl>'.
				'<dd>- обвести в рамку'.
				'<dd>- на чёрном фоне'.
			'</dl>'.
			'<p>Параметр применяется ко всем выбранным номерам газеты.'.
			'<h1>Номера газеты</h1>'.
			'<p>Объявление можно подать в ближайшие 4 номера газеты. '.
				'Если объявление размещается сразу в 4 номера, то один номер становится бесплатным.'.
			'<p>Как правило, очередной номер газеты выходит каждую пятницу. '.
				'Крайний срок подачи объявления на текущий номер - понедельник.'.
			'<h1>Отправка и оплата</h1>'.
			'<p>После того, как заполнены все поля и выбраны номера газеты, '.
				'кнопка отправки объявления становится зелёной и в ней указана окончательная стоимость объявления. '.
				'Далее Вам будет предложено произвести оплату в системе приёма платежей Onpay одним из удобных для Вас способов. '.
			'<p>После проверки платежа сотрудником газеты КупецЪ объявление будет добавлено в печать, а также размещено в приложении. '.
				'Сотрудники могут изменить содержание объявления, если в нём будут найдены ошибки, либо запретить размещение объявления, '.
				'если оно нарушает правила.'.

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
		'<div id="tab-head">Условия размещения</div>'.
		'<div class="menu-info">'.
			'<p>Размещение объявлений в газете КупецЪ является платной услугой.'.
			'<p>Вычисление стоимости объявления производится по следующей схеме:'.
			'<table>'.
				'<tr><td>Первые '.$g['txt_len_first'].' символов:<td>'.$g['txt_cena_first'].' руб.'.
				'<tr><td>Каждые следующие '.$g['txt_len_next'].' символов:<td>'.$g['txt_cena_next'].' руб.'.
				'<tr><td>Обвести в рамку:<td>'.$dop[1]['cena'].' руб.'.
				'<tr><td>Выделить на чёрном фоне:<td>'.$dop[2]['cena'].' руб.'.
			'</table>'.
			'<h1>Сотрудники газеты могут отказать в размещении объявлений следующего содержания:</h1>'.
			'<ul><li>товары, производство и (или) реализация которых запрещены законодательством Российской Федерации;'.
				'<li>наркотические средства, прихотропные вещества и прекурсоры;'.
				'<li>взрывчатых веществ и материалов, за исключением пиротехнических изделий;'.
				'<li>органов и (или) тканей человека в качестве объектов купли-продажи;'.
				'<li>товаров, подлежащих государственной регистрации, в случае отсутствия такой регистрации;'.
				'<li>товаров, подлежащих обязательной сертификации или иному обязательному подтверждению '.
					'соответствия требованиям технических регламентов, в случае отсутствия такой сертификации '.
					'или подтверждения такого соответствия;'.
				'<li>товары, на производство и (или) реализацию которых требуется получение лицензий '.
					'или иных специальных разрешений, в случае отсутствия таких разрешений.'.
			'</ul>'.
		'</div>'.
	'</div>';
}//terms()

function contact() {
	return
	'<div class="menu" id="menu4">'.
		'<div id="tab-head">Контактная информация</div>'.
		'<div class="menu-info">'.
			'<p>ИП Точенов  Юрий Алексеевич.'.
			'<p><b>КупецЪ</b> - еженедельная газета рекламы и объявлений.'.
			'<p>День отправки в печать: каждый вторник.'.
			'<p>День выхода газеты: каждую пятницу.'.
			'<p>Тираж: 13000 экз. Область распространения: Няндома, Каргополь.'.
			'<p>Газета распространяется бесплатно.'.
			'<table>'.
				'<tr><td>Телефон редакции:<td>8 81838 63777'.
				'<tr><td>Адрес:<td>г.Няндома, ул.Ленина д.34, 2 этаж, офис 1'.
				'<tr><td>Режим работы:<td>Пн-Пт 10-18'.
				'<tr><td>E-mail:<td>kupez29@mail.ru'.
				'<tr><td>Группа ВКонтакте:<td><a href="http://vk.com/kupez_info" target="_blank">vk.com/kupez_info</a>'.
				'<tr><td>Приложение ВКонтакте:<td><a href="http://vk.com/kupezz" target="_blank">vk.com/kupezz</a>'.
			//	'<tr><td>Контактные лица:<td>'.
			'</table>'.

		'</div>'.
	'</div>';
}//contact()



