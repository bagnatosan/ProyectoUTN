@extends('layouts.app')

@section('title', 'Panel Administrador')

@section('content')
<div class="w-full max-w-6xl mx-auto space-y-8">

    {{-- Título --}}
    <div class="text-center">
        <span class="text-xs font-semibold tracking-widest text-purple-400 uppercase">Panel de Control</span>
        <h1 class="text-4xl font-bold text-white mt-2">Dashboard <span class="text-purple-400">Administrador</span></h1>
        <p class="text-slate-400 mt-2">Gestión global de la plataforma</p>
    </div>

    {{-- Alertas de sesión --}}
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

    {{-- ══════════════════════════════════════════
         FILA 1: MÉTRICAS DE USUARIOS
         ══════════════════════════════════════════ --}}
    <div>
        <p class="text-xs font-semibold tracking-widest text-slate-500 uppercase mb-3">Usuarios</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <button onclick="toggleTable('users', 'all')"
                class="metric-card group bg-slate-900 border border-slate-800 hover:border-white/30 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="users" data-role="all">
                <p class="text-3xl font-bold text-white group-hover:scale-105 transition-transform">{{ $stats['total_users'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Usuarios totales</p>
                <p class="text-slate-600 text-xs mt-2">Ver todos →</p>
            </button>

            <button onclick="toggleTable('users', 'client')"
                class="metric-card group bg-slate-900 border border-slate-800 hover:border-blue-400/50 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="users" data-role="client">
                <p class="text-3xl font-bold text-blue-400 group-hover:scale-105 transition-transform">{{ $stats['total_clients'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Clientes</p>
                <p class="text-slate-600 text-xs mt-2">Ver lista →</p>
            </button>

            <button onclick="toggleTable('users', 'seller')"
                class="metric-card group bg-slate-900 border border-slate-800 hover:border-green-400/50 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="users" data-role="seller">
                <p class="text-3xl font-bold text-green-400 group-hover:scale-105 transition-transform">{{ $stats['total_sellers'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Emprendedores</p>
                <p class="text-slate-600 text-xs mt-2">Ver lista →</p>
            </button>

            <button onclick="toggleTable('users', 'seller_no_products')"
                class="metric-card group bg-slate-900 border {{ $stats['sellers_no_products'] > 0 ? 'border-amber-500/40 bg-amber-500/5' : 'border-slate-800' }} hover:border-amber-400/50 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="users" data-role="seller_no_products">
                <p class="text-3xl font-bold {{ $stats['sellers_no_products'] > 0 ? 'text-amber-400' : 'text-slate-500' }} group-hover:scale-105 transition-transform">
                    {{ $stats['sellers_no_products'] }}
                </p>
                <p class="text-slate-400 text-sm mt-1">Sin productos</p>
                <p class="text-slate-600 text-xs mt-2">{{ $stats['sellers_no_products'] > 0 ? '⚠ Ver quiénes →' : 'Todo en orden ✓' }}</p>
            </button>

        </div>
    </div>

    {{-- ══════════════════════════════════════════
         FILA 2: MÉTRICAS DE CONTENIDO
         ══════════════════════════════════════════ --}}
    <div>
        <p class="text-xs font-semibold tracking-widest text-slate-500 uppercase mb-3">Contenido</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <button onclick="toggleTable('products', 'all')"
                class="metric-card group bg-slate-900 border border-slate-800 hover:border-yellow-400/50 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="products" data-role="all">
                <p class="text-3xl font-bold text-yellow-400 group-hover:scale-105 transition-transform">{{ $stats['total_products'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Productos totales</p>
                <p class="text-slate-600 text-xs mt-2">Ver todos →</p>
            </button>

            <button onclick="toggleTable('products', 'active')"
                class="metric-card group bg-slate-900 border border-slate-800 hover:border-green-400/50 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="products" data-role="active">
                <p class="text-3xl font-bold text-green-400 group-hover:scale-105 transition-transform">{{ $stats['active_products'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Productos activos</p>
                <p class="text-slate-600 text-xs mt-2">Ver lista →</p>
            </button>

            <button onclick="toggleTable('products', 'inactive')"
                class="metric-card group bg-slate-900 border border-slate-800 hover:border-slate-400/50 rounded-xl p-5 text-center transition-all duration-200 cursor-pointer"
                data-section="products" data-role="inactive">
                <p class="text-3xl font-bold text-slate-400 group-hover:scale-105 transition-transform">{{ $stats['inactive_products'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Productos inactivos</p>
                <p class="text-slate-600 text-xs mt-2">Ver lista →</p>
            </button>

            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 text-center">
                <p class="text-3xl font-bold text-purple-400">{{ $stats['total_reservations'] }}</p>
                <p class="text-slate-400 text-sm mt-1">Reservas totales</p>
                @if($stats['pending_reservations'] > 0)
                    <p class="text-amber-400 text-xs mt-2">{{ $stats['pending_reservations'] }} pendientes</p>
                @else
                    <p class="text-slate-600 text-xs mt-2">Sin pendientes ✓</p>
                @endif
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════
         ACTIVIDAD RECIENTE
         ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Últimos usuarios --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Últimos registros</h2>
                <button onclick="toggleTable('users', 'all')" class="text-xs text-purple-400 hover:text-purple-300 transition-colors">
                    Ver todos ({{ $stats['total_users'] }}) →
                </button>
            </div>
            <ul class="divide-y divide-slate-800">
                @forelse($recentUsers as $user)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            {{ $user->role === 'seller' ? 'bg-green-500/20 text-green-400' : ($user->role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400') }}">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $user->role === 'seller' ? 'bg-green-500/20 text-green-400' : ($user->role === 'admin' ? 'bg-purple-500/20 text-purple-400' : 'bg-blue-500/20 text-blue-400') }}">
                            {{ $user->role === 'seller' ? 'Emprendedor' : ($user->role === 'admin' ? 'Admin' : 'Cliente') }}
                        </span>
                        <p class="text-xs text-slate-600 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </li>
                @empty
                <li class="px-5 py-6 text-center text-slate-600 text-sm">No hay usuarios todavía.</li>
                @endforelse
            </ul>
        </div>

        {{-- Últimos productos --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-white">Últimos productos</h2>
                <button onclick="toggleTable('products', 'all')" class="text-xs text-yellow-400 hover:text-yellow-300 transition-colors">
                    Ver todos ({{ $stats['total_products'] }}) →
                </button>
            </div>
            <ul class="divide-y divide-slate-800">
                @forelse($recentProducts as $product)
                <li class="px-5 py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $product->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $product->businessProfile->business_name ?? '—' }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-semibold text-yellow-400">${{ number_format($product->price, 0) }}</p>
                        <span class="text-xs {{ $product->is_active ? 'text-green-400' : 'text-slate-500' }}">
                            {{ $product->is_active ? '● Activo' : '○ Inactivo' }}
                        </span>
                    </div>
                </li>
                @empty
                <li class="px-5 py-6 text-center text-slate-600 text-sm">No hay productos todavía.</li>
                @endforelse
            </ul>
        </div>

    </div>

    {{-- ══════════════════════════════════════════
         TABLA EXPANDIBLE DE USUARIOS
         ══════════════════════════════════════════ --}}
    <div id="table-users" class="hidden bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-white" id="users-table-title">Usuarios</h2>
                <p class="text-xs text-slate-500 mt-0.5" id="users-table-subtitle"></p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" id="users-search" placeholder="Buscar por nombre o email…"
                    class="text-sm bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-purple-500 w-56"
                    oninput="filterUsersTable()">
                <button onclick="toggleTable('users', null)"
                    class="text-slate-400 hover:text-white transition-colors text-lg leading-none" title="Cerrar">✕</button>
            </div>
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
                <tbody class="divide-y divide-slate-800" id="users-tbody">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-800/50 transition-colors user-row"
                        data-role="{{ $user->role }}"
                        data-name="{{ strtolower($user->name) }}"
                        data-email="{{ strtolower($user->email) }}"
                        data-has-products="{{ $user->role === 'seller' && $user->businessProfile?->products?->count() > 0 ? '1' : '0' }}">
                        <td class="px-6 py-4 text-slate-500">{{ $user->id }}</td>
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
                            @if($user->isSuspended())
                                <span class="ml-1 px-2 py-1 rounded-full text-xs bg-rose-500/20 text-rose-400 border border-rose-500/30">Suspendido</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="text-xs text-indigo-400 hover:text-indigo-300 border border-indigo-500/30 px-3 py-1 rounded-lg transition-colors">
                                    Ver detalle
                                </a>
                                @if($user->role !== 'admin')
                                <form action="{{ route('admin.users.suspend', $user) }}" method="POST"
                                    onsubmit="return confirm('{{ $user->isSuspended() ? '¿Reactivar' : '¿Suspender' }} a {{ $user->name }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-xs px-3 py-1 rounded-lg transition-colors border
                                            {{ $user->isSuspended()
                                                ? 'text-emerald-400 hover:text-emerald-300 border-emerald-500/30'
                                                : 'text-amber-400 hover:text-amber-300 border-amber-500/30' }}">
                                        {{ $user->isSuspended() ? 'Reactivar' : 'Suspender' }}
                                    </button>
                                </form>
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
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="users-empty" class="hidden px-6 py-8 text-center text-slate-500 text-sm">No hay usuarios en esta categoría.</div>
        {{-- Paginación --}}
        <div class="px-6 py-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-500" id="users-pagination">
            <span id="users-page-info"></span>
            <div class="flex gap-2">
                <button onclick="changePage('users', -1)" id="users-prev" class="px-3 py-1 rounded bg-slate-800 hover:bg-slate-700 disabled:opacity-30 transition-colors">← Anterior</button>
                <button onclick="changePage('users', 1)"  id="users-next" class="px-3 py-1 rounded bg-slate-800 hover:bg-slate-700 disabled:opacity-30 transition-colors">Siguiente →</button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         TABLA EXPANDIBLE DE PRODUCTOS
         ══════════════════════════════════════════ --}}
    <div id="table-products" class="hidden bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-white" id="products-table-title">Productos</h2>
                <p class="text-xs text-slate-500 mt-0.5" id="products-table-subtitle"></p>
            </div>
            <div class="flex items-center gap-3">
                <input type="text" id="products-search" placeholder="Buscar por nombre o negocio…"
                    class="text-sm bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-slate-200 placeholder-slate-500 focus:outline-none focus:border-yellow-500 w-56"
                    oninput="filterProductsTable()">
                <button onclick="toggleTable('products', null)"
                    class="text-slate-400 hover:text-white transition-colors text-lg leading-none" title="Cerrar">✕</button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-slate-300">
                <thead class="bg-slate-800 text-slate-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Producto</th>
                        <th class="px-6 py-3 text-left">Negocio</th>
                        <th class="px-6 py-3 text-left">Precio</th>
                        <th class="px-6 py-3 text-left">Estado</th>
                        <th class="px-6 py-3 text-left">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800" id="products-tbody">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-800/50 transition-colors product-row"
                        data-active="{{ $product->is_active ? '1' : '0' }}"
                        data-name="{{ strtolower($product->name) }}"
                        data-business="{{ strtolower($product->businessProfile->business_name ?? '') }}">
                        <td class="px-6 py-4 text-slate-500">{{ $product->id }}</td>
                        <td class="px-6 py-4 font-medium text-white">{{ $product->name }}</td>
                        <td class="px-6 py-4">{{ $product->businessProfile->business_name ?? '—' }}</td>
                        <td class="px-6 py-4">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4">
                            @if($product->is_active)
                                <span class="px-2 py-1 rounded-full text-xs bg-green-500/20 text-green-400 border border-green-500/30">Activo</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-slate-700/40 text-slate-400 border border-slate-600/30">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.products.toggle', $product) }}" method="POST"
                                    onsubmit="return confirm('{{ $product->is_active ? '¿Desactivar' : '¿Activar' }} {{ $product->name }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="text-xs px-3 py-1 rounded-lg transition-colors border
                                            {{ $product->is_active
                                                ? 'text-amber-400 hover:text-amber-300 border-amber-500/30'
                                                : 'text-emerald-400 hover:text-emerald-300 border-emerald-500/30' }}">
                                        {{ $product->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.products.delete', $product) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que querés eliminar {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-400 hover:text-rose-300 border border-rose-500/30 px-3 py-1 rounded-lg transition-colors">
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500 text-xs italic">No hay productos registrados todavía.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="products-empty" class="hidden px-6 py-8 text-center text-slate-500 text-sm">No hay productos en esta categoría.</div>
        <div class="px-6 py-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-500" id="products-pagination">
            <span id="products-page-info"></span>
            <div class="flex gap-2">
                <button onclick="changePage('products', -1)" id="products-prev" class="px-3 py-1 rounded bg-slate-800 hover:bg-slate-700 transition-colors">← Anterior</button>
                <button onclick="changePage('products', 1)"  id="products-next" class="px-3 py-1 rounded bg-slate-800 hover:bg-slate-700 transition-colors">Siguiente →</button>
            </div>
        </div>
    </div>

</div>

<script>
const PAGE_SIZE = 10;
const state = {
    users:    { filter: 'all', search: '', page: 1 },
    products: { filter: 'all', search: '', page: 1 },
};

const USERS_TITLES = {
    'all':               'Todos los usuarios',
    'client':            'Clientes',
    'seller':            'Emprendedores',
    'seller_no_products':'Emprendedores sin productos',
};
const PRODUCTS_TITLES = {
    'all':     'Todos los productos',
    'active':  'Productos activos',
    'inactive':'Productos inactivos',
};

// ── Toggle tabla (abrir/cerrar/filtrar) ──
function toggleTable(section, filter) {
    const table = document.getElementById('table-' + section);
    // Si ya está abierta con el mismo filtro → cerrar
    if (!table.classList.contains('hidden') && state[section].filter === filter && filter !== null) {
        table.classList.add('hidden');
        return;
    }
    if (filter === null) { table.classList.add('hidden'); return; }

    state[section].filter = filter;
    state[section].page   = 1;

    // Actualizar título
    if (section === 'users') {
        document.getElementById('users-table-title').textContent = USERS_TITLES[filter] || 'Usuarios';
        document.getElementById('users-search').value = '';
        state.users.search = '';
        applyUsersFilter();
    } else {
        document.getElementById('products-table-title').textContent = PRODUCTS_TITLES[filter] || 'Productos';
        document.getElementById('products-search').value = '';
        state.products.search = '';
        applyProductsFilter();
    }

    table.classList.remove('hidden');
    table.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── USUARIOS ──
function filterUsersTable() {
    state.users.search = document.getElementById('users-search').value.toLowerCase();
    state.users.page   = 1;
    applyUsersFilter();
}

function applyUsersFilter() {
    const rows   = Array.from(document.querySelectorAll('.user-row'));
    const f      = state.users.filter;
    const search = state.users.search;

    const visible = rows.filter(row => {
        const roleMatch =
            f === 'all'               ? true :
            f === 'seller_no_products'? row.dataset.role === 'seller' && row.dataset.hasProducts === '0' :
                                        row.dataset.role === f;
        const searchMatch = !search ||
            row.dataset.name.includes(search) ||
            row.dataset.email.includes(search);
        return roleMatch && searchMatch;
    });

    // Ocultar todas, mostrar solo página actual
    rows.forEach(r => r.style.display = 'none');
    const start = (state.users.page - 1) * PAGE_SIZE;
    visible.slice(start, start + PAGE_SIZE).forEach(r => r.style.display = '');

    document.getElementById('users-empty').classList.toggle('hidden', visible.length > 0);
    updatePagination('users', visible.length);
}

// ── PRODUCTOS ──
function filterProductsTable() {
    state.products.search = document.getElementById('products-search').value.toLowerCase();
    state.products.page   = 1;
    applyProductsFilter();
}

function applyProductsFilter() {
    const rows   = Array.from(document.querySelectorAll('.product-row'));
    const f      = state.products.filter;
    const search = state.products.search;

    const visible = rows.filter(row => {
        const filterMatch =
            f === 'all'      ? true :
            f === 'active'   ? row.dataset.active === '1' :
            f === 'inactive' ? row.dataset.active === '0' : true;
        const searchMatch = !search ||
            row.dataset.name.includes(search) ||
            row.dataset.business.includes(search);
        return filterMatch && searchMatch;
    });

    rows.forEach(r => r.style.display = 'none');
    const start = (state.products.page - 1) * PAGE_SIZE;
    visible.slice(start, start + PAGE_SIZE).forEach(r => r.style.display = '');

    document.getElementById('products-empty').classList.toggle('hidden', visible.length > 0);
    updatePagination('products', visible.length);
}

// ── PAGINACIÓN ──
function changePage(section, dir) {
    state[section].page += dir;
    section === 'users' ? applyUsersFilter() : applyProductsFilter();
}

function updatePagination(section, total) {
    const totalPages = Math.max(1, Math.ceil(total / PAGE_SIZE));
    state[section].page = Math.min(Math.max(1, state[section].page), totalPages);
    const page = state[section].page;
    const start = (page - 1) * PAGE_SIZE + 1;
    const end   = Math.min(page * PAGE_SIZE, total);

    document.getElementById(section + '-page-info').textContent =
        total > 0 ? `Mostrando ${start}–${end} de ${total}` : '0 resultados';
    document.getElementById(section + '-prev').disabled = page <= 1;
    document.getElementById(section + '-next').disabled = page >= totalPages;
    document.getElementById(section + '-pagination').style.display = total > PAGE_SIZE ? '' : 'none';
}
</script>
@endsection
