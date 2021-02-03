@extends('layouts.base')

@section('title','勤怠修正')

@section('link')
<link rel="stylesheet" href="{{asset('/css/attendance.css')}}" >
<link rel="stylesheet" href="{{asset('/css/base.css')}}">
@endsection

@section('content')
<div class="container-fluid">
    @component('components.header')
		@slot('back_btn')
		<div class="m-3 back" onclick="back();" data-id="{{ $data[0] }}">&lt;&nbsp;戻る</div>
		@endslot

		@slot('display_title')
		<div class="text-center">勤怠修正 &nbsp;({{ $data[1] }})</div>
		<div class="text-center mt-1">{{ $data[2] }}</div>
        @endslot

    @endcomponent
    <div class="row mt-3">
        <div class="col">
            <form method="post" action="{{ url('/admin/attendance-update') }}/{{ $data[0] }}/{{ $data[8] }}" onSubmit="return check()" novalidate>
            {{ csrf_field() }}
				<div class="form-row">
					<div class="col-sm-4">
						<div class="form-grop">
							<label for="date">出勤</label>
							<input type="datetime-local" class="form-control" name="go_time" id="go_time" value="{{ $data[3] }}" disabled>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-grop">
							<label for="date">出勤</label>
							<input type="datetime-local" class="form-control text-danger @if(session('flash_message')) is-invalid @endif @error('go_time') is-invalid @enderror"
							name="go_time" id="go_time" value="{{ $data[3] }}">
							@error('go_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('go_time') }}</strong>
                                    </span>
                            @enderror
                            <!-- フラッシュメッセージ -->
                            @if (session('flash_message'))
                                <span class="flash_message invalid-feedback font-weight-bold" role="alert">
                                    {{ session('flash_message') }}
                                </span>
                            @endif
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-grop">
							<label for="date">休憩入り時間</label>
							<input type="datetime-local" class="form-control" name="break_in" id="break_in" value="{{ $data[4] }}" disabled>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-grop">
							<label for="date">休憩入り時間</label>
							<input type="datetime-local" class="form-control text-danger" name="break_in" id="break_in" value="{{ $data[4] }}">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-grop">
							<label for="date">休憩終わり時間</label>
							<input type="datetime-local" class="form-control" name="break_out" id="break_out" value="{{ $data[5] }}" disabled>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-grop">
							<label for="date">休憩終わり時間</label>
							<input type="datetime-local" class="form-control text-danger" name="break_out" id="break_out" value="{{ $data[5] }}">
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-grop">
							<label for="date">退勤</label>
							<input type="datetime-local" class="form-control" name="out_time" id="out_time" value="{{ $data[6] }}" disabled>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-grop">
							<label for="date">退勤</label>
							<input type="datetime-local" class="form-control text-danger @error('out_time') is-invalid @enderror" name="out_time" id="out_time" value="{{ $data[6] }}">
							@error('out_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('out_time') }}</strong>
                                    </span>
                            @enderror
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-grop">
							<label for="date">交通費</label>
							<input type="number" step="10" class="form-control" name="expense" id="expense" value="{{ $data[7] }}" disabled>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-grop">
							<label for="date">交通費</label>
							<input type="number" step="10" class="form-control text-danger @error('expense') is-invalid @enderror"" name="expense" id="expense" value="{{ $data[7] }}">
							@error('expense')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('expense') }}</strong>
                                    </span>
                            @enderror
						</div>
					</div>
					<div class="col-12">
						<div class="form-grop">
							<input type="submit" name="update-btn" class="btn btn-warning my-3 ml-3 float-right" value="変更">
							<input type="submit" name="delete-btn" class="btn btn-warning m-3 float-right" value="削除">
						</div>
					</div>
				</div>
			</form>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
//(<戻る)
function back(){
	$(document).on('click','.back',function(){
		var id = $(this).data('id');
		window.location.href="/admin/attendance-record/" + id;
	});
}

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