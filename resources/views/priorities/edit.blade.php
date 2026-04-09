@extends('adminlte::page')

@section('title', 'Editar Prioridad')

@section('content_header')
    <h1>Editar Prioridad: {{ $priority->name }}</h1>
@stop

@section('content')
<div class="card card-info">
    <form action="{{ route('priorities.update', $priority->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre de la Prioridad</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $priority->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Actualizar</button>
            <a href="{{ route('priorities.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop