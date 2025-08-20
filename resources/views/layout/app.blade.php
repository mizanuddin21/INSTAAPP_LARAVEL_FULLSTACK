<!DOCTYPE html>
<html>
<head>
    <title>InstaApp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
        <div class="container d-flex align-items-center">
            <div class="me-2">
                <img src="{{ asset('Instagram-Logo.png') }}" alt="avatar" class="rounded-circle" width="40">
            </div>
            <a class="navbar-brand" href="{{ route('posts.index') }}">InstaApp</a>
            <div class="ms-auto">
                @auth
                    <span>{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger ms-2">Logout</button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-success ms-2">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    </script>

    @yield('scripts')
</body>
</html>
