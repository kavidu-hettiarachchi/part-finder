<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Part Finder</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-800">
        <div class="bg-gray-800">
            <div class="relative min-h-screen flex flex-col items-center justify-center">
                <div class="mb-12">
                    <a href="/">
                        <img src="https://www.partxa.com/_next/image?url=https%3A%2F%2Fassets.partxa.com%2Fimg%2Flogo%2Fpartxa-logo-white-42-tm.png&amp;w=384&amp;q=75">
                    </a>
                </div>



                <div class="mb-12">
                    <h1 class="text-white text-3xl">Part Finder</h1>
                </div>
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">

                        @if (Route::has('login'))
                            <nav class="-mx-3 flex flex-1 justify-center">
                                @auth
                                    <a class="inline-block w-full md:w-auto px-6 py-3 m-6 font-medium text-black bg-white hover:bg-gray-200 rounded transition duration-200"  href="{{ url('/dashboard') }}">Dashboard</a>

                                @else
                                    <a class="inline-block w-full md:w-auto px-6 py-3 m-6 font-medium text-black bg-white hover:bg-gray-200 rounded transition duration-200"  href="{{ route('login') }}">Log in</a>

                                    @if (Route::has('register'))
                                        <a class="inline-block w-full md:w-auto px-6 py-3 m-6 font-medium text-black bg-white hover:bg-gray-200 rounded transition duration-200"  href="{{ route('register') }}">Register</a>
                                    @endif
                                @endauth
                            </nav>
                        @endif



                </div>
            </div>
        </div>
    </body>
</html>
