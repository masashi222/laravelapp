@extends('layouts.base')

@section('title','シフト作成')

@section('link')
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/shift.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
    	@slot('back_btn')
    	<div class="m-3 back" onclick="back();">&lt;&nbsp;戻る</div>
    	@endslot
		@slot('display_title')
		<div class="text-center">シフト作成</div>
        <div class="text-center"><span class="btn" onclick="prevCalendar();">&laquo;</span> &emsp;<span id="period"></span> &emsp;<span class="btn" onclick="nextCalendar();">&raquo;</span></div>
        @endslot

    @endcomponent
	<div class="row">
        <div class="col">
            <table class="table" id="calendar">
                <!--script-->
            </table>
            <div class="modal fade" id="dialog1">
            	<div class="modal-dialog" role="document">
            		<div class="modal-content">
            			<div class="modal-header">
            				<h5 class="modal-title"><!-- create.js --></h5>
            				<button type="button" class="btn" data-dismiss="modal" onclick="">
	                			<span>&times;</span>
	                		</button>
            			</div>
            			<form method="post" action="{{ url('/staff/shift-create') }}">
            			{{ csrf_field() }}
                			<div class="modal-body" id="modal-body" data-date="">
                				<input type="hidden" id="date-info" name="date-info" value="">
                				<input type="hidden" id="memberid" name="memberid" value="{{ $memberid }}">
                				<input type="hidden" id="shift_createid" name="shift_createid" value="">
                				<div class="input-group align-items-center">
                					<div class="mx-2">出勤</div>
                					<select id="go_time" name="go_time" class="form-control">
                						<option id="12:00:00" value="12:00:00">12:00</option><option id="12:30:00" value="12:30:00">12:30</option>
                						<option id="13:00:00" value="13:00:00">13:00</option><option id="13:30:00" value="13:30:00">13:30</option>
                						<option id="14:00:00" value="14:00:00">14:00</option><option id="14:30:00" value="14:30:00">14:30</option>
                						<option id="15:00:00" value="15:00:00">15:00</option><option id="15:30:00" value="15:30:00">15:30</option>
                						<option id="16:00:00" value="16:00:00">16:00</option><option id="16:30:00" value="16:30:00">16:30</option>
                						<option id="17:00:00" value="17:00:00">17:00</option><option id="17:30:00" value="17:30:00">17:30</option>
                						<option id="18:00:00" value="18:00:00">18:00</option><option id="18:30:00" value="18:30:00">18:30</option>
                						<option id="19:00:00" value="19:00:00">19:00</option><option id="19:30:00" value="19:30:00">19:30</option>
                						<option id="20:00:00" value="20:00:00">20:00</option>
                					</select>
                				</div>
                				<br>
                				<div class="input-group  align-items-center">
                					<div class="mx-2">退勤</div>
                					<select id="out_time" name="out_time" class="form-control">
                    					<option id="20:30:00" value="20:30:00">20:30</option><option id="21:00:00" value="21:00:00">21:00</option>
                    					<option id="21:30:00" value="21:30:00">21:30</option><option id="22:00:00" value="22:00:00">22:00</option>
                    					<option id="22:30:00" value="22:30:00">22:30</option><option id="23:00:00" value="23:00:00">23:00</option>
                    					<option id="23:30:00" value="23:30:00">23:30</option><option id="00:00:00" value="00:00:00">00:00</option>
                    					<option id="00:30:00" value="00:30:00">00:30</option><option id="01:00:00" value="01:00:00">01:00</option>
                    					<option id="01:30:00" value="01:30:00">01:30</option><option id="02:00:00" value="02:00:00">02:00</option>
                    					<option id="05:00:00" value="05:00:00">LAST</option>
                					</select>
                				</div>
                			</div>
                			<div class="modal-footer">
               					<div class="form-group">
	                				<input type="submit" id="create-btn" name="create-btn" class="btn btn-warning create-btn" value="">
               					</div>
                			</div>
            			</form>
            		</div>
            	</div>
            </div>
        </div>
    </div>
    <div class="row">
    	<div class="col">
    		<form method="post" action="{{ url('/staff/shift-create') }}">
    		{{ csrf_field() }}
    			<div class="form-row">
    				<div class="col">
    					<div class="form-grop">
							<input type="submit" id="post-btn" name="post-btn" class="btn btn-warning float-right" value="">
						</div>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
    @php
    $data = $data;
    $info = $info;
    @endphp
</div>
@endsection
@section('script')
<script>

const weeks = ['日', '月', '火', '水', '木', '金', '土'];
const date = new Date();
let year = date.getFullYear();
let month = date.getMonth() + 2;
if (month > 12) {
    year++;
    month = 1;
}

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
                calendarHtml += '<td class="border" onclick="showDialog();" data-id=\'{"year":' +year+ ',"month":'
                +month+ ',"date":' +dayCount+ '}\'>'
                + dayCount + '<ul class="px-0" style="font-size:0.75em; color:black; list-style:none;" data-date="' + yymmdd + '"></ul>' + '</td>';
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

      //カレンダー内に登録の表示
        var data = @json($data);

        if( data !== null ){
        	var date_shift = [];
        	var go_time = [];
        	var out_time = [];
        	var shift_createid = [];
        	for(let i = 0; i<data.length; i++){
        		date_shift[i] = data[i][0];
        		go_time[i] = data[i][1].slice(11);
        		out_time[i] = data[i][2].slice(11);
        		shift_createid[i] = data[i][3];
        	}

        var text = '登録';
        var registerHTML = '<li class="mt-1 table-danger">' + text + '</li>';
        $(document).ready(function(){
        	for(let i=0; i<date_shift.length; i++){
        		$(`ul[data-date=${date_shift[i]}]`).append(registerHTML);
        	}
        });
        }

      //提出ボタンの表示切り替え
        var info = @json($info);

        if (info == null ){
        	document.getElementById('post-btn').setAttribute("value",'提出');
        	document.getElementById('post-btn').disabled = false;
        }else if ( info !== null && info == '0' ){
        	document.getElementById('post-btn').setAttribute("value",'提出');
        	document.getElementById('create-btn').disabled = false;
        }else if ( info !== null && info !== '0' ){
        	document.getElementById('post-btn').setAttribute("value",'提出済');
        	document.getElementById('post-btn').disabled = true;
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

      //カレンダー内に登録の表示
        var data = @json($data);

        if( data !== null ){
        	var date_shift = [];
        	var go_time = [];
        	var out_time = [];
        	var shift_createid = [];
        	for(let i = 0; i<data.length; i++){
        		date_shift[i] = data[i][0];
        		go_time[i] = data[i][1].slice(11);
        		out_time[i] = data[i][2].slice(11);
        		shift_createid[i] = data[i][3];
        	}

        	var text = '登録';
        	var registerHTML = '<li class="mt-1 table-danger">' + text + '</li>';
            $(document).ready(function(){
            	for(let i=0; i<date_shift.length; i++){
            		$(`ul[data-date=${date_shift[i]}]`).append(registerHTML);
            	}
            });
        }

      //提出ボタンの表示切り替え
        var info = @json($info);

        if (info == null ){
        	document.getElementById('post-btn').setAttribute("value",'提出');
        	document.getElementById('post-btn').disabled = false;
        }else if ( info !== null && info == '0' ){
        	document.getElementById('post-btn').setAttribute("value",'提出');
        	document.getElementById('create-btn').disabled = false;
        }else if ( info !== null && info !== '0' ){
        	document.getElementById('post-btn').setAttribute("value",'提出済');
        	document.getElementById('post-btn').disabled = true;
        }
}

showCalendar(year, month);

//シフト一覧画面へ(< 戻る)
function back(){
	$(document).on('click','.back',function(){
		window.location.href="/staff/shift-record";
	});
}

//カレンダー内に登録の表示
var data = @json($data);

if( data !== null ){
	var date_shift = [];
	var go_time = [];
	var out_time = [];
	var shift_createid = [];
	for(let i = 0; i<data.length; i++){
		date_shift[i] = data[i][0];
		go_time[i] = data[i][1].slice(11);
		out_time[i] = data[i][2].slice(11);
		shift_createid[i] = data[i][3];
	}

	var text = '登録';
	var registerHTML = '<li class="mt-1 table-danger">' + text + '</li>';
    $(document).ready(function(){
    	for(let i=0; i<date_shift.length; i++){
    		$(`ul[data-date=${date_shift[i]}]`).append(registerHTML);
    	}
    });
}

//提出ボタンの表示切り替え
var info = @json($info);

if (info == null ){
	document.getElementById('post-btn').setAttribute("value",'提出');
	document.getElementById('post-btn').disabled = false;
}else if ( info !== null && info == '0' ){
	document.getElementById('post-btn').setAttribute("value",'提出');
	document.getElementById('create-btn').disabled = false;
}else if ( info !== null && info !== '0' ){
	document.getElementById('post-btn').setAttribute("value",'提出済');
	document.getElementById('post-btn').disabled = true;
}

//シフト作成詳細ダイアログ
function showDialog(){
    $(document).on('click','td',function(){
        console.log('関数は動いております。');
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
    	//日付による制御
    	var todate = date.getDate();
    	var year01 = date.getFullYear();
		var month01 = date.getMonth()+2;
		if (month01 > 12) {
            year01++;
            month01 = 1;
        }
    	if( todate > 2 && todate < 18){
    		//3~17日
    		var next1th = year01 + '-' + ('0' + month01).slice(-2) + '-' + '01';
    		if( date_info >= next1th ){
    			//フォームボタン使える
    			//未登録のフォーム内の初期値設定
    			document.getElementById('12:00:00').selected = true;
    			document.getElementById('20:30:00').selected = true;
    			document.getElementById('create-btn').setAttribute("value",'登録');
    			document.getElementById('create-btn').disabled = false;
    			document.getElementById('shift_createid').setAttribute("value",null);
    			//hiddenデータの設定
    			document.getElementById("date-info").setAttribute("value",dateInfo);
    			//登録済のフォーム内初期値設定
    			if( data !== null ){
    				for(let i=0; i < data.length; i++){
    					if( date_info == date_shift[i] ){
    						document.getElementById(`${go_time[i]}`).selected = true;
    						document.getElementById(`${out_time[i]}`).selected = true;
    						document.getElementById('create-btn').setAttribute("value",'解除');
    						document.getElementById('create-btn').disabled = false;
    						document.getElementById('shift_createid').setAttribute("value",`${shift_createid[i]}`);
    					}else{
    						continue;
    					}
    				}
    			}
    		}else{
    			//フォームボタン使えない
    			//未登録のフォーム内の初期値設定
    			document.getElementById('12:00:00').selected = true;
    			document.getElementById('20:30:00').selected = true;
    			document.getElementById('create-btn').setAttribute("value",'登録');
    			document.getElementById('create-btn').disabled = true;
    			document.getElementById('shift_createid').setAttribute("value",null);
    			//hiddenデータの設定
    			document.getElementById("date-info").setAttribute("value",dateInfo);
    			//登録済のフォーム内初期値設定
    			if( data !== null ){
    				for(let i=0; i < data.length; i++){
    					if( date_info == date_shift[i] ){
    						document.getElementById(`${go_time[i]}`).selected = true;
    						document.getElementById(`${out_time[i]}`).selected = true;
    						document.getElementById('create-btn').setAttribute("value",'解除');
    						document.getElementById('create-btn').disabled = true;
    						document.getElementById('shift_createid').setAttribute("value",`${shift_createid[i]}`);
    					}else{
    						continue;
    					}
    				}
    			}
    		}
    	}else if( todate > 17){
    		//18~末日
    		var next16th = year01 + '-' + ('0' + month01).slice(-2) + '-' + '16';
    		if( date_info >= next16th ){
    			//フォームボタン使える
    			//未登録のフォーム内の初期値設定
    			document.getElementById('12:00:00').selected = true;
    			document.getElementById('20:30:00').selected = true;
    			document.getElementById('create-btn').setAttribute("value",'登録');
    			document.getElementById('create-btn').disabled = false;
    			document.getElementById('shift_createid').setAttribute("value",null);
    			//hiddenデータの設定
    			document.getElementById("date-info").setAttribute("value",dateInfo);
    			//登録済のフォーム内初期値設定
    			if( data !== null ){
    				for(let i=0; i < data.length; i++){
    					if( date_info == date_shift[i] ){
    						document.getElementById(`${go_time[i]}`).selected = true;
    						document.getElementById(`${out_time[i]}`).selected = true;
    						document.getElementById('create-btn').setAttribute("value",'解除');
    						document.getElementById('create-btn').disabled = false;
    						document.getElementById('shift_createid').setAttribute("value",`${shift_createid[i]}`);
    					}else{
    						continue;
    					}
    				}
    			}
    		}else{
    			//フォームボタン使えない
    			//未登録のフォーム内の初期値設定
    			document.getElementById('12:00:00').selected = true;
    			document.getElementById('20:30:00').selected = true;
    			document.getElementById('create-btn').setAttribute("value",'登録');
    			document.getElementById('create-btn').disabled = true;
    			document.getElementById('shift_createid').setAttribute("value",null);
    			//hiddenデータの設定
    			document.getElementById("date-info").setAttribute("value",dateInfo);
    			//登録済のフォーム内初期値設定
    			if( data !== null ){
    				for(let i=0; i < data.length; i++){
    					if( date_info == date_shift[i] ){
    						document.getElementById(`${go_time[i]}`).selected = true;
    						document.getElementById(`${out_time[i]}`).selected = true;
    						document.getElementById('create-btn').setAttribute("value",'解除');
    						document.getElementById('create-btn').disabled = true;
    						document.getElementById('shift_createid').setAttribute("value",`${shift_createid[i]}`);
    					}else{
    						continue;
    					}
    				}
    			}
    		}
    	}else{
    		//翌月2日まで
    		var this16th = date.getFullYear() + '-' + ('0' + (date.getMonth()+1)).slice(-2) + '-' + '16';
    		if( date_info >= this16th ){
    			//フォームボタン使える
    			//未登録のフォーム内の初期値設定
    			document.getElementById('12:00:00').selected = true;
    			document.getElementById('20:30:00').selected = true;
    			document.getElementById('create-btn').setAttribute("value",'登録');
    			document.getElementById('create-btn').disabled = false;
    			document.getElementById('shift_createid').setAttribute("value",null);
    			//hiddenデータの設定
    			document.getElementById("date-info").setAttribute("value",dateInfo);
    			//登録済のフォーム内初期値設定
    			if( data !== null ){
    				for(let i=0; i < data.length; i++){
    					if( date_info == date_shift[i] ){
    						document.getElementById(`${go_time[i]}`).selected = true;
    						document.getElementById(`${out_time[i]}`).selected = true;
    						document.getElementById('create-btn').setAttribute("value",'解除');
    						document.getElementById('create-btn').disabled = false;
    						document.getElementById('shift_createid').setAttribute("value",`${shift_createid[i]}`);
    					}else{
    						continue;
    					}
    				}
    			}
    		}else{
    			//フォームボタン使えない
    			//未登録のフォーム内の初期値設定
    			document.getElementById('12:00:00').selected = true;
    			document.getElementById('20:30:00').selected = true;
    			document.getElementById('create-btn').setAttribute("value",'登録');
    			document.getElementById('create-btn').disabled = true;
    			document.getElementById('shift_createid').setAttribute("value",null);
    			//hiddenデータの設定
    			document.getElementById("date-info").setAttribute("value",dateInfo);
    			//登録済のフォーム内初期値設定
    			if( data !== null ){
    				for(let i=0; i < data.length; i++){
    					if( date_info == date_shift[i] ){
    						document.getElementById(`${go_time[i]}`).selected = true;
    						document.getElementById(`${out_time[i]}`).selected = true;
    						document.getElementById('create-btn').setAttribute("value",'解除');
    						document.getElementById('create-btn').disabled = true;
    						document.getElementById('shift_createid').setAttribute("value",`${shift_createid[i]}`);
    					}else{
    						continue;
    					}
    				}
    			}
    		}
    	}
    });
}
</script>
@endsection