@extends('layouts.app')

@section('title', 'Mis Reservas | ProyectoUTN')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Vista Preliminar (Borrador)
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Historial de mis Reservas
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Responsable: <span class="text-indigo-300 font-semibold">Programador 4</span> | Modelo: <code>Reservation</code>
        </p>

        <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-12 text-center text-slate-500 text-sm">
            <p>Aquí se maquetará el historial de reservas de cara al cliente logueado, mostrando las reservas en una tabla o lista con columnas de Producto, Fecha, Hora, Estado del Turno y Contacto del Emprendedor.</p>
        </div>
    </div>
</div>
@endsection
