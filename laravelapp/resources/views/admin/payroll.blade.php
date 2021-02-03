@extends('layouts.base')

@section('title','給与計算書')

@section('link')
<link rel="stylesheet" href="{{asset('/css/payroll-print.css')}}" >
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/payroll.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
    	@slot('back_btn')
    	<div class="m-3 back" onclick="back();">&lt;&nbsp;戻る</div>
    	@endslot
		@slot('display_title')
		<div class="text-center mt-3">給与計算書</div>
        @endslot
    @endcomponent
	<div class="row mt-5">
		<div class="col-sm-12">
			<div class="float-left">株式会社〇〇 &nbsp;&nbsp;給与計算書&nbsp;&nbsp;山田太郎</div>
		</div>
	</div>
	@foreach( $data as $item )
	<div class="row avoid">
		<div class="col-sm-12">
			<table class="table border avoid">
			{{ $item[0] }}~{{ $item[1] }}
				<thead>
					<tr>
						<th>No,</th><th>氏名</th><th>時間</th><th>給与額</th>
						<th>交通費</th><th>振り込み金額</th><th>退職採用日</th><th>概要</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td rowspan="3" class="border no-span">{{ $item[2] }}</td><td rowspan="3" class="border name-span" style="width:13%;">{{ $item[4] }}</td><td class="border">{{ $item[6] }}</td><td class="border">{{ $item[8] }}</td>
						<td class="border">{{ $item[5] }}</td><td class="border"></td><td rowspan="3" class="border"></td><td rowspan="3" class="border"></td>
					</tr>
					<tr>
						<td class="border">{{ $item[7] }}</td><td class="border">{{ $item[9] }}</td><td class="border">{{ $item[11] }}</td><td class="border"></td>
					</tr>
					<tr>
						<td class="border sum-span">計</td><td class="border">{{ $item[10] }}</td><td class="border">{{ $item[13]}}</td><td class="border">{{ $item[12] }}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	@endforeach
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
</script>
@endsection