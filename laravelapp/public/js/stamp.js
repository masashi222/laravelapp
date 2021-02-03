function time(){
	const weeks = ['日','月','火','水','木','金','土'];
	const date = new Date();
	const year = date.getFullYear();
	const month = date.getMonth()+1;
	const onlyDate = date.getDate();
	const day = weeks[date.getDay()];
	const hour = date.getHours();
	const minute = date.getMinutes();
	const second = date.getSeconds();

	const fullDate = year+'年'+month+'月'+onlyDate+'日'+'('+day+')';
	const time = hour+'時'+minute+'分'+second+'秒';

	document.getElementById('stamp-date').textContent = fullDate;
	document.getElementById('stamp-time').textContent = time;
};

setInterval('time()',1000);

//time();

//シフト一覧画面へ(nav)
function toshift(){
	$(document).on('click','.shift-nav',function(){
		window.location.href="/staff/shift-record";
	});
}

//勤怠一覧画面へ(nav)
function toAttendance(){
	$(document).on('click','.attendance-nav',function(){
		window.location.href="/staff/attendance-record";
	});
}

//現在時刻（フォーム送信用）
function createTime(){
	const weeks = ['日','月','火','水','木','金','土'];
	const date = new Date();
	const year = date.getFullYear();
	const month = ('0' + (date.getMonth()+1)).slice(-2);
	const onlyDate = ('0' + date.getDate()).slice(-2);
	const day = weeks[date.getDay()];
	const hour = ('0' + date.getHours()).slice(-2);
	const minute = ('0' + date.getMinutes()).slice(-2);
	const second = ('0' + date.getSeconds()).slice(-2);

	const fullDate = year + '-' + month + '-' + onlyDate + '\t' + hour + ':' + minute + ':' + second;

	return fullDate;
};

function getTime(){
	$(document).on('click','#go_btn',function(){
		var time = createTime();
		document.getElementById("go_time").setAttribute("value",time);
		document.getElementById("go_flg").setAttribute("value",1);
	});
	$(document).on('click','#breakIn_btn',function(){
		var time = createTime();
		document.getElementById("break_in").setAttribute("value",time);
		document.getElementById("in_flg").setAttribute("value",1);
		document.getElementById("out_flg").setAttribute("value",0);
	});
	$(document).on('click','#breakOut_btn',function(){
		var time = createTime();
		document.getElementById("break_out").setAttribute("value",time);
		document.getElementById("fin_flg").setAttribute("value",1);
	});
	$(document).on('click','#out_btn',function(){
		var time = createTime();
		document.getElementById("out_time").setAttribute("value",time);
		document.getElementById("out_flg").setAttribute("value",1);
		document.getElementById("in_flg").setAttribute("value",0);
	});
}

function check(){
	if(window.confirm('実行してよろしいですか？')){ // 確認ダイアログを表示
		return true; // 「OK」時は送信を実行
	}
	else{ // 「キャンセル」時の処理
		window.alert('キャンセルされました'); // 警告ダイアログを表示
		return false; // 送信を中止
	}
}