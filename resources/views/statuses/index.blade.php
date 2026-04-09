@extends('adminlte::page')

@section('title', 'Estatus')

@section('content_header')
    <h1>Catálogo de Estatus</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Estatus</h3>
        <div class="card-tools">
            <a href="{{ route('statuses.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Estatus
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th>Nombre</th>
                    <th style="width: 150px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statuses as $status)
                <tr>
                    <td>{{ $status->id }}</td>
                    <td>{{ $status->name }}</td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('statuses.edit', $status->id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Editar">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>

                            @if(auth()->user()->role === 'admin')
                            <form action="{{ route('statuses.destroy', $status->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este estatus?')">
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