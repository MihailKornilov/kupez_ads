<?php
require_once('config.php');

switch(@$_POST['op']) {
	case 'ob_save':
		if(!$rubric_id = _num($_POST['rubric_id']))
			jsonError();
		if(empty($_POST['gn']))
			jsonError();

		$rubric_id_sub = _num($_POST['rubric_id_sub']);
		$txt = _txt($_POST['txt']);
		$txt = preg_replace('/[ ]+/', ' ', $txt);
		$telefon = _txt($_POST['telefon']);
		$dop_id = _num($_POST['dop_id']);
		$ob_cena = _num($_POST['ob_cena']);

		$gn_values = array();

		$gn = explode(',', $_POST['gn']);
		foreach($gn as $i => $r)
			if(!_num($r))
				jsonError();

		$sql = "INSERT INTO `_zayav` (
				    `app_id`,
				    `service_id`,
				    `nomer`,

				    `rubric_id`,
				    `rubric_id_sub`,
				    `about`,
				    `phone`,

				    `onpay_checked`,
				    `count`,
				    `viewer_id_add`
				) VALUES (
				    ".APP_ID.",
				    8,
					"._maxSql('_zayav', 'nomer', 1).",

				    ".$rubric_id.",
				    ".$rubric_id_sub.",
				    '".addslashes($txt)."',
				    '".addslashes($telefon)."',

					2,
				    ".count($gn).",
				    ".VIEWER_ONPAY."
				)";
		query($sql);

		$send['id'] = query_insert_id('_zayav');

		$sql = "UPDATE `_zayav`
				SET `name`=CONCAT('Интернет-объявление ',`nomer`)
				WHERE `id`=".$send['id'];
		query($sql);

		foreach($gn as $i => $r) {
			$gn_values[] = '('.
				APP_ID.','.
				$send['id'].','.
				$r.','.
				$dop_id.','.
				($i != 3 ? $ob_cena : 0).
			')';
		}
		$sql = "INSERT INTO `_zayav_gazeta_nomer` (
					`app_id`,
					`zayav_id`,
					`gazeta_nomer_id`,
					`dop`,
					`cena`
			   ) VALUES ".implode(',', $gn_values);
		query($sql);

		_zayavBalansUpdate($send['id']);

		_history(array(
			'type_id' => 73,
			'zayav_id' => $send['id'],
			'viewer_id' => VIEWER_ONPAY
		));

		jsonSuccess($send);
		break;
}
jsonError();
