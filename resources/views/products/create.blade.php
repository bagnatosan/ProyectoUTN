@extends('layouts.app')

@section('title', 'Nuevo Producto | ProyectoUTN')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="border border-slate-800 bg-slate-900/40 backdrop-blur rounded-2xl p-8 shadow-xl">
        <span class="px-3 py-1 text-xs font-bold tracking-wider rounded-full uppercase bg-indigo-500/10 border border-indigo-500/20 text-indigo-400">
            Vista Preliminar (Borrador)
        </span>
        <h1 class="text-3xl font-bold tracking-tight text-white mt-4">
            Agregar Nuevo Producto
        </h1>
        <p class="text-slate-400 mt-2 text-sm">
            Responsable: <span class="text-indigo-300 font-semibold">Programador 2</span> | Modelo: <code>Product</code>
        </p>

        <div class="mt-8 border border-dashed border-slate-800 rounded-xl p-8 text-center text-slate-500 text-sm">
            <p>Aquí se maquetará el formulario de creación de productos (Nombre, descripción, precio, categoría, estado e imagen descriptiva).</p>
        </div>
    </div>
</div>
@endsection
