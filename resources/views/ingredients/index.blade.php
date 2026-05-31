@extends('layouts.app')

@section('title', 'Ingredientes | ProyectoUTN')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <div class="flex justify-between items-center">
            <div>
                <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
                    Vista Preliminar (Borrador)
                </span>
                <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
                    Listado de Ingredientes (Materia Prima)
                </h1>
                <p class="text-slate-400 mt-2 text-sm">
                    Responsable: <span class="text-indigo-300 font-semibold">Programador 3</span> | Modelo: <code>Ingredient</code>
                </p>
            </div>
            <a href="#" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors">
                + Nuevo Ingrediente
            </a>
        </div>

        <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-12 text-center text-slate-500 text-sm">
            <p>Aquí se maquetará la tabla de ingredientes (inventario de insumos) con sus columnas: Nombre, Unidad de medida (gr, ml, unidad), Costo unitario y Acciones (Editar / Eliminar).</p>
        </div>
    </div>
</div>
@endsection
