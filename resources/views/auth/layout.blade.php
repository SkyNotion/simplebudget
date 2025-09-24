<!doctype html>
<html>
  <head>
    <title>@yield('title') | SimpleBudget</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
    @include('font')
    @include('element.toast')
  </head>
  <body>
    @yield('content')
  </body>
</html>