<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
	<title>@yield('title')</title>
	<link rel="stylesheet" href="{{asset('/css/bootstrap.min.css')}}" >
	@yield('link')

	<script src="{{asset('/js/jquery.min.js')}}"></script>
	<script src="{{asset('/js/popper.min.js')}}"></script>
	<script src="{{asset('/js/bootstrap.min.js')}}"></script>
</head>
<body>
	@yield('content')
</body>
@yield('script')
</html>