@extends('auth.layout')

@section('title', 'Sign Up')

@section('content')
  <div class="white-box" style="max-height: 330px;">
    <form class="column-items" method="POST" action="{{ route('register') }}">
      {{ csrf_field() }}
      <input class="text-box" style="margin-top: 60px;" type="text" placeholder="Name" name="name" value="{{ old('name') }}" required autofocus>
      @if($errors->has('name'))
        <script>Toast('{{ $errors->first('name') }}', 'error');</script>
      @endif
  	  <input class="text-box" type="email" placeholder="Email" name="email" value="{{ old('email') }}" required>
      @if($errors->has('email'))
        <script>Toast('{{ $errors->first('email') }}', 'error');</script>
      @endif
  	  <input class="text-box" type="password" placeholder="Password" name="password" minlength="8" required>
      @if($errors->has('password'))
        <script>Toast('{{ $errors->first('password') }}', 'error');</script>
      @endif
  	  <button class="user-auth-btn" type="submit">register</button>
    	<div style="margin: auto;font-size: 12px;">
    		or <a style="text-decoration: none; color: #3C10FF;" href="{{ route('login') }}">login</a>
    	</div>
    </form>
  </div>
@endsection