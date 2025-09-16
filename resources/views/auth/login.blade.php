@extends('auth.layout')

@section('title', 'Login')

@section('content')
  <div class="white-box" style="max-height: 260px;">
    <form class="column-items" method="POST" action="{{ Request::url() }}">
  	  <input class="text-box" style="margin-top: 60px;" type="email" placeholder="Email" name="email">
  	  <input class="text-box" type="password" placeholder="Password" name="password" minlength="8">
  	  <button class="user-auth-btn" type="submit">login</button>
    	<div style="margin: auto;font-size: 12px;">
    		or <a style="text-decoration: none; color: #3C10FF;" href="{{ url('/signup') }}">sign up</a>
    	</div>
    </form>
  </div>
@endsection