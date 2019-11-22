<!DOCTYPE html>
<html>
<head>
{{--  <title>Weibo App</title>--}}
  {{--优化，在其它网页没有标题时默认使用Weibo App--}}
  <title>@yield('title', 'Weibo App')-- Laravel 新手入门教程</title>

  <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
@include('layouts._header')

<div class="container">
  <div class="offset-md-1 col-md-10">
    @yield('content')
    @include('layouts._footer')
  </div>
</div>

</body>
</html>
