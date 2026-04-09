@extends('adminlte::page')

@section('title', 'Nueva Prioridad')

@section('content_header')
    <h1>Crear Prioridad</h1>
@stop

@section('content')
<div class="card card-primary">
    <form action="{{ route('priorities.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre de la Prioridad</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Ej. Alta, Media, Baja" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="{{ route('priorities.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop