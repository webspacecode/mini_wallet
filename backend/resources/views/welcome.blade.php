<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="flex justify-center pt-8 sm:justify-center sm:pt-0">
                    <a href="/wallet" >
                        <h1 class="text-white font-bold text-2xl p-5 bg-purple-700 rounded-full">
                            Try Mini Wallet
                        </h1>
                    </a>
                    
                </div>
                
                <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                    <div class="flex justify-center pt-8 sm:justify-center sm:pt-0">
                        <div class="p-6">
                            

                            <div class="">
                                <div class="mt-2 text-gray-600 dark:text-gray-400 text-2xl">
                                    Mini Wallet is a test wallet to transfer imaginary money to your freind
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </body>
</html>
