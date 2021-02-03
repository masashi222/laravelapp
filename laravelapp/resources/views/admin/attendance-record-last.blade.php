@extends('layouts.base')

@section('title','勤怠一覧')

@section('link')
<link rel="stylesheet" href="{{asset('/css/attendance.css')}}" >
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('back_btn')
		<div class="m-3 back" onclick="back();">&lt;&nbsp;戻る</div>
		@endslot

		@slot('display_title')
		<div class="text-center">勤怠一覧&nbsp;({{ $info[4] }})</div>
        <div class="text-center mt-1">{{ $info[0] }}</div>
        @endslot
    @endcomponent
    <div class="row mt-3">
        <div class="col">
            <nav class="nav nav-tabs nav-pills nav-fill">
                <a class="nav-item nav-link active table-info">先月</a>
                <a class="nav-item nav-link table-light record-nav" data-id="{{ $info[5] }}" onclick="toThis();">今月</a>
                <a class="nav-item nav-link table-light record-nav" data-id="{{ $info[5] }}" onclick="toNext();">来月</a>
            </nav>
    	</div>
	</div>
    <div class="row mt-3">
        <div class="col">
            <table class="table">
				<thead class="thead-dark">
					<tr>
						<th></th><th>出勤</th><th>退勤</th><th>給与</th><th>交通費</th>
					</tr>
				</thead>
				<tbody>
					@foreach( $data as $item )
					<tr data-id="{{ $info[5] }}" data-stampid="{{ $item[6] }}">
						<th>{{ $item[0] }}({{ $item[1] }})</th><td>{{ $item[2] }}</td><td>{{ $item[3] }}</td><td>{{ $item[4] }}</td><td>{{ $item[5] }}</td>
					</tr>
					@endforeach
					<tr>
						<th>計</th><th class="table-dark">交通費</th><td>{{ $info[1] }}</td><th class="table-dark">給与</th><td>{{ $info[2] }}</td>
					</tr>
					<tr>
						<th></th><th></th><td></td><th class="table-dark">合計給与</th><td>{{ $info[3] }}</td>
					</tr>
				</tbody>
			</table>
        </div>
    </div>
    @if( $info[6] == 0 )
    @endif
    @if( $info[6] == 1 )
    <div class="row">
    	<div class="col">
    		<button type="button" class="btn btn-warning register-btn" onclick="toRegister();" data-id="{{ $info[5] }}">登録</button>
    	</div>
    </div>
    @endif
</div>
@endsection
@section('script')
<script>
//(<戻る)
function back(){
	$(document).on('click','.back',function(){
		window.location.href="/admin/attendance-staff-record";
	});
}

//勤怠一覧表示期間変更(今月)
function toThis(){
	$(document).on('click','.record-nav',function(){
		var id = $(this).data('id');
		window.location.href="/admin/attendance-record/" + id;
	});
}
//勤怠一覧表示期間変更(来月)
function toNext(){
	$(document).on('click','.record-nav',function(){
		var id = $(this).data('id');
		window.location.href="/admin/attendance-record-next/" + id;
	});
}

//勤怠登録画面
function toRegister(){
	$(document).on('click','.register-btn',function(){
		var id = $(this).data('id');
		window.location.href="/admin/attendance-register/" + id;
	});
}
</script>
@endsection