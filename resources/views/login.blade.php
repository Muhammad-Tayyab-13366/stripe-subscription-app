@extends('layout.auth-layout')
@section('content')
    <form action="{{ route('userLogin') }}" method="post">
        
        @csrf
        <h1>Login</h1>

        <fieldset>

            @session('success')
            <h2 style="color:green" for="name">{{ session('success')  }}</h2>
            @endsession
           
           
            <label for="mail">Email:</label>
            <input type="email" name="email" value="{{ old('email')}}" required>
            @error('email')
                <div class="error" style="color:red;">{{ $message }}</div>
            @enderror

            <label for="password">Password:</label>
            <input type="password" name="password" required>
            @error('password')
                <div class="error" style="color:red;">{{ $message }}</div>
            @enderror
        </fieldset>
        <button type="submit">Loagin</button>
        <br>
        <p>If you have no account? <a href="{{ route('register') }}">Sign Up Now</a></p>
    </form>
@endsection