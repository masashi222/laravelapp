
//シフト一覧画面へ(nav)
function toshift(){
	$(document).on('click','.shift-nav',function(){
		window.location.href="/staff/shift-record";
	});
}

//勤怠打刻画面へ(nav)
function toStamp(){
	$(document).on('click','.stamp-nav',function(){
		window.location.href="/staff/stamp";
	});
}

//戻るボタン
function back(){
	$(document).on('click','.back',function(){
		window.location.href="/staff/attendance-record";
	});
}

//勤怠一覧表示期間変更(先月)
function toLast(){
	$(document).on('click','.record-nav',function(){
		window.location.href="/staff/attendance-record-last";
	});
}
//勤怠一覧表示期間変更(来月)
function toNext(){
	$(document).on('click','.record-nav',function(){
		window.location.href="/staff/attendance-record-next";
	});
}

//ログイン画面へ遷移
function toLogin(){
		if(window.confirm('退出しますか？')){
			window.location.href="/logout";
		}else{
			window.alert('キャンセルされました');
		}
}