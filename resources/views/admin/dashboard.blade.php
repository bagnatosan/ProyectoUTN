@extends('layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8">

    <!-- Título -->
    <div class="text-center">
        <span class="text-xs font-semibold tracking-widest text-purple-400 uppercase">Panel de Control</span>
        <h1 class="text-4xl font-bold text-white mt-2">Dashboard <span class="text-purple-400">Administrador</span></h1>
        <p class="text-slate-400 mt-2">Gestión global de la plataforma</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Usuarios totales</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-blue-400">{{ $stats['total_clients'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Clientes</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-green-400">{{ $stats['total_sellers'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Emprendedores</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-yellow-400">{{ $stats['total_products'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Productos</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-purple-400">{{ $stats['total_reservations'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Reservas</p>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-lg font-semibold text-white">Usuarios registrados</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="bg-slate-800 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Rol</th>
                        <th class="px-6 py-3 text-left">Registro</th>
                        <th class="px-6 py-3 text-left">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">{{ $user->id }}</td>
                        <td class="px-6 py-4 font-medium text-white">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2 py-1 rounded-full text-xs bg-purple-500/20 text-purple-400 border border-purple-500/30">Admin</span>
                            @elseif($user->role === 'seller')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400 border border-green-500/30">Emprendedor</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400 border border-blue-500/30">Cliente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($user->role !== 'admin')
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que querés eliminar a {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 border border-rose-500/30 px-3 py-1 rounded-lg transition-colors">
                                    Eliminar
                                </button>
                            </form>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection@extends('layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8">

    <!-- Título -->
    <div class="text-center">
        <span class="text-xs font-semibold tracking-widest text-purple-400 uppercase">Panel de Control</span>
        <h1 class="text-4xl font-bold text-white mt-2">Dashboard <span class="text-purple-400">Administrador</span></h1>
        <p class="text-slate-400 mt-2">Gestión global de la plataforma</p>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-white">{{ $stats['total_users'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Usuarios totales</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-blue-400">{{ $stats['total_clients'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Clientes</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-green-400">{{ $stats['total_sellers'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Emprendedores</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-yellow-400">{{ $stats['total_products'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Productos</p>
        </div>
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
            <p class="text-3xl font-bold text-purple-400">{{ $stats['total_reservations'] }}</p>
            <p class="text-slate-400 text-sm mt-1">Reservas</p>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800">
            <h2 class="text-lg font-semibold text-white">Usuarios registrados</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="bg-slate-800 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Rol</th>
                        <th class="px-6 py-3 text-left">Registro</th>
                        <th class="px-6 py-3 text-left">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">{{ $user->id }}</td>
                        <td class="px-6 py-4 font-medium text-white">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-2 py-1 rounded-full text-xs bg-purple-500/20 text-purple-400 border border-purple-500/30">Admin</span>
                            @elseif($user->role === 'seller')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400 border border-green-500/30">Emprendedor</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400 border border-blue-500/30">Cliente</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            @if($user->role !== 'admin')
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que querés eliminar a {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 border border-rose-500/30 px-3 py-1 rounded-lg transition-colors">
                                    Eliminar
                                </button>
                            </form>
                            @else
                                <span class="text-slate-600 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection