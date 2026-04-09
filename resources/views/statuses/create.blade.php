@extends('adminlte::page')

@section('title', 'Nuevo Estatus')

@section('content_header')
    <h1>Crear Estatus</h1>
@stop

@section('content')
<div class="card card-primary">
    <form action="{{ route('statuses.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre del Estatus</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" placeholder="Ej. Pendiente, En Proceso, Terminado" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Guardar Estatus</button>
            <a href="{{ route('statuses.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop