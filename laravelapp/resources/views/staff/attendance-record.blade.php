@extends('layouts.base')

@section('title','勤怠一覧')

@section('link')
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/attendance.css')}}">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('display_title')
		<div class="text-center">勤怠一覧</div>
        <div class="text-center mt-1">{{ $info[0] }}</div>
        @endslot
		@slot('logout')
		<div class="m-3 float-right">
			<span id="logout" onclick="toLogin();"><i class="fas fa-sign-out-alt">&nbsp;退出</i></span>
		</div>
		@endslot
    @endcomponent
    <div class="row mt-3">
        <div class="col">
            <nav class="nav nav-tabs nav-pills nav-fill">
                <a class="nav-item nav-link table-light record-nav" onclick="toLast();">先月</a>
                <a class="nav-item nav-link active table-info">今月</a>
                <a class="nav-item nav-link table-light record-nav" onclick="toNext();">来月</a>
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
					<tr>
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
	@component('components.navbar')
	@slot('nav')
	<a class="nav-item nav-link acitve bg-info">勤怠</a>
    <a class="nav-item nav-link bg-light shift-nav" onclick="toshift();">シフト</a>
    <a class="nav-item nav-link bg-light stamp-nav" onclick="toStamp();">打刻</a>
    @endslot
	@endcomponent
</div>
@endsection
@section('script')
<script src="{{asset('/js/attendance.js')}}"></script>
@endsection