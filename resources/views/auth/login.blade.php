@extends('auth.layout')

@section('title', 'Login')

@section('content')
  <div class="white-box" style="max-height: 260px;">
    <form class="column-items" method="POST" action="{{ route('login') }}">
      {{ csrf_field() }}
  	  <input class="text-box" style="margin-top: 60px;" type="email" placeholder="Email" name="email" value="{{ old('email') }}" required autofocus>
      @if($errors->has('email'))
        <script>Toast('{{ $errors->first('email') }}', 'error');</script>
      @endif
  	  <input class="text-box" type="password" placeholder="Password" name="password" minlength="8" required>
      @if($errors->has('password'))
        <script>Toast('{{ $errors->first('password') }}', 'error');</script>
      @endif
  	  <button class="user-auth-btn" type="submit">login</button>
    	<div style="margin: auto;font-size: 12px;">
    		or <a style="text-decoration: none; color: #3C10FF;" href="{{ route('register') }}">register</a>
    	</div>
    </form>
  </div>
@endsection