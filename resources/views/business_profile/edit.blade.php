@extends('layouts.app')

@section('title', 'Editar Perfil del Emprendimiento')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">

    <h1 class="text-2xl font-bold mb-6">Perfil del Emprendimiento</h1>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Errores de validación --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('business_profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Nombre del emprendimiento --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nombre del emprendimiento *</label>
            <input type="text" name="business_name"
                value="{{ old('business_name', $profile->business_name ?? '') }}"
                class="w-full border rounded px-3 py-2"
                required>
        </div>

        {{-- Descripción --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Descripción</label>
            <textarea name="description" rows="3"
                class="w-full border rounded px-3 py-2">{{ old('description', $profile->description ?? '') }}</textarea>
        </div>

        {{-- Teléfono --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="text" name="phone"
                value="{{ old('phone', $profile->phone ?? '') }}"
                class="w-full border rounded px-3 py-2">
        </div>

        {{-- Dirección --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Dirección</label>
            <input type="text" name="address"
                value="{{ old('address', $profile->address ?? '') }}"
                class="w-full border rounded px-3 py-2">
        </div>

        {{-- Logo --}}
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Logo</label>
            @if($profile && $profile->logo)
                <img src="{{ Storage::url($profile->logo) }}" alt="Logo actual" class="w-24 h-24 object-cover rounded mb-2">
            @endif
            <input type="file" name="logo" accept="image/jpeg,image/png,image/jpg"
                class="w-full border rounded px-3 py-2">
            <p class="text-xs text-gray-500 mt-1">JPG o PNG, máximo 2MB</p>
        </div>

        <button type="submit"
            class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Guardar cambios
        </button>

    </form>
</div>
@endsection