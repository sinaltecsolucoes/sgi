<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- CSS DO DATATABLES --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />

    {{-- CSS DA EXTENSÃO RESPONSIVE ABAIXO --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.dataTables.css" />

</head>

<body class="font-sans antialiased">
    <div x-data class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="mx-auto sm:px-6 lg:px-8"> {{-- max-w-7xl removido daqui --}}
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Sucesso!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'Ok'
            })
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Erro!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'Ok'
            })
        </script>
    @endif

    {{-- JQUERY E JS DO DATATABLES --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>

    {{-- ADICIONE O JS DA EXTENSÃO RESPONSIVE ABAIXO --}}
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.js"></script>


    @stack('scripts')

</body>

</html>