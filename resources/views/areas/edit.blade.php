@extends('adminlte::page')

@section('title', 'Editar Área')

@section('content_header')
    <h1>Editar Área: {{ $area->name }}</h1>
@stop

@section('content')
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">Modificar datos del área</h3>
    </div>
    
    <form action="{{ route('areas.update', $area->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- ¡Vital! Laravel necesita esto para saber que es una actualización --}}
        
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre del Área</label>
                <input type="text" 
                       name="name" 
                       class="form-control @error('name') is-invalid @enderror" 
                       id="name" 
                       value="{{ old('name', $area->name) }}" {{-- Si falla la validación, mantiene lo nuevo; si no, muestra lo que hay en BD --}}
                       required>
                
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info">Actualizar Cambios</button>
            <a href="{{ route('areas.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop