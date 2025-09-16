@extends('auth.layout')

@section('title', 'Sign Up')

@section('content')
  <div class="white-box" style="max-height: 330px;">
    <form class="column-items" method="POST" action="{{ url('/signup') }}">
      <input class="text-box" style="margin-top: 60px;" type="text" placeholder="Name" name="name">
  	  <input class="text-box" type="email" placeholder="Email" name="email">
  	  <input class="text-box" type="password" placeholder="Password" name="password" minlength="8">
  	  <button class="user-auth-btn" type="submit">signup</button>
    	<div style="margin: auto;font-size: 12px;">
    		or <a style="text-decoration: none; color: #3C10FF;" href="{{ url('/login') }}">login</a>
    	</div>
    </form>
  </div>
@endsection