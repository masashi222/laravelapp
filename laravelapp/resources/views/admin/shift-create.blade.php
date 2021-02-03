@extends('layouts.base')

@section('title','シフト作成')

@section('link')
<link rel="stylesheet" href="{{asset('/css/shift.css')}}" >
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/create.css')}}">
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
    @if( $info !== null)
    <div class="row pt-3">
    	<div class="col">
    		<div class="alert alert-warning alert-dismissible fade show" role="alert">
    			<button type="button" class="close" data-dismiss="alert">
    				<span aria-hidden="true">&times;</span>
    			</button>
    			<h3 class="alert-heading">シフト未提出</h3>
    			<p>
    			@foreach( $info as $item )
    			{{ $item }} &nbsp;
    			@endforeach
    			</p>
    		</div>
    	</div>
    </div>
    @endif
	<div class="row m-3">
    	<div class="col">
    		<nav class="nav nav-pills">
                <a class="nav-item nav-link active table-info">月</a>
                <a class="nav-item nav-link table-light day-nav" onclick="toshiftcreateAdmin2();">日</a>
            </nav>
    	</div>
    </div>
    <div class="row">
        <div class="col">
            <table class="table" id="calendar">
                <!-- script -->
            </table>
            <!-- シフト詳細ダイアログ -->
            <div class="modal fade" id="dialog1">
            	<div class="modal-dialog" role="document">
            		<div class="modal-content">
            			<div class="modal-header">
            				<h5 class="modal-title"><!-- script --></h5>
            				<button type="button" class="btn" data-dismiss="modal" onclick="">
	                			<span>&times;</span>
	                		</button>
            			</div>
            			<form method="post" action="{{ url('/admin/shift-create') }}">
            			{{ csrf_field() }}
                			<div class="modal-body" id="modal-body" data-date="">
                                <!-- script -->
                			</div>
                			<div class="modal-footer">
                				<div class="form-group">
	                				<input type="button" class="btn btn-danger" id="fix-btn" name="fix-btn" value="追加" data-dismiss="modal" onclick="subDialog();">
               					</div>
               					<div class="form-group">
	                				<input type="submit" class="btn btn-warning" id="register-btn" name="register-btn" value="">
               					</div>
                			</div>
            			</form>
            		</div>
            	</div>
            </div>
            <!-- 従業員詳細ダイアログ -->
            <div class="modal fade" id="dialog2">
            	<div class="modal-dialog" role="document">
            		<div class="modal-content">
            			<div class="modal-header">
            				<h5 class="modal-title"></h5>
            				<button type="button" class="btn" data-dismiss="modal" onclick="">
	                			<span>&times;</span>
	                		</button>
            			</div>
            			<div class="modal-header">
            				従業員追加
            			</div>
            			<form method="post" action="{{ url('/admin/shift-create') }}">
            			{{ csrf_field() }}
                			<div class="modal-body" id="sub-body">
                                <!-- script -->
                			</div>
                			<div class="modal-footer">
                				<div class="form-group">
	                				<input type="submit" class="btn btn-danger" name="add-btn" id="add-btn" value="追加">
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
    		<form method="post" action="{{ url('/admin/shift-create') }}" onSubmit="return check()">
    		{{ csrf_field() }}
    			<div class="form-row">
    				<div class="col">
    					<div class="form-grop">
    						@if( $display == '0' )
							<input type="submit" class="btn btn-warning float-right" name="confirm" id="confirm" value="確定" disabled>
							@endif
							@if( $display == '1' )
							<input type="submit" class="btn btn-warning float-right" name="confirm" id="confirm" value="確定">
							@endif
							@if( $display == '2' )
							<input type="submit" class="btn btn-warning float-right" name="confirm" id="confirm" value="確定済" disabled>
							@endif
						</div>
    				</div>
    			</div>
    		</form>
    	</div>
    </div>
    @php
    $data = $data;
    $info = $info;
    $membes_data = $members_data;
    @endphp
</div>
@endsection
@section('script')
<script>
//calendar
const date = new Date();
const nowDate = date.getDate();
const this16th = date.getFullYear()+'-'+('0'+(date.getMonth()+1)).slice(-2)+'-'+'16';
const this31th = date.getFullYear()+'-'+('0'+(date.getMonth()+1)).slice(-2)+'-'+(new Date(date.getFullYear(),date.getMonth()+1,0)).getDate();
var year01 = date.getFullYear();
var month01 = date.getMonth()+2;
if (month01 > 12) {
    year01++;
    month01 = 1;
}
const next1th = year01 +'-'+('0'+ month01 ).slice(-2)+'-'+'01';
const next15th = year01 +'-'+('0'+ month01 ).slice(-2)+'-'+'15';
const weeks = ['日', '月', '火', '水', '木', '金', '土'];

if(nowDate < 16){
	//今月の表示
	var year = date.getFullYear();
	var month = date.getMonth() + 1;

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
	                calendarHtml += '<td class="border" onclick="showDialog();" data-id=\'{"year":' +year+ ',"month":' +month+ ',"date":' +dayCount+ '}\' data-date="'+ yymmdd + '">' + dayCount + '<ul class="px-0" style="font-size:0.75em; color:black; list-style:none;" data-date="'+ yymmdd +'"></ul></td>';
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

	      //カレンダー内に名前の表示
	        var data = @json($data);

	        if( data !== null ){
	        	var shift_createid = [];
	        	var memberid = [];
	        	let name = [];
	        	var date_shift = [];
	        	var go_time = [];
	        	var out_time = [];
	        	var display_out = [];
	        	var is_register = [];

	        	for(let i = 0; i<data.length; i++){
	        		shift_createid[i] = data[i][0];
	        		memberid[i] = data[i][1];
	        		name[i] = data[i][2];
	        		date_shift[i] = data[i][3];
	        		go_time[i] = data[i][4];
	        		out_time[i] = data[i][5];
	        		if( data[i][5] == '05:00:00' ){
	        			display_out[i] = 'LAST';
	        		}else{
	        			display_out[i] = data[i][5].slice(0,5);
	        		}
	        		is_register[i] = data[i][6];
	        	}


	        $(document).ready(function(){
	        	var w = $(window).width();
	            var x = 500;
	            if (w <= x) {
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	                	if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
	            	}
	            }else{
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	            		if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
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

	      //カレンダー内に名前の表示
	        var data = @json($data);

	        if( data !== null ){
	        	var shift_createid = [];
	        	var memberid = [];
	        	let name = [];
	        	var date_shift = [];
	        	var go_time = [];
	        	var out_time = [];
	        	var display_out = [];
	        	var is_register = [];

	        	for(let i = 0; i<data.length; i++){
	        		shift_createid[i] = data[i][0];
	        		memberid[i] = data[i][1];
	        		name[i] = data[i][2];
	        		date_shift[i] = data[i][3];
	        		go_time[i] = data[i][4];
	        		out_time[i] = data[i][5];
	        		if( data[i][5] == '05:00:00' ){
	        			display_out[i] = 'LAST';
	        		}else{
	        			display_out[i] = data[i][5].slice(0,5);
	        		}
	        		is_register[i] = data[i][6];
	        	}


	        $(document).ready(function(){
	        	var w = $(window).width();
	            var x = 500;
	            if (w <= x) {
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	                	if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
	            	}
	            }else{
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	            		if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
	            	}
	            }
	        });
	        }
	}

	showCalendar(year, month);

}else{
	//翌月の表示
	var year = date.getFullYear();
	var month = date.getMonth() + 2;
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
	                calendarHtml += '<td class="border" onclick="showDialog();" data-id=\'{"year":' +year+ ',"month":' +month+ ',"date":' +dayCount+ '}\'data-date="'+ yymmdd + '">' + dayCount + '<ul class="px-0" style="font-size:0.75em; color:black; list-style:none;" data-date="'+ yymmdd +'"></ul></td>';
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

	      //カレンダー内に名前の表示
	        var data = @json($data);

	        if( data !== null ){
	        	var shift_createid = [];
	        	var memberid = [];
	        	let name = [];
	        	var date_shift = [];
	        	var go_time = [];
	        	var out_time = [];
	        	var display_out = [];
	        	var is_register = [];

	        	for(let i = 0; i<data.length; i++){
	        		shift_createid[i] = data[i][0];
	        		memberid[i] = data[i][1];
	        		name[i] = data[i][2];
	        		date_shift[i] = data[i][3];
	        		go_time[i] = data[i][4];
	        		out_time[i] = data[i][5];
	        		if( data[i][5] == '05:00:00' ){
	        			display_out[i] = 'LAST';
	        		}else{
	        			display_out[i] = data[i][5].slice(0,5);
	        		}
	        		is_register[i] = data[i][6];
	        	}


	        $(document).ready(function(){
	        	var w = $(window).width();
	            var x = 500;
	            if (w <= x) {
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	                	if( is_register[i] == '1' ){
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
	            	}
	            }else{
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	            		if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
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

	      //カレンダー内に名前の表示
	        var data = @json($data);

	        if( data !== null ){
	        	var shift_createid = [];
	        	var memberid = [];
	        	let name = [];
	        	var date_shift = [];
	        	var go_time = [];
	        	var out_time = [];
	        	var display_out = [];
	        	var is_register = [];

	        	for(let i = 0; i<data.length; i++){
	        		shift_createid[i] = data[i][0];
	        		memberid[i] = data[i][1];
	        		name[i] = data[i][2];
	        		date_shift[i] = data[i][3];
	        		go_time[i] = data[i][4];
	        		out_time[i] = data[i][5];
	        		if( data[i][5] == '05:00:00' ){
	        			display_out[i] = 'LAST';
	        		}else{
	        			display_out[i] = data[i][5].slice(0,5);
	        		}
	        		is_register[i] = data[i][6];
	        	}


	        $(document).ready(function(){
	        	var w = $(window).width();
	            var x = 500;
	            if (w <= x) {
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	                	if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + name[i].slice(0,2) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
	            	}
	            }else{
	            	var memberHTML = [];
	            	for(let i=0; i<date_shift.length; i++){
	            		if( is_register[i] == '1' ){
	                    	console.log('登録されています');
	                		memberHTML[i] = '<li class="mt-1 table-danger">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else if( is_register[i] == '0' ){
	            			memberHTML[i] = '<li class="mt-1 text-muted">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}else{
	                		memberHTML[i] = '<li class="mt-1">' + (name[i]+'　　').slice(0,3) + '</li>';
	            			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	                	}
	            	}
	            }
	        });
	        }
	}

	showCalendar(year, month);
}

//シフト作成画面変更(nav)
function toshiftcreateAdmin2(){
	$(document).on('click','.day-nav',function(){
		window.location.href="/admin/shift-create2";
	});
}


//戻る(<戻る)
function back(){
	$(document).on('click','.back',function(){
		window.location.href="/admin/shift-record";
	});
}

//カレンダー内に名前の表示
var data = @json($data);

if( data !== null ){
	var shift_createid = [];
	var memberid = [];
	let name = [];
	var date_shift = [];
	var go_time = [];
	var out_time = [];
	var display_out = [];
	var is_register = [];

	for(let i = 0; i<data.length; i++){
		shift_createid[i] = data[i][0];
		memberid[i] = data[i][1];
		name[i] = data[i][2];
		date_shift[i] = data[i][3];
		go_time[i] = data[i][4];
		out_time[i] = data[i][5];
		if( data[i][5] == '05:00:00' ){
			display_out[i] = 'LAST';
		}else{
			display_out[i] = data[i][5].slice(0,5);
		}
		is_register[i] = data[i][6];
	}


$(document).ready(function(){
	var w = $(window).width();
    var x = 500;
    if (w <= x) {
    	var memberHTML = [];
    	for(let i=0; i<date_shift.length; i++){
        	if( is_register[i] == '1' ){
            	console.log('登録されています');
        		memberHTML[i] = '<li class="mt-1 table-danger">' + name[i].slice(0,2) + '</li>';
    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
        	}else if( is_register[i] == '0' ){
    			memberHTML[i] = '<li class="mt-1 text-muted">' + name[i].slice(0,2) + '</li>';
    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
        	}else{
        		memberHTML[i] = '<li class="mt-1">' + name[i].slice(0,2) + '</li>';
    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
        	}
    	}
    }else{
    	var memberHTML = [];
    	for(let i=0; i<date_shift.length; i++){
    		if( is_register[i] == '1' ){
            	console.log('登録されています');
        		memberHTML[i] = '<li class="mt-1 table-danger">' + (name[i]+'　　').slice(0,3) + '</li>';
    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
        	}else if( is_register[i] == '0' ){
    			memberHTML[i] = '<li class="mt-1 text-muted">' + (name[i]+'　　').slice(0,3) + '</li>';
    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
        	}else{
        		memberHTML[i] = '<li class="mt-1">' + (name[i]+'　　').slice(0,3) + '</li>';
    			$(`ul[data-date=${date_shift[i]}]`).append(memberHTML[i]);
        	}
    	}
    }
});
}

//日付を押した時のきっかけ
function showDialog(){
	//シフト作成詳細ダイアログ
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
		//ボタンの初期設定
		document.getElementById('register-btn').setAttribute("value",'登録');
		document.getElementById('register-btn').disabled = true;
		if( nowDate >2 && nowDate <16 && dateInfo >= this16th && dateInfo <= this31th){
			document.getElementById('fix-btn').disabled = false;
		}else if( nowDate >17 && dateInfo >= next1th && dateInfo <= next15th){
			document.getElementById('fix-btn').disabled = false;
		}else{
			document.getElementById('fix-btn').disabled = true;
		}
		if( data !== null ){
			let name = [];
			for(let i = 0; i<data.length; i++){
				name[i] = data[i][2];
			}
			//子要素の削除
			const parent = document.getElementById('modal-body');
			while( parent.firstChild ){
				parent.removeChild( parent.firstChild );
			}
			//希望ありのフォーム内の初期値設定
			var memberHTML = [];
	    	for(let i=0; i<date_shift.length; i++){
	        	if( date_info == date_shift[i] ){
	        		memberHTML[i] = '<div class="form-check form-group">\
	        							<input type="hidden" id="date-info" name="date-info[]" value="'+ date_shift[i] +'">\
	        							<input type="hidden" id="shift_createid" name="shift_createid[]" value="'+ shift_createid[i] +'">\
                	        			<label class="form-check-label">';
                	if( is_register[i] == '1' ){
                    	memberHTML[i] += '<input type="checkbox" class="form-check-input" name="selectid[]"  value="'+ shift_createid[i] +'" checked>';
                	}else{
                		memberHTML[i] += '<input type="checkbox" class="form-check-input" name="selectid[]"  value="'+ shift_createid[i] +'">';
                	}
                	memberHTML[i] += (name[i]+'　　').slice(0,3) + '&nbsp;' + go_time[i].slice(0,5) + '~' + display_out[i] +'\
            	        			</label>\
            	        			<br>\
            	        			<div class="input-group align-items-center">\
            	        				<div class="mx-2">出勤</div>\
                	        			<select id="'+ shift_createid[i] +'" name="go_time[]" class="form-control">\
                    	        			<option id="12:00:00" value="12:00:00">12:00</option><option id="12:30:00" value="12:30:00">12:30</option>\
                    	        			<option id="13:00:00" value="13:00:00">13:00</option><option id="13:30:00" value="13:30:00">13:30</option>\
                    	        			<option id="14:00:00" value="14:00:00">14:00</option><option id="14:30:00" value="14:30:00">14:30</option>\
                    	        			<option id="15:00:00" value="15:00:00">15:00</option><option id="15:30:00" value="15:30:00">15:30</option>\
                    						<option id="16:00:00" value="16:00:00">16:00</option><option id="16:30:00" value="16:30:00">16:30</option>\
                    						<option id="17:00:00" value="17:00:00">17:00</option><option id="17:30:00" value="17:30:00">17:30</option>\
                    						<option id="18:00:00" value="18:00:00">18:00</option><option id="18:30:00" value="18:30:00">18:30</option>\
                    						<option id="19:00:00" value="19:00:00">19:00</option><option id="19:30:00" value="19:30:00">19:30</option>\
                    						<option id="20:00:00" value="20:00:00">20:00</option>\
                						</select>\
        							</div>\
            						<div class="input-group  align-items-center">\
            							<div class="mx-2">退勤</div>\
                						<select id="'+ shift_createid[i] +'" name="out_time[]" class="form-control">\
                        					<option id="20:30:00" value="20:30:00">20:30</option><option id="21:00:00" value="21:00:00">21:00</option>\
                        					<option id="21:30:00" value="21:30:00">21:30</option><option id="22:00:00" value="22:00:00">22:00</option>\
                        					<option id="22:30:00" value="22:30:00">22:30</option><option id="23:00:00" value="23:00:00">23:00</option>\
                        					<option id="23:30:00" value="23:30:00">23:30</option><option id="00:00:00" value="00:00:00">00:00</option>\
                        					<option id="00:30:00" value="00:30:00">00:30</option><option id="01:00:00" value="01:00:00">01:00</option>\
                        					<option id="01:30:00" value="01:30:00">01:30</option><option id="02:00:00" value="02:00:00">02:00</option>\
                        					<option id="05:00:00" value="05:00:00">LAST</option>\
                    					</select>\
                					</div>\
            					</div>';
	        		$(`#modal-body[data-date=${date_shift[i]}]`).append(memberHTML[i]);
	        		//セレクトボックスの初期値設定
	        		$(`select#${shift_createid[i]} option[value="${go_time[i]}"]`).prop('selected',true);
					$(`select#${shift_createid[i]} option[value="${out_time[i]}"]`).prop('selected',true);
					if( (nowDate >2 && nowDate <16) ||  nowDate >17){
						document.getElementById('register-btn').removeAttribute("value");
						document.getElementById('register-btn').setAttribute("value",'登録');
						document.getElementById('register-btn').disabled = false;
					}
	        	}else{
					continue;
	    		}
	    	}
		}

	});
}
//追加ボタンを押した時のきっかけ
function subDialog(){
	//従業員詳細ダイアログ
	$(document).on('click','#fix-btn',function(){
		//モーダルダイアログ
		$('#dialog2').modal();
		//ボタン初期値
		document.getElementById('add-btn').disabled = true;
		//日付情報の取得
		var date_info = document.getElementById("modal-body").getAttribute("data-date");
		console.log(date_info);
		//追加メンバー情報の取得
		var members_data = @json($members_data);
		let members_id = [];
		let members_name = [];
		let name = [];
		if ( data !== null ){
    		for(let i = 0; i<data.length; i++){
    			if( date_info == date_shift[i] ){
    				name[i] = data[i][2];
    			}else{
    				continue;
    			}
    		}
		}
		name = name.filter(function (x, i, self) {
            return self.indexOf(x) === i;
        });
		//子要素の削除
		const parent = document.getElementById('sub-body');
		while( parent.firstChild ){
			parent.removeChild( parent.firstChild );
		}
		let staffHTML = [];
		for(let i=0; i<members_data.length; i++){
			members_id[i] = members_data[i][0];
			members_name[i] = members_data[i][1];
			if( name.indexOf(members_name[i]) >= 0){
				continue;
			}else{
    			staffHTML[i] = '<div class="form-check form-group">\
                				<input type="hidden" id="date-info" name="date-info" value="'+ date_info +'">\
    							<label class="form-check-label">\
    								<input type="checkbox" class="form-check-input staffid" name="staffid[]" value="'+ members_id[i] +'">'+ (members_name[i]+'　　').slice(0,3) +'\
    							</label>\
    							</div>';
    			$('#sub-body').append(staffHTML[i]);
			}
		}
		//ボタンの動き
		$(document).on('click','.staffid', function(){
			if ($(".staffid:checked").length > 0) {
		          // ボタン有効
		          $("#add-btn").prop("disabled", false);
		        } else {
		          // ボタン無効
		          $("#add-btn").prop("disabled", true);
		        }
		  });
	});

}

//シフト確定ダイアログの設定
function check(){
	if(window.confirm('実行してよろしいですか？')){ // 確認ダイアログを表示
		return true; // 「OK」時は送信を実行
	}
	else{ // 「キャンセル」時の処理
		window.alert('キャンセルされました'); // 警告ダイアログを表示
		return false; // 送信を中止
	}
}
</script>
@endsection