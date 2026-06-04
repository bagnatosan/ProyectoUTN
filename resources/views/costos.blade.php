<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Costos - ProyectoUTN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        {!! $estilosAmigo !!}
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-bg': '#0b0f19',
                        'dark-card': '#13192b',
                        'laravel-green': '#00c853',
                        'dark-input': '#1e253b'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-bg text-white font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-dark-card border-r border-gray-800/80 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-8">
                    <span class="bg-laravel-green text-black font-bold px-2 py-1 rounded">P</span>
                    <h1 class="text-lg font-bold tracking-wide">ProyectoUTN</h1>
                </div>
                <nav class="space-y-2">
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Dashboard</a>
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Productos</a>
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Reservas</a>
                    <a href="#" class="block py-2 px-4 rounded text-laravel-green bg-gray-800/40 font-medium border-l-4 border-laravel-green pl-3">Costos</a>
                    <a href="#" class="block py-2 px-4 rounded text-gray-400 hover:bg-gray-800/50 hover:text-white transition">Perfil</a>
                </nav>
            </div>
            <button class="text-left text-sm text-gray-500 hover:text-red-400 py-2 px-4 transition">Cerrar Sesión</button>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto">
            <div class="mb-8">
                <h2 class="text-2xl font-bold tracking-tight">Módulo de costos</h2>
                <p class="text-gray-400 text-sm mt-1">Calculá el costo real de tus productos y tu margen de ganancia</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-dark-card p-6 rounded-xl border border-gray-800/60 shadow-2xl">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-laravel-green tracking-wide">Materias primas</h3>
                        <button class="bg-laravel-green hover:bg-green-600 text-black font-bold px-4 py-1.5 rounded-lg text-sm transition shadow-lg shadow-green-900/20">+ Nuevo</button>
                    </div>
                    <div class="overflow-x-auto rounded-lg border border-gray-800/40">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-900/40 border-b border-gray-800 text-gray-400 font-medium">
                                    <th class="py-3 px-4">INGREDIENTE</th>
                                    <th class="py-3 px-4">UNIDAD</th>
                                    <th class="py-3 px-4">$$ COSTO</th>
                                    <th class="py-3 px-4 text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800/40 text-gray-300">
                                @foreach ($ingredients as $ingredient)
                                    <tr class="border-b border-gray-800/60 hover:bg-gray-800/40 transition">
                                        <td class="py-3 px-4 font-medium">{{ $ingredient->name }}</td>
                                        <td class="py-3 px-4 text-gray-400">{{ $ingredient->unit_measure }}</td>
                                        <td class="py-3 px-4 font-semibold text-emerald-400">$ {{ number_format($ingredient->unit_cost, 2, ',', '.') }}</td>
                                        <td class="py-3 px-4 text-center space-x-3">
                                            <button class="text-blue-400 hover:text-blue-300 transition">Editar</button>
                                            <button class="text-red-400 hover:text-red-300 transition">❌</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-dark-card p-6 rounded-xl border border-gray-800/60 flex flex-col justify-between shadow-2xl">
                    <div>
                        <h3 class="text-lg font-semibold text-laravel-green tracking-wide mb-1">Calculadora - Torta de chocolate</h3>
                        <p class="text-xs text-gray-400 mb-6">Ingredientes asociados al producto</p>
                        
                        <div class="space-y-3 mb-6 bg-gray-900/20 p-4 rounded-lg border border-gray-800/40">
                            <div class="flex justify-between text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-800 pb-2">
                                <span>Ingrediente</span>
                                <span>Cantidad</span>
                                <span>Subtotal</span>
                            </div>
                            <div class="flex justify-between text-sm py-1">
                                <span class="text-gray-300">Harina 0000</span>
                                <span class="text-gray-400">0.5 kg</span>
                                <span class="font-medium text-emerald-400">$600,00</span>
                            </div>
                            <div class="flex justify-between text-sm py-1">
                                <span class="text-gray-300">Huevos</span>
                                <span class="text-gray-400">4 unidades</span>
                                <span class="font-medium text-emerald-400">$800,00</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-dark-input p-5 rounded-xl space-y-3 border border-gray-800/80 shadow-inner">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">COSTO TOTAL:</span>
                            <span class="font-semibold text-white">$1.400,00</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">MARGEN DE GANANCIA:</span>
                            <span class="font-medium text-laravel-green bg-green-950/40 px-2 py-0.5 rounded text-xs border border-green-800/30">60%</span>
                        </div>
                        <div class="flex justify-between text-base border-t border-gray-700/60 pt-3 mt-1">
                            <span class="font-medium text-gray-200">PRECIO SUGERIDO:</span>
                            <span class="font-bold text-xl text-laravel-green tracking-wide">$3.500,00</span>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
</body>
</html>