@extends('layouts.base')

@section('title','勤怠打刻')

@section('link')
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/stamp.css')}}" >
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('display_title')
		<div class="text-center mt-3">勤怠打刻</div>
        @endslot
    @endcomponent
	<div class="stamp-pass-frame">
		<div class="row">
    		<div class="col">
    			<h3 id="stamp-date" class="text-center">
    			<!-- script -->
    			</h3>
    		</div>
    	</div>
    	<div class="row my-3">
    		<div class="col">
    			<h4 class="text-center">打刻キー</h4>
    			<h3 class="text-center">{{ $code }}</h3>
    		</div>
    	</div>
	</div>
	@component('components.navbar-admin')
	@slot('nav')
	<a class="nav-item nav-link bg-light shift-nav" onclick="toshiftrecordAdmin();">シフト</a>
    <a class="nav-item nav-link bg-light attendance-nav" onclick="toattendancestaffRecord();">勤怠</a>
    <a class="nav-item nav-link bg-light staff-nav" onclick="tostaffRecord();">従業員</a>
	<a class="nav-item nav-link active bg-info">打刻</a>
    @endslot
	@endcomponent
</div>
@endsection
@section('script')
<script>
//スタッフ修正画面へ
function tostaffFix(){
	$(document).on('click','.card',function(){
		var id = $(this).data('id');
		window.location.href="/admin/staff-fix/" + id;
	});
}
//スタッフ登録画面へ
function tostaffRegister(){
	$(document).on('click','.register-btn',function(){
		window.location.href="/admin/staff-register";
	});
}

//nav
function toshiftrecordAdmin(){
	$(document).on('click','.shift-nav',function(){
		window.location.href="/admin/shift-record";
	});
}
function toattendancestaffRecord(){
	$(document).on('click','.attendance-nav',function(){
		window.location.href="/admin/attendance-staff-record";
	});
}
function tostaffRecord(){
	$(document).on('click','.staff-nav',function(){
		window.location.href="/admin/staff-record";
	});
}

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

	document.getElementById('stamp-date').textContent = fullDate;
};

//setInterval('time()',1000);

time();
</script>
@endsection