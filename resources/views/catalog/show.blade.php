@extends('layouts.app')

@section('title', 'Catálogo de Productos | ProyectoUTN')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Vista Pública Preliminar (Borrador)
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Catálogo del Emprendimiento
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Responsable: <span class="text-indigo-300 font-semibold">Programador 2</span> | Modelos: <code>BusinessProfile</code>, <code>Product</code>
        </p>

        <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-12 text-center text-slate-500 text-sm">
            <p>Aquí se maquetará el catálogo comercial que visualizan los clientes. Contendrá una cabecera con el logo e información de la tienda, filtros por categoría de producto y una grilla con tarjetas de productos activas y botón de agendar reserva.</p>
        </div>
    </div>
</div>
@endsection
