<?php
require_once('config.php');

switch(@$_POST['op']) {
	case 'ob_save':
		if(!$rubric_id = _num($_POST['rubric_id']))
			jsonError();
		if(empty($_POST['gn']))
			jsonError();

		$rubric_sub_id = _num($_POST['rubric_sub_id']);
		$txt = _txt($_POST['txt']);
		$txt = preg_replace('/[ ]+/', ' ', $txt);
		$telefon = _txt($_POST['telefon']);
		$dop_id = _num($_POST['dop_id']);
		$ob_cena = _num($_POST['ob_cena']);

		$summa = 0;
		$gn_values = array();

		$gn = explode(',', $_POST['gn']);
		foreach($gn as $i => $r) {
			if(!_num($r))
				jsonError();
			if($i != 3)
				$summa += $ob_cena;
		}

		$sql = "INSERT INTO `gazeta_zayav` (
				    `category`,

				    `rubric_id`,
				    `rubric_sub_id`,
				    `txt`,
				    `telefon`,

				    `summa`,
				    `gn_count`,
				    `viewer_id_add`
				) VALUES (
				    1,

				    ".$rubric_id.",
				    ".$rubric_sub_id.",
				    '".addslashes($txt)."',
				    '".addslashes($telefon)."',

				    ".$summa.",
				    ".count($gn).",
				    ".VIEWER_ONPAY."
				)";
		query($sql);
		$send['id'] = mysql_insert_id();

		foreach($gn as $i => $r) {
			$gn_values[] = '('.
				$send['id'].','.
				$r.','.
				$dop_id.','.
				($i != 3 ? $ob_cena : 0).
			')';
		}
		$sql = "INSERT INTO `gazeta_nomer_pub` (
					`zayav_id`,
					`general_nomer`,
					`dop`,
					`cena`
			   ) VALUES ".implode(',', $gn_values);
		query($sql);

		_historyInsert(
			11,
			array(
				'zayav_id' => $send['id'],
				'viewer_id' => VIEWER_ONPAY
			),
			'gazeta_history'
		);

		jsonSuccess($send);
		break;
}
jsonError();
