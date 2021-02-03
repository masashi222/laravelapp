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
	<div class="stamp-frame">
		<div class="row">
			<div class="col-sm-12">
				<h3 id="stamp-date" class="text-center">
					<!-- stamp.js -->
				</h3>
				<h3 id="stamp-time" class="text-center">
					<!-- stamp.js -->
				</h3>
			</div>
			<div class="col-sm-12 my-2">
				<form method="post" action="{{ url('/staff/stamp') }}" onSubmit="return check()" novalidate>
				{{ csrf_field() }}
					<div class="form-row">
						<div class="col">
							<div class="form-grop my-1 mx-auto w-75">
								<label for="select">勤務者</label>
								<select class="form-control" id="memberid" name="memberid">
										<option value="{{ $member->id }}" selected>{{ $member->name }}</option>
								</select>
							</div>
							<div class="form-grop my-1 mx-auto w-75">
								<label for="expense">交通費</label>
								<input type="number" step="10" class="form-control @error('expense') is-invalid @enderror" id="expense" name="expense" value="{{ $expense }}">
								@error('expense')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('expense') }}</strong>
                                    </span>
                            	@enderror
							</div>
							@if( $display == 1 )
							<div class="form-grop my-1 mx-auto w-75">
								<label for="key">打刻キー</label>
								<input type="number" step="10" class="form-control @if(session('flash_message')) is-invalid @endif @error('key') is-invalid @enderror" id="key" name="key">
								@error('key')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('key') }}</strong>
                                    </span>
                            	@enderror
                            	<!-- フラッシュメッセージ -->
                                @if (session('flash_message'))
                                    <span class="flash_message invalid-feedback font-weight-bold" role="alert">
                                        {{ session('flash_message') }}
                                    </span>
                                @endif
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="go_btn" name="go_btn" class="btn btn-warning w-75" value="出勤" onclick="getTime();">
								<input type="hidden" id="go_time" name="go_time" value="">
								<input type="hidden" id="go_flg" name="go_flg" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakIn_btn" name="breakIn_btn" class="btn btn-outline-warning w-75" value="休憩入り" onclick="getTime();" disabled>
								<input type="hidden" id="break_in" name="break_in" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakOut_btn" name="breakOut_btn" class="btn btn-outline-warning w-75" value="休憩終わり" onclick="getTime();" disabled>
								<input type="hidden" id="break_out" name="break_out" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="out_btn" name="out_btn" class="btn btn-warning w-75" value="退勤" onclick="getTime();" disabled>
								<input type="hidden" id="out_time" name="out_time" value="">
							</div>
							@endif
							@if( $display == 2 )
							<div class="form-group my-4 text-center">
								<input type="submit" id="go_btn" name="go_btn" class="btn btn-warning w-75" value="出勤" onclick="getTime();" disabled>
								<input type="hidden" id="go_time" name="go_time" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakIn_btn" name="breakIn_btn" class="btn btn-outline-warning w-75" value="休憩入り" onclick="getTime();">
								<input type="hidden" id="break_in" name="break_in" value="">
								<input type="hidden" id="in_flg" name="in_flg" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakOut_btn" name="breakOut_btn" class="btn btn-outline-warning w-75" value="休憩終わり" onclick="getTime();" disabled>
								<input type="hidden" id="break_out" name="break_out" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="out_btn" name="out_btn" class="btn btn-warning w-75" value="退勤" onclick="getTime();">
								<input type="hidden" id="out_time" name="out_time" value="">
								<input type="hidden" id="out_flg" name="out_flg" value="">
							</div>
							@endif
							@if( $display == 3 )
							<div class="form-group my-4 text-center">
								<input type="submit" id="go_btn" name="go_btn" class="btn btn-warning w-75" value="出勤" onclick="getTime();" disabled>
								<input type="hidden" id="go_time" name="go_time" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakIn_btn" name="breakIn_btn" class="btn btn-outline-warning w-75" value="休憩入り" onclick="getTime();" disabled>
								<input type="hidden" id="break_in" name="break_in" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakOut_btn" name="breakOut_btn" class="btn btn-outline-warning w-75" value="休憩終わり" onclick="getTime();">
								<input type="hidden" id="break_out" name="break_out" value="">
								<input type="hidden" id="fin_flg" name="fin_flg" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="out_btn" name="out_btn" class="btn btn-warning w-75" value="退勤" onclick="getTime();" disabled>
								<input type="hidden" id="out_time" name="out_time" value="">
							</div>
							@endif
							@if( $display == 4 )
							<div class="form-group my-4 text-center">
								<input type="submit" id="go_btn" name="go_btn" class="btn btn-warning w-75" value="出勤" onclick="getTime();" disabled>
								<input type="hidden" id="go_time" name="go_time" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakIn_btn" name="breakIn_btn" class="btn btn-outline-warning w-75" value="休憩入り" onclick="getTime();" disabled>
								<input type="hidden" id="break_in" name="break_in" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="breakOut_btn" name="breakOut_btn" class="btn btn-outline-warning w-75" value="休憩終わり" onclick="getTime();" disabled>
								<input type="hidden" id="break_out" name="break_out" value="">
							</div>
							<div class="form-group my-4 text-center">
								<input type="submit" id="out_btn" name="out_btn" class="btn btn-warning w-75" value="退勤" onclick="getTime();">
								<input type="hidden" id="out_time" name="out_time" value="">
								<input type="hidden" id="out_flg" name="out_flg" value="">
								<input type="hidden" id="in_flg" name="in_flg" value="" disabled>
							</div>
							@endif
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	@component('components.navbar')
    	@slot('nav')
    	<a class="nav-item nav-link bg-light attendance-nav" onclick="toAttendance();">勤怠</a>
        <a class="nav-item nav-link bg-light shift-nav" onclick="toshift();">シフト</a>
        <a class="nav-item nav-link active bg-info">打刻</a>
        @endslot
    	@endcomponent
</div>
@endsection

@section('script')
<script src="{{asset('/js/stamp.js')}}"></script>
@endsection