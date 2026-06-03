@extends('layouts.app')

@section('title', 'Gestionar Disponibilidad Horaria | ProyectoUTN')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Vista Preliminar (Borrador)
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Configurar Disponibilidad y Agenda Horaria
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Responsable: <span class="text-indigo-300 font-semibold">Programador 4</span> | Modelo: <code>AvailabilitySlot</code>
        </p>

        <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-12 text-center text-slate-500 text-sm">
            <p>Aquí se maquetará la configuración de agenda semanal del vendedor (días laborables y rangos horarios). Permitirá agregar, editar y suspender franjas horarias de atención.</p>
        </div>
    </div>
</div>
@endsection
