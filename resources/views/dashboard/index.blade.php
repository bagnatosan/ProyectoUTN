@extends('layouts.app')

@section('title', 'Dashboard del Emprendedor | ProyectoUTN')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Vista Preliminar (Borrador)
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Panel de Métricas y Turnos del Emprendedor
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Responsable: <span class="text-indigo-300 font-semibold">Programador 5</span> | Modelos: Todos
        </p>

        <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-12 text-center text-slate-500 text-sm">
            <p>Aquí se maquetará el panel interactivo del vendedor. Contendrá los widgets de analítica comercial (Facturación mensual, margen real, cantidad de turnos completados y cancelados), la barra de búsqueda y filtros avanzados de reservas y la tabla interactiva de turnos del día con botones rápidos de cambio de estado.</p>
        </div>
    </div>
</div>
@endsection
