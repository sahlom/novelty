@extends('adminlte::page')

@section('title', 'Áreas')

@section('content_header')
    <h1>Áreas de la Institución</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Áreas</h3>
        <div class="card-tools">
            <a href="{{ route('areas.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Área
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Nombre del Área</th>
                    <th style="width: 150px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($areas as $area)
                <tr>
                    <td>{{ $area->id }}</td>
                    <td>{{ $area->name }}</td>
                    <td>
                        <div class="btn-group">
                            {{-- El botón de editar lo dejamos para todos, o podrías limitarlo también --}}
                            <a href="{{ route('areas.edit', $area->id) }}" 
                            class="btn btn-xs btn-default text-info mx-1 shadow" 
                            title="Editar">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>

                            {{-- FILTRO DE SEGURIDAD: Solo el Admin ve el botón de eliminar --}}
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('areas.destroy', $area->id) }}" 
                                    method="POST" 
                                    class="d-inline" 
                                    onsubmit="return confirm('¿Estás seguro de eliminar esta área? Las tareas asociadas quedarán sin área asignada.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-xs btn-default text-danger mx-1 shadow" 
                                            title="Eliminar">
                                        <i class="fa fa-lg fa-fw fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Opcional: Un ícono de candado o simplemente nada --}}
                                <!-- <span class="text-muted mx-2" title="No tienes permisos para eliminar"><i class="fas fa-lock"></i></span> -->
                            @endif
                        </div>
                    </td>                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop