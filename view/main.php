<?php
function _kupez_header() {
	define('VERSION_SCRIPT', 1);
	return
	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.
	'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">'.
	'<head>'.
		'<meta http-equiv="content-type" content="text/html; charset=windows-1251">'.
		'<title>Газета КупецЪ - подача объявления</title>'.

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
}

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
}

function forma() {
	$sql = "SELECT *
			FROM `_setup_gazeta_ob_dop`
			WHERE `app_id`=".APP_ID;
	$dop = query_arr($sql);

	return
	'<table id="ad-form"'.(AD_POSTED_ID ? ' class="dn"' : '').'>'.
//		'<tr><td class="label">Ф.И.О:<td><input type="text" />'.
//		'<tr><td class="label">Телефон для связи:<td><input type="text" />'.
		'<tr><td class="label">Рубрика:'.
			'<td class="title">'.
				'<input type="hidden" id="rubric_id" />'.
				'<input type="hidden" id="rubric_id_sub" />'.
		'<tr><td class="label top">Текст:'.
			'<td class="title">'.
				'<textarea id="txt" autofocus></textarea>'.
				'<div id="ob-calc"></div>'.
		'<tr><td class="label">Дополнительно:'.
			'<td class="title">'.
				_check('ramka', 'Обвести в рамку <em>(+'.round($dop[1]['cena'] * CENA_KOEF).' руб.)</em>').
				_check('black', 'Чёрный фон <em>(+'.round($dop[2]['cena'] * CENA_KOEF).' руб.)</em>').
		'<tr><td class="label">Телефон:<td class="title"><input type="text" id="telefon" placeholder="8 900 123 45 67" />'.
		'<tr><td class="label top">Номера газеты:<td class="title">'.gazetaNomer().
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
						'<span>Номер <b>'.$r['week_nomer'].'</b></span>'.
						'<span class="gn">('.$r['general_nomer'].')</span>.'.
						'<span class="print">День выхода: '.FullData($r['day_public'], 1).'</span>'.
						'<span class="cena"></span>'.
			'</table>';
	}
	return '<div id="gazeta-nomer">'.$spisok.'</div>';
}

function forma_sended() {//сообщение об успешном размещении объявления
	$paid = '<div id="pay-wait">Идёт проверка платежа...</div>';
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
				$paid = '<div id="pay-success">Платёж на сумму '.round($mo['order.from_amount']).' руб. зачислен.</div>';
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
}

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
				'Если объявление размещается сразу в 3 номера, то четвертый номер бесплатно.'.
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
		'<div id="tab-head">Условия размещения</div>'.
		'<div class="menu-info">'.
			'<p>Размещение объявлений в газете КупецЪ является платной услугой.'.
			'<p>Вычисление стоимости объявления производится по следующей схеме:'.
			'<table>'.
				'<tr><td>Первые '.$g['TXT_LEN_FIRST'].' символов:'.
					'<td class="r">'.round($g['TXT_CENA_FIRST'] * CENA_KOEF).' руб.'.
				'<tr><td>Каждые следующие '.$g['TXT_LEN_NEXT'].' символов:'.
					'<td class="r">'.round($g['TXT_CENA_NEXT'] * CENA_KOEF).' руб.'.
				'<tr><td>Обвести в рамку:'.
					'<td class="r">'.round($dop[1]['cena'] * CENA_KOEF).' руб.'.
				'<tr><td>Выделить на чёрном фоне:'.
					'<td class="r">'.round($dop[2]['cena'] * CENA_KOEF).' руб.'.
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
}

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
}



