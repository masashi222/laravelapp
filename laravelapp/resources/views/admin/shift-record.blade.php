@extends('layouts.base')

@section('title','シフト一覧')

@section('link')
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/shift.css')}}">
<link rel="stylesheet" href="{{asset('/css/shift-print.css')}}">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('display_title')
		<div class="text-center shift_print">シフト一覧</div>
        <div class="text-center"><span class="btn shift_print" onclick="prevCalendar();">&laquo;</span> &emsp;<span class="shift_print" id="period"></span> &emsp;<span class="btn shift_print" onclick="nextCalendar();">&raquo;</span></div>
        @endslot
		@slot('logout')
		<div class="m-3 float-right shift_print">
			<span id="logout" onclick="toLogin();"><i class="fas fa-sign-out-alt shift_print">&nbsp;退出</i></span>
		</div>
		@endslot
    @endcomponent
    <div class="row">
        <div class="col">
            <table class="table" id="calendar">
                <!--shift.js-->
            </table>
            <!-- シフト詳細ダイアログ -->
            <div class="modal fade" id="dialog1">
            	<div class="modal-dialog" role="document">
            		<div class="modal-content">
            			<div class="modal-header">
            				<h5 class="modal-title"><!-- sihif.js --></h5>
            				<button type="button" class="btn" data-dismiss="modal" onclick="">
            					<span>&times;</span>
            				</button>
            			</div>
            			<div class="modal-body" id="modal-body" data-date="">
            			</div>
            		</div>
            	</div>
            </div>
        </div>
    </div>
    <div class="row no_print">
    	<div class="col">
			<button type="button" class="btn btn-warning create-btn shift_print" onclick="toshiftcreateAdmin();">＋作成</button>
    	</div>
    </div>
	@component('components.navbar-admin')
	@slot('nav')
	<a class="nav-item nav-link active bg-info shift_print">シフト</a>
    <a class="nav-item nav-link bg-light attendance-nav shift_print" onclick="toattendanceAdmin();">勤怠</a>
    <a class="nav-item nav-link bg-light staff-nav shift_print" onclick="toStaff();">従業員</a>
    <a class="nav-item nav-link bg-light stamp-nav shift_print" onclick="toStamppass();">打刻</a>
    @endslot
	@endcomponent
	@php
	$data = $data;
	@endphp
</div>
@endsection
@section('script')
<script>
const weeks = ['日', '月', '火', '水', '木', '金', '土'];
const date = new Date();
let year = date.getFullYear();
let month = date.getMonth() + 1;

function showCalendar(year, month){
        const calendarHtml = createCalendar(year,month);
        document.getElementById('calendar').innerHTML = calendarHtml;
}

function createCalendar(year,month){
    const startDate = new Date(year,month-1,1);//月の最初の日の取得
    const endDate = new Date(year,month,0);//月の最後の日の取得
    const endDayCount = endDate.getDate();//月の日数
    const lastMonthEndDate = new Date(year,month-1,0);//前月の最後の日を取得
    const lastMonthendDayCount = lastMonthEndDate.getDate();//前月の日数
    const startDay = startDate.getDay();//月の最初の日の曜日の獲得
    let dayCount = 1;//日にちのカウント
    let calendarHtml = '';//HTMLを組み立てる変数

    let periodHtml = year + '/' + month ;
    document.getElementById('period').textContent = periodHtml;

    calendarHtml += '<thead>' + '<tr>';

    //曜日の行の作成
    for(let i=0;i<weeks.length;i++){
        calendarHtml += '<th>' + weeks[i] + '</th>';
        }

    calendarHtml += '</tr>' + '</thead>' + '<tbody>';

    for(let w=0;w<6;w++){
        calendarHtml += '<tr>'

        for(let d=0;d<7;d++){
         if (w == 0 && d < startDay) {
                // 1行目で1日の曜日の前
                let num = lastMonthendDayCount - startDay + d + 1;
                calendarHtml += '<td class="text-light border">' + num + '</td>';
            } else if (dayCount > endDayCount) {
                // 末尾の日数を超えた
                let num = dayCount - endDayCount;
                calendarHtml += '<td class="text-light border">' + num + '</td>';
                dayCount++;
            } else {
            	let mm = ('0' + month).slice(-2);
                let dd = ('0' + dayCount).slice(-2);
                let yymmdd = year + '-' + mm + '-' + dd;
                calendarHtml += '<td class="border" onclick="showDialog();" data-id=\'{"year":' +year+ ',"month":' +month+ ',"date":' +dayCount+ '}\'>'
                + dayCount + '<ul class="px-0" style="font-size:0.75em; color:black; list-style:none;" data-date="'+ yymmdd +'"></ul></td>';
                dayCount++;
            }
        }
        calendarHtml += '</tr>'
    }
    calendarHtml += '</tbody>';

    return calendarHtml;
}

function prevCalendar() {
    document.getElementById('calendar').innerHTML = '';

        month--;

        if (month < 1) {
            year--;
            month = 12;
        }
        showCalendar(year, month);

      //カレンダーにシフトの表示
        var data = @json($data);

        if( data !== null){
        	var shift_createid = [];
        	var memberid = [];
            var shift_name = [];
            var display_name = [];
           	var date_shift = [];
           	var go_time = [];
           	var out_time = [];
           	var display_out = [];
           	for(let i =0; i<data.length; i++){
           	   	shift_createid[i] = data[i][0];
           	   	memberid[i] = data[i][1];
           	   	shift_name[i] = data[i][2];
           	   	display_name[i] = data[i][2].slice(0,1);
           	   	date_shift[i] = data[i][3];
           	   	go_time[i] = data[i][4];
           	   	out_time[i] = data[i][5];
           	   	if( data[i][5] == '05:00' ){
           	   	   	display_out[i] = 'LAST';
           	   	}else{
           	   	   	display_out[i] = data[i][5];
           	   	}
        	}

           	$(document).ready(function(){
           		var w = $(window).width();
           	    var x = 500;
           	    if (w <= x) {
           	    	let memberHTML = [];
           	    	for(let i=0; i<date_shift.length; i++){
           	        		memberHTML[i] = '<li class="my-0">'+ shift_name[i].slice(0,2) +'</li>';
           	    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
           	    	}
           	    }else{
           	    	let memberHTML = [];
           	    	for(let i=0; i<date_shift.length; i++){
           	        		memberHTML[i] = '<li class="my-0">'+ display_name[i] + '&nbsp;' + go_time[i] + '~' + display_out[i] +'</li>';
           	    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
           	    	}
           	    }
           	});
        }

}

function nextCalendar() {
    document.getElementById('calendar').innerHTML = '';

        month++;

        if (month > 12) {
            year++;
            month = 1;
        }
        showCalendar(year, month);

      //カレンダーにシフトの表示
        var data = @json($data);

        if( data !== null){
        	var shift_createid = [];
        	var memberid = [];
            var shift_name = [];
            var display_name = [];
           	var date_shift = [];
           	var go_time = [];
           	var out_time = [];
           	var display_out = [];
           	for(let i =0; i<data.length; i++){
           	   	shift_createid[i] = data[i][0];
           	   	memberid[i] = data[i][1];
           	   	shift_name[i] = data[i][2];
           	   	display_name[i] = data[i][2].slice(0,1);
           	   	date_shift[i] = data[i][3];
           	   	go_time[i] = data[i][4];
           	   	out_time[i] = data[i][5];
           	   	if( data[i][5] == '05:00' ){
           	   	   	display_out[i] = 'LAST';
           	   	}else{
           	   	   	display_out[i] = data[i][5];
           	   	}
        	}

           	$(document).ready(function(){
           		var w = $(window).width();
           	    var x = 500;
           	    if (w <= x) {
           	    	let memberHTML = [];
           	    	for(let i=0; i<date_shift.length; i++){
           	        		memberHTML[i] = '<li class="my-0">'+ shift_name[i].slice(0,2) +'</li>';
           	    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
           	    	}
           	    }else{
           	    	let memberHTML = [];
           	    	for(let i=0; i<date_shift.length; i++){
           	        		memberHTML[i] = '<li class="my-0">'+ display_name[i] + '&nbsp;' + go_time[i] + '~' + display_out[i] +'</li>';
           	    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
           	    	}
           	    }
           	});
        }

}

showCalendar(year, month);

//勤怠一覧画面へ(nav)
function toattendanceAdmin(){
	$(document).on('click','.attendance-nav',function(){
		window.location.href="/admin/attendance-staff-record";
	});
}

//スタッフ一覧画面へ(nav)
function toStaff(){
	$(document).on('click','.staff-nav',function(){
		window.location.href="/admin/staff-record";
	});
}

//勤怠打刻画面へ（nav）
function toStamppass(){
	$(document).on('click','.stamp-nav',function(){
		window.location.href="/admin/stamp-pass";
	});
}

//シフト作成画面へ(btn)
function toshiftCreate(){
	$(document).on('click','.create-btn',function(){
		window.location.href="/staff/shift-create";
	});
}
function toshiftcreateAdmin(){
	$(document).on('click','.create-btn',function(){
		window.location.href="/admin/shift-create";
	});
}

//カレンダーにシフトの表示
var data = @json($data);

if( data !== null){
	var shift_createid = [];
	var memberid = [];
    var shift_name = [];
    var display_name = [];
   	var date_shift = [];
   	var go_time = [];
   	var out_time = [];
   	var display_out = [];
   	for(let i =0; i<data.length; i++){
   	   	shift_createid[i] = data[i][0];
   	   	memberid[i] = data[i][1];
   	   	shift_name[i] = data[i][2];
   	   	display_name[i] = data[i][2].slice(0,1);
   	   	date_shift[i] = data[i][3];
   	   	go_time[i] = data[i][4];
   	   	out_time[i] = data[i][5];
   	   	if( data[i][5] == '05:00' ){
   	   	   	display_out[i] = 'LAST';
   	   	}else{
   	   	   	display_out[i] = data[i][5];
   	   	}
	}

   	$(document).ready(function(){
   		var w = $(window).width();
   	    var x = 500;
   	    if (w <= x) {
   	    	let memberHTML = [];
   	    	for(let i=0; i<date_shift.length; i++){
   	        		memberHTML[i] = '<li class="my-0">'+ shift_name[i].slice(0,2) +'</li>';
   	    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
   	    	}
   	    }else{
   	    	let memberHTML = [];
   	    	for(let i=0; i<date_shift.length; i++){
   	        		memberHTML[i] = '<li class="my-0">'+ display_name[i] + '&nbsp;' + go_time[i] + '~' + display_out[i] +'</li>';
   	    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
   	    	}
   	    }
   	});
}

//シフト一覧詳細ダイアログ
function showDialog(){
	$(document).on('click','td',function(){
		//モーダルダイアログ
		$('#dialog1').modal();
		//シフト詳細の日付
		$('.modal-title').text('');
		var dataYear = $(this).data('id').year;
		var dataMonth = $(this).data('id').month;
		var dataDate = $(this).data('id').date;
		var dateFull = new Date(dataYear,dataMonth-1,dataDate);
		var dataDay = weeks[dateFull.getDay()];
		var dateText = dataMonth+'/'+dataDate+'('+dataDay+')';
		var dataMonth_edit = ('0' + dataMonth).slice(-2);
		var dataDate_edit = ('0' + dataDate).slice(-2);
		var dateInfo = dataYear+'-'+dataMonth_edit+'-'+dataDate_edit;
		$('.modal-title').text(dateText);
		document.getElementById("modal-body").setAttribute("data-date",dateInfo);
		var date_info = document.getElementById("modal-body").getAttribute("data-date");
		if( data !== null ){
			//子要素の削除
			const parent = document.getElementById('modal-body');
			while( parent.firstChild ){
				parent.removeChild( parent.firstChild );
			}
			//シフト確定のモダール内の初期値設定
			let memberHTML = [];
			for(let i=0; i<date_shift.length; i++){
				if( date_info == date_shift[i] ){
					memberHTML[i] = '<br><p>' + (shift_name[i]+'　　').slice(0,3) + '&nbsp;' + go_time[i] + '~' + display_out[i] + '</p><br>';
					$(`#modal-body[data-date=${date_shift[i]}]`).append(memberHTML[i]);
				}
			}
		}
	});
}

//シフト修正画面へ
function toshiftFix(){
	$(document).on('click','.fix-btn',function(){
		window.location.href="/staff/shift-fix";
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
</script>
@endsection