@extends('layouts.base')

@section('title','従業員一覧')

@section('link')
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/staff.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('display_title')
		<div class="text-center mt-3">従業員管理</div>
        @endslot

    @endcomponent
	<div class="row">
		@foreach($members as $member)
    	<div class="col-sm-6">
    		<div class="card border-warning my-3 mx-auto" style="width:300px;" onclick="tostaffFix();" data-id="{{ $member->id }}">
    			<h4 class="card-header">{{ $member->name }}</h4>
    			<div class="card-body">
    				<div>業務形態：{{ $member->getBusiness() }}</div>
    				<div>LoginID：{{ $member->id }}</div>
    			</div>
    		</div>
    	</div>
    	@endforeach
    </div>
    <div class="row">
    	<div class="col">
			<button type="button" class="btn btn-warning m-4 register-btn" onclick="tostaffRegister();">登録</button>
    	</div>
    </div>
	@component('components.navbar-admin')
	@slot('nav')
	<a class="nav-item nav-link bg-light shift-nav" onclick="toshiftrecordAdmin();">シフト</a>
    <a class="nav-item nav-link bg-light attendance-nav" onclick="toattendancestaffRecord();">勤怠</a>
	<a class="nav-item nav-link active bg-info">従業員</a>
	<a class="nav-item nav-link bg-light stamp-nav" onclick="toStamppass();">打刻</a>
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
function toStamppass(){
	$(document).on('click','.stamp-nav',function(){
		window.location.href="/admin/stamp-pass";
	});
}
</script>
@endsection