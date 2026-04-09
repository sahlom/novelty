@extends('adminlte::page')

@section('title', 'Prioridades')

@section('content_header')
    <h1>Catálogo de Prioridades</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Prioridades</h3>
        <div class="card-tools">
            <a href="{{ route('priorities.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Prioridad
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th>Nombre</th>
                    <th style="width: 150px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($priorities as $priority)
                <tr>
                    <td>{{ $priority->id }}</td>
                    <td>{{ $priority->name }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('priorities.edit', $priority->id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>

                            @if(auth()->user()->role === 'admin')
                            <form action="{{ route('priorities.destroy', $priority->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta prioridad?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar">
                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop