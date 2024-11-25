<html>
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Trial & Subscription Project</title>
      <link href='https://fonts.googleapis.com/css?family=Nunito:400,300' rel='stylesheet' type='text/css'>
      <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  </head>
  <body>

    @auth
      <header style="background: #eee; padding:15px;">
        <div style=" display: flex;justify-content: space-around;align-items: center;">
          <h1 style="margin:0px;">Welcome, {{ Auth::user()->name }}</h1>
          <span><a href="{{ route('logout') }}">Logout</a></span>
        </div>
      
      </header>
    @endauth

    @yield('content')
  </body>
</html>
