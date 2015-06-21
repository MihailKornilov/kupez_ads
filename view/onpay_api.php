<?php
function toFailSave() {
	global $request, $txt;

	$save =
		'request:'.NR.$request.
		NR.NR.
		implode(NR, $txt).
		NR.NR.
		'ob_get_contents: '.NR.ob_get_contents();

	$fp = fopen(DOCUMENT_ROOT.'/view/save.txt', 'w+');
	fwrite($fp, $save);
	fclose($fp);
}

set_time_limit(1000);
ob_start();
register_shutdown_function('toFailSave');


require_once('../config.php');

define('SECRET_KEY', 't1S06KorEyX');
define('NR', "\n\r");


$request = file_get_contents('php://input');
$r = (array)json_decode($request);

if(!$pay_for = _num($r['pay_for']))
	die('01');

$txt = array();
$txt[] = 'type: '.$r['type'];
$txt[] = 'pay_for: '.$pay_for;

// формирование ответа-false
$sig = sha1($r['type'].';false;'.$pay_for.';'.SECRET_KEY);
define('STATUS_FALSE', '{"status":false,"pay_for":"'.$pay_for.'","signature":"'.$sig.'"}');

// формирование ответа-true
$sig = sha1($r['type'].';true;'.$pay_for.';'.SECRET_KEY);
define('STATUS_TRUE', '{"status":true,"pay_for":"'.$pay_for.'","signature":"'.$sig.'"}');

$txt[] = 'проверка наличия заявки...';
$sql = "SELECT * FROM `gazeta_zayav` WHERE `id`=".$pay_for;
$txt[] = $sql;
if(!$z = query_assoc($sql))
	die(STATUS_FALSE);
$txt[] = '...ok';

$txt[] = 'проверка наличия платежа...';
$sql = "SELECT COUNT(*) FROM `gazeta_money` WHERE `zayav_id`=".$pay_for." AND `viewer_id_add`=".VIEWER_ONPAY;
$txt[] = $sql;
if(query_value($sql))
	die(STATUS_FALSE);
$txt[] = '...ok';

switch($r['type']) {
	case 'check': break;
	case 'pay':
		$payment = (array)$r['payment'];
		$txt[] = 'amount: '.$payment['amount'];

		$txt[] = 'внесение платежа...';
		$sql = "INSERT INTO `gazeta_money` (
				`zayav_id`,
				`invoice_id`,
				`sum`,
				`viewer_id_add`
			) VALUES (
				".$pay_for.",
				9,
				".$payment['amount'].",
				".VIEWER_ONPAY."
			)";
		$txt[] = $sql;
		query($sql);
		$insert_id = mysql_insert_id();
		$txt[] = $insert_id.' = insert_id';

		$txt[] = 'внесение истории о платеже...';
		invoice_history_insert(array(
			'action' => 1,
			'table' => 'gazeta_money',
			'id' => $insert_id
		));
		$txt[] = '...ok';

		$txt[] = 'внесение информации о платеже...';
		$user = (array)$r['user'];
		$balance = (array)$r['balance'];
		$order = (array)$r['order'];
		$sql = "INSERT INTO `onpay_money_info` (
				`zayav_id`,
				`money_id`,

				`user.email`,
				`user.phone`,
				`user.note`,

				`payment.id`,
				`payment.date_time`,
				`payment.amount`,
				`payment.way`,
				`payment.rate`,

				`balance.amount`,
				`balance.way`,

				`order.from_amount`,
				`order.from_way`,
				`order.to_amount`,
				`order.to_way`
			) VALUES (
				".$pay_for.",
				".$insert_id.",

				'".addslashes($user['email'])."',
				'".addslashes($user['phone'])."',
				'".addslashes($user['note'])."',

				".$payment['id'].",
				'".substr($payment['date_time'], 0, 19)."',
				".$payment['amount'].",
				'".$payment['way']."',
				".$payment['rate'].",

				".$balance['amount'].",
				'".$balance['way']."',

				".$order['from_amount'].",
				'".$order['from_way']."',
				".$order['to_amount'].",
				'".$order['to_way']."'
			)";
		$txt[] = $sql;
		query($sql);
		$txt[] = '...ok';
		break;
	default:
		die('02');
}


die(STATUS_TRUE);


/*

{
	"type":"pay",
	"signature":"a63be002d218ac618a73dd0513c08dfc83d8469c",
	"pay_for":"19912",
	"user":{
		"email":"kupez29ads@mail.ru",
		"phone":null,
		"note":""
	},
	"payment":{
		"id":13516650,
		"date_time":"2015-06-08 15:13:40 +0300",
		"amount":100.0,
		"way":"TST",
		"rate":1.0,
		"release_at":"null"
	},
	"balance":{
		"amount":100.0,
		"way":"TST"
	},
	"order":{
		"from_amount":100.0,
		"from_way":"TST",
		"to_amount":100.0,
		"to_way":"TST"
	}
}

*/