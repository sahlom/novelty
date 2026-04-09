@extends('adminlte::page')

@section('title', 'Crear Área')

@section('content_header')
    <h1>Nueva Área</h1>
@stop

@section('content')
<div class="card card-primary">
    <form action="{{ route('areas.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre del Área</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Ej. Redes, Soporte, Desarrollo" value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('areas.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop