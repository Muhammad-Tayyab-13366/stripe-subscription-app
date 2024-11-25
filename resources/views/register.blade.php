@extends('layout.auth-layout')
@section('content')
    <form action="{{ route('userRegister') }}" method="post">
        
        @csrf
        <h1>Sign Up</h1>

        <fieldset>

            @session('success')
            <h2 style="color:green" for="name">{{ session('success')  }}</h2>
            @endsession
           
            <label for="name">Name:</label>
            <input type="text" name="name" value="{{ old('name')}}" required>

            @error('name')
                <div class="error" style="color:red;">{{ $message }}</div>
            @enderror
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
        <button type="submit">Sign Up</button>
        <br>
        <p>Already has account? <a href="{{ route('login') }}">Login Now</a></p>
    </form>
@endsection