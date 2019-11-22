<!DOCTYPE html>
<html>
<head>
{{--  <title>Weibo App</title>--}}
  {{--优化，在其它网页没有标题时默认使用Weibo App--}}
  <title>@yield('title', 'Weibo App')-- Laravel 新手入门教程</title>
</head>
<body>
@yield('content')
</body>
</html>
