<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>401 - No autorizado</title>
    
    <!-- Tailwind CSS (opcional) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Iconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .bg-unauthorized {
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
    </style>
</head>
<body class="bg-unauthorized min-h-screen flex items-center">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden p-8">
            <div class="text-center">
                <div class="text-9xl font-bold text-red-500 mb-6">401</div>
                <h1 class="text-2xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-lock mr-2"></i> Acceso no autorizado
                </h1>
                <p class="text-gray-600 mb-8">
                    {{ $exception->getMessage() ?: 'No tienes permisos para acceder a esta página.' }}
                </p>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ url('/') }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                        <i class="fas fa-home mr-2"></i> Ir al inicio
                    </a>
                    
                    @auth
                    <a href="{{ url()->previous() !== url()->current() && !str_contains(url()->previous(), '/usuario/permisos') ? url()->previous() : route('home') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver atrás
                    </a>
                    @else
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i> Iniciar sesión
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</body>
</html>