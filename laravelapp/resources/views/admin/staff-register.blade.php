@extends('layouts.base')

@section('title','従業員登録')

@section('link')
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
<link rel="stylesheet" href="{{asset('/css/staff.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
    	@slot('back_btn')
		<div class="m-3 back" onclick="back();">&lt;&nbsp;戻る</div>
    	@endslot
		@slot('display_title')
		<div class="text-center mt-3">従業員登録</div>
        @endslot

    @endcomponent
	<div class="row mt-3">
		<div class="col">
			<form method="post" action="{{url('/admin/staff-register')}}" class="form-horizontal" novalidate>
			{{ csrf_field() }}
				<div class="form-group row ">
				    <label for="number" class="control-label col-sm-2">従業員番号</label>
				    <div class="col-sm-10">
			      		<input type="number" name="number" class="form-control @error('number') is-invalid @enderror" value="{{old('number')}}">
				      	@error('number')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('number') }}</strong>
                            </span>
						@enderror
				    </div>
			  	</div>
				<div class="form-group row ">
				    <label for="name" class="control-label col-sm-2">Name</label>
				    <div class="col-sm-10">
			      		<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{old('name')}}">
				      	@error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
						@enderror
				    </div>
			  	</div>
		  		<div class="form-group row ">
				    <label for="pass" class="control-label col-sm-2">Password</label>
				    <div class="col-sm-10">
				    	<input id="pass" type="password" name="pass" class="form-control @error('pass') is-invalid @enderror" value="{{old('pass')}}">
				    	@error('pass')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('pass') }}</strong>
                            </span>
						@enderror
				    </div>
				    <div class="col-12">
				   		<label for="passcheck" class="text-muted" style="font-size:0.75em;">パスワードを表示する</label>
    					<input type="checkbox" id="passcheck"/>
				    </div>
			  	</div>
			  	<div class="form-group row ">
				    <label for="expense" class="control-label col-sm-2">交通費</label>
				    <div class="col-sm-10">
			      		<input type="number" step="10" min="0" name="expense" class="form-control @error('expense') is-invalid @enderror" placeholder="(固定)"  value="{{old('expense')}}">
				      	@error('expense')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('expense') }}</strong>
                            </span>
						@enderror
				    </div>
			  	</div>
			  	<div class="form-group row ">
				    <label for="salary" class="control-label col-sm-2">時給</label>
				    <div class="col-sm-10">
			      		<input type="number" step="10" min="0" name="salary" class="form-control @error('salary') is-invalid @enderror" value="{{old('salary')}}">
				      	@error('salary')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('salary') }}</strong>
                            </span>
						@enderror
				    </div>
			  	</div>
			  	<div class="form-group row">
				    <label for="business" class="control-label col-sm-2">業務形態</label>
				    <div class="col-sm-10">
				    	<select class="form-control" name="business_no">
							<option value="1">1.オーナー</option>
							<option value="2">2.正社員</option>
							<option value="3">3.アルバイト</option>
						</select>
				    </div>
			  	</div>
			  	<div class="form-group">
				      <input type="submit" class="btn btn-warning my-3 ml-3 float-right" value="登録">
			  	</div>
			</form>
		</div>
	</div>
</div>
@endsection
@section('script')
<script>
function back(){
	$(document).on('click','.back',function(){
		window.location.href="/admin/staff-record";
	});
}

//パスワードの表示・非表示
$(function() {
    var password  = '#pass';
    var passcheck = '#passcheck';

    $(passcheck).change(function() {
        if ($(this).prop('checked')) {
            $(password).attr('type','text');
        } else {
            $(password).attr('type','password');
        }
    });
});
</script>
@endsection