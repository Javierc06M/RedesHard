<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('resources/css/sidebar.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @switch(Route::currentRouteName())
        @case('ventas.index')
            <link rel="stylesheet" href="{{ asset('resources/css/ventas.css') }}">
            @break

        @case('admin.profile')
            <link rel="stylesheet" href="{{ asset('resources/css/profile.css') }}">
            @break

        @default
            {{-- Sin estilos adicionales --}}
    @endswitch


</head>
<body>
    
    @include('layouts.partials.sidebar')

    @yield('content')


    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('resources/js/sidebar.js') }}"></script>

    @switch(Route::currentRouteName())
        @case('ventas.index')
            <script src="{{ asset('resources/js/ventas.js') }}"></script>
            @break
        @case('admin.profile')
            <script src="{{ asset('resources/js/profile.js') }}"></script>
            @break
    
        @default
            
    @endswitch
</body>
</html>