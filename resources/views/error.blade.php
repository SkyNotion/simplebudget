<!DOCTYPE html>
<html>
  <head>
  	<title>{{ $status }} | SimpleBudget</title>
      @include('font')
    <style>
      body {
        font-family: var(--font);
      }
    </style>
  </head>
  <body>
  	<center>
  		<h1>{{ $status }} {{ $message }}</h1>
  	</center>
  	<hr>
  	<center>SimpleBudget</center>
  </body>
</html>