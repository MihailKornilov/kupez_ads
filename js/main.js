var AJAX_MAIN = URL + '/ajax/main.php',
	SEND_GN = '',//������ ����� ��� �����
	OB_CENA = 0, //��������� ���������� ��� �������������� ����������
	OB_SUM = 0,  //������������� ��������� ����������
	OB_DOP = 0,  //����� �� �������������� ��������
	_cookie = function(name, value) {
		if(value !== undefined) {
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + 1);
			document.cookie = name + '=' + value + '; path=/; expires=' + exdate.toGMTString();
			return '';
		}
		var r = document.cookie.split('; ');
		for(var i = 0; i < r.length; i++) {
			var k = r[i].split('=');
			if(k[0] == name)
				return k[1];
		}
		return '';

	},
	obSumCalc = function() {// ���������� ��������� ����������
		var txt_sum = 0, // ����� ������ �� �����
			podr_about = '', // ��������� ������������ ����� ����������
			txt = $('#txt').val()
				.replace(/\./g, '')    // �����
				.replace(/,/g, '')     // �������
				.replace(/\//g, '')    // ���� /
				.replace(/\"/g, '')    // ������� �������
				.replace(/( +)/g, ' ') // ������ �������
				.replace( /^\s+/g, '') // ������� � ������
				.replace( /\s+$/g, '');// ������� � �����
		if(!txt.length)
			$('#ob-calc').html('');
		else {
			txt_sum += Math.round(TXT_CENA_FIRST * CENA_KOEF);
			if(txt.length > TXT_LEN_FIRST) {
				podr_about = ' = ';
				var CEIL = Math.ceil((txt.length - TXT_LEN_FIRST) / TXT_LEN_NEXT);
				podr_about += TXT_LEN_FIRST;
				var LAST = txt.length - TXT_LEN_FIRST - (CEIL - 1) * TXT_LEN_NEXT;
				txt_sum += CEIL * Math.round(TXT_CENA_NEXT * CENA_KOEF);
				if(TXT_LEN_NEXT == LAST) CEIL++;
				if(CEIL > 1) podr_about += ' + ' + TXT_LEN_NEXT;
				if(CEIL > 2) podr_about += 'x' + (CEIL - 1);
				if(TXT_LEN_NEXT > LAST) podr_about += ' + ' + LAST;
			}
			var html = '�����: <b>' + txt.length + '</b>' + podr_about + '<br />' +
				'����: <b>' + txt_sum + '</b> ���. <span>(��� ����� ���. ����������)</span>';
			$('#ob-calc').html(html);
		}
		OB_CENA = txt_sum;
		submitTest();
	},
	nomerCenaPrint = function() {
		var nomer = $('#gazeta-nomer .nomer'),
			k = 0;//���������� ��������� �������
		OB_SUM = 0;
		for(var n = 0; n < nomer.length; n++) {
			var sp = nomer.eq(n),
				dis = sp.hasClass('dis');
			if(!dis)
				k++;
			var cena = k == 4 ? 0 : OB_CENA + Math.round(OB_DOP * CENA_KOEF);
			sp.find('.cena').html(OB_CENA ? '<b>' + cena + '</b> ���.' : '');
			if(!dis)
				OB_SUM += cena;
		}
	},
	obValues = function() {
		var dop_id = 0;
		if(_num($('#ramka').val()))
			dop_id = 1;
		else if(_num($('#black').val()))
			dop_id = 2;
		return {
			op:'ob_save',
			rubric_id:_num($('#rubric_id').val()),
			rubric_id_sub:_num($('#rubric_id_sub').val()),
			txt:$.trim($('#txt').val()),
			telefon:$.trim($('#telefon').val()),
			gn:SEND_GN,
			ob_cena:OB_CENA + OB_DOP,
			dop_id:dop_id
		};
	},
	submitTest = function() {
		nomerCenaPrint();
		var s = $('#submit'),
			span = s.find('span'),
			v = obValues();
		s.removeClass('send');
		if(!v.rubric_id)
			span.html('�� ������� �������');
		else if(!v.txt)
			span.html('�� ������ �����');
		else if(!v.telefon)
			span.html('�� ������ ����� ��������');
		else if(!v.gn)
			span.html('�� ������� ������ ������');
		else {
			s.addClass('send');
			span.html('���������� ���������� �� ' + OB_SUM + ' ���.');
		}
	},
	checkPut = function(v, attr_id) {
		var dop_id = 0;
		OB_DOP = 0;
		if(attr_id == 'ramka') {
			$('#black')._check(0);
			dop_id = 1;
		}
		if(attr_id == 'black') {
			$('#ramka')._check(0);
			dop_id = 2;
		}
		if(v)
			OB_DOP = GAZETA_OBDOP_CENA[dop_id];

		submitTest();
	};

$(document)
	.on('click', '#submit.send', function() {
		var t = $(this);
		if(t.hasClass('sending'))
			return;
		t.addClass('sending');
		$.post(AJAX_MAIN, obValues(), function(res) {
			if(res.success) {
				t.remove();
				$('#ad-form-sended #msg b').html(res.id);
				$('#ad-form').hide();
				$('#ad-form-sended').show();
				var u = 'https://secure.onpay.ru/pay/nyandoma_kupez?' +
						'f=7' +
						'&pay_mode=fix' +            //����� "����� �� ������������� �����"
						'&price=' + OB_SUM +         //��������� ����� ��� �������
					//	'&currency=RUR' +            //� ������
						'&pay_for=' + res.id +       //� ������
						'&price_final=true' +        //���� �� ����� ���� �������� ��������
						'&user_email=kupez29ads@mail.ru';
				//window.open(u);
				_cookie('ad_posted_id', res.id);
				window.location.href = u;
			}
		}, 'json');
	})
	.ready(function() {
		$('#menu td').click(function() {
			var t = $(this),
				v = t.attr('val');
			$('#menu td').removeClass('s');
			t.addClass('s');
			$('.menu').hide();
			$('#menu' + v).show();
			$('#tr-submit')[v == 1 ? 'show' : 'hide']();
		});

		$('#rubric_id')._rubric({
			w_rub:170,
			w_sub:250,
			func:function() {
				$('#txt').focus();
				submitTest();
			}
		});


		$('#txt')
			.autosize()
			.keyup(obSumCalc);

		$('#telefon').keyup(submitTest);

		$('#gazeta-nomer .td-head').click(function() {
			var t = $(this).parent().parent().parent(),
				v = t.hasClass('dis');
			t[(v ? 'remove' : 'add') + 'Class']('dis');
			var nomer = $('#gazeta-nomer .nomer'),
				arr = [];
			for(var n = 0; n < nomer.length; n++) {
				var sp = nomer.eq(n);
				if(sp.hasClass('dis'))
					continue;
				arr.push(sp.attr('val'));
			}
			SEND_GN = arr.join();
			submitTest();
		});

		$('#ramka')._check(checkPut);
		$('#black')._check(checkPut);

		submitTest();
	});
