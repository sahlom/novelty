@extends('adminlte::page')

@section('title', 'Editar Estatus')

@section('content_header')
    <h1>Editar Estatus: {{ $status->name }}</h1>
@stop

@section('content')
<div class="card card-info">
    <form action="{{ route('statuses.update', $status->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre del Estatus</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $status->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Actualizar Estatus</button>
            <a href="{{ route('statuses.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop