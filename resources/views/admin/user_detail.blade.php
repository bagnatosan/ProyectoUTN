@extends('layouts.app')

@section('title', 'Detalle de Usuario | Admin')

@section('content')
<div class="w-full max-w-4xl mx-auto space-y-6">

    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-purple-400 transition-colors">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <span>Volver al panel</span>
    </a>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl px-5 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl px-5 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- ══════ Header del usuario ══════ --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold shrink-0
                    {{ $user->role === 'seller' ? 'bg-green-500/20 text-green-400' : ($user->role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400') }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-xl font-bold text-white">{{ $user->name }}</h1>
                        @if($user->role === 'admin')
                            <span class="px-2 py-1 rounded-full text-xs bg-purple-500/20 text-purple-400 border border-purple-500/30">Admin</span>
                        @elseif($user->role === 'seller')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400 border border-green-500/30">Emprendedor</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-blue-500/20 text-blue-400 border border-blue-500/30">Cliente</span>
                        @endif
                        @if($user->isSuspended())
                            <span class="px-2 py-1 rounded-full text-xs bg-rose-500/20 text-rose-400 border border-rose-500/30">Suspendido</span>
                        @endif
                    </div>
                    <p class="text-slate-400 text-sm mt-1">{{ $user->email }}</p>
                    <p class="text-slate-600 text-xs mt-1">Registrado el {{ $user->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            @if($user->role !== 'admin')
            <div class="flex items-center gap-2 shrink-0">
                <form action="{{ route('admin.users.suspend', $user) }}" method="POST"
                    onsubmit="return confirm('{{ $user->isSuspended() ? '¿Reactivar' : '¿Suspender' }} a {{ $user->name }}?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="text-sm px-4 py-2 rounded-lg transition-colors border
                            {{ $user->isSuspended()
                                ? 'text-emerald-400 hover:text-emerald-300 border-emerald-500/30'
                                : 'text-amber-400 hover:text-amber-300 border-amber-500/30' }}">
                        {{ $user->isSuspended() ? 'Reactivar cuenta' : 'Suspender cuenta' }}
                    </button>
                </form>
                <form action="{{ route('admin.users.delete', $user) }}" method="POST"
                    onsubmit="return confirm('¿Seguro que querés eliminar a {{ $user->name }}? Esta acción no se puede deshacer.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-rose-400 hover:text-rose-300 border border-rose-500/30 px-4 py-2 rounded-lg transition-colors">
                        Eliminar
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    @if($user->role === 'seller')
        {{-- ══════ Perfil de negocio ══════ --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Perfil de negocio</h2>
            @if($user->businessProfile)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Nombre comercial</p>
                        <p class="text-white font-medium">{{ $user->businessProfile->business_name }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-xs mb-1">Teléfono</p>
                        <p class="text-white font-medium">{{ $user->businessProfile->phone ?? '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-slate-500 text-xs mb-1">Dirección</p>
                        <p class="text-white font-medium">{{ $user->businessProfile->address ?? 'Sin dirección cargada' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-slate-500 text-xs mb-1">Descripción</p>
                        <p class="text-slate-300">{{ $user->businessProfile->description ?? '—' }}</p>
                    </div>
                </div>
            @else
                <p class="text-slate-500 text-sm italic">Este emprendedor todavía no completó su perfil de negocio.</p>
            @endif
        </div>

        {{-- ══════ Estadísticas ══════ --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-yellow-400">{{ $productsCount }}</p>
                <p class="text-slate-400 text-sm mt-1">Productos cargados</p>
            </div>
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-purple-400">{{ $reservationsCount }}</p>
                <p class="text-slate-400 text-sm mt-1">Reservas recibidas</p>
            </div>
        </div>

        {{-- ══════ Últimos productos ══════ --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-white">Últimos productos cargados</h2>
            </div>
            <ul class="divide-y divide-slate-800">
                @forelse($recentProducts as $product)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-yellow-400">${{ number_format($product->price, 0) }}</p>
                        <span class="text-xs {{ $product->is_active ? 'text-green-400' : 'text-slate-500' }}">
                            {{ $product->is_active ? '● Activo' : '○ Inactivo' }}
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-5 py-6 text-center text-slate-600 text-sm">Todavía no cargó ningún producto.</li>
                @endforelse
            </ul>
        </div>

        {{-- ══════ Últimas reservas recibidas ══════ --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-white">Últimas reservas recibidas</h2>
            </div>
            <ul class="divide-y divide-slate-800">
                @forelse($recentReservations as $reservation)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $reservation->product->name ?? 'Producto eliminado' }}</p>
                        <p class="text-xs text-slate-500">{{ $reservation->client_name }} &middot; {{ $reservation->reservation_date->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full shrink-0
                        {{ match($reservation->status) {
                            'pending' => 'bg-amber-500/20 text-amber-400 border border-amber-500/30',
                            'confirmed' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                            'completed' => 'bg-green-500/20 text-green-400 border border-green-500/30',
                            'cancelled' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
                            default => 'bg-slate-700/40 text-slate-400 border border-slate-600/30',
                        } }}">
                        {{ match($reservation->status) {
                            'pending' => 'Pendiente',
                            'confirmed' => 'Confirmada',
                            'completed' => 'Completada',
                            'cancelled' => 'Cancelada',
                            default => $reservation->status,
                        } }}
                    </span>
                </li>
                @empty
                <li class="px-5 py-6 text-center text-slate-600 text-sm">Todavía no recibió ninguna reserva.</li>
                @endforelse
            </ul>
        </div>

    @elseif($user->role === 'client')
        {{-- ══════ Perfil de cliente ══════ --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Datos del cliente</h2>
            <p class="text-slate-500 text-xs mb-1">Dirección</p>
            <p class="text-white font-medium text-sm">{{ $user->clientProfile->address ?? 'Sin dirección cargada' }}</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center max-w-xs">
            <p class="text-3xl font-bold text-purple-400">{{ $reservationsCount }}</p>
            <p class="text-slate-400 text-sm mt-1">Reservas realizadas</p>
        </div>

        {{-- ══════ Últimas reservas hechas ══════ --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-white">Últimas reservas realizadas</h2>
            </div>
            <ul class="divide-y divide-slate-800">
                @forelse($recentReservations as $reservation)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $reservation->product->name ?? 'Producto eliminado' }}</p>
                        <p class="text-xs text-slate-500">{{ $reservation->product->businessProfile->business_name ?? '—' }} &middot; {{ $reservation->reservation_date->format('d/m/Y') }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full shrink-0
                        {{ match($reservation->status) {
                            'pending' => 'bg-amber-500/20 text-amber-400 border border-amber-500/30',
                            'confirmed' => 'bg-blue-500/20 text-blue-400 border border-blue-500/30',
                            'completed' => 'bg-green-500/20 text-green-400 border border-green-500/30',
                            'cancelled' => 'bg-rose-500/20 text-rose-400 border border-rose-500/30',
                            default => 'bg-slate-700/40 text-slate-400 border border-slate-600/30',
                        } }}">
                        {{ match($reservation->status) {
                            'pending' => 'Pendiente',
                            'confirmed' => 'Confirmada',
                            'completed' => 'Completada',
                            'cancelled' => 'Cancelada',
                            default => $reservation->status,
                        } }}
                    </span>
                </li>
                @empty
                <li class="px-5 py-6 text-center text-slate-600 text-sm">Todavía no hizo ninguna reserva.</li>
                @endforelse
            </ul>
        </div>
    @endif

</div>
@endsection
