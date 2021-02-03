@extends('layouts.base')

@section('title','勤怠管理')

@section('link')
<link rel="stylesheet" href="{{asset('/css/attendance.css')}}" >
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('display_title')
		<div class="text-center">勤怠管理</div>
        <div class="text-center mt-1">{{ $display[0] }}</div>
        @endslot
    @endcomponent
	<div class="row mt-3">
		@foreach( $contents as $content )
    	<div class="col-sm-12">
    		<table class="table table-dark staff-table"  data-id="{{ $content[6] }}" onclick="toattendancerecordAdmin();">
    			<thead>
					<tr>
						<th></th><th>就業時間</th><th>交通費</th><th>給与</th>
						<th>確定給与</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th>{{ $content[5] }}</th><td>{{ $content[3] }}:{{ $content[4] }}</td><td>{{ $content[0] }}</td><td>{{ $content[1] }}</td>
						<td>{{ $content[2] }}</td>
					</tr>
				</tbody>
    		</table>
    	</div>
    	@endforeach
    </div>
    @if( $display[1] == 0 )
    <div class="row">
    	<div class="col">
			<button type="button" class="btn btn-warning payroll-btn" onclick="toPayroll();" disabled>給与計算書</button>
    	</div>
    </div>
    @endif
    @if( $display[1] == 1 )
    <div class="row">
    	<div class="col">
			<button type="button" class="btn btn-warning payroll-btn" onclick="toPayroll();">給与計算書</button>
    	</div>
    </div>
    @endif
    @component('components.navbar-admin')
		@slot('nav')
		<a class="nav-item nav-link bg-light shift-nav" onclick="toshiftrecordAdmin();">シフト</a>
        <a class="nav-item nav-link active bg-info">勤怠</a>
        <a class="nav-item nav-link bg-light staff-nav" onclick="tostaffRecord();">従業員</a>
        <a class="nav-item nav-link bg-light stamp-nav" onclick="toStamppass();">打刻</a>
        @endslot
    @endcomponent
</div>
@endsection
@section('script')
<script>
//nav
function toshiftrecordAdmin(){
	$(document).on('click','.shift-nav',function(){
		window.location.href="/admin/shift-record";
	});
}
function tostaffRecord(){
	$(document).on('click','.staff-nav',function(){
		window.location.href="/admin/staff-record";
	});
}
function toStamppass(){
	$(document).on('click','.stamp-nav',function(){
		window.location.href="/admin/stamp-pass";
	});
}

//給与計算書画面へ
function toPayroll(){
	$(document).on('click','.payroll-btn',function(){
		window.location.href="/admin/payroll";
	});
}

//勤怠一覧画面へ
function toattendancerecordAdmin(){
	$(document).on('click','.staff-table',function(){
		var id = $(this).data('id');
		window.location.href="/admin/attendance-record/" + id;
	});
}
</script>
@endsection