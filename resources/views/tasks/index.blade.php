@extends('adminlte::page')

@section('title', 'Listado de Tareas')

@section('content_header')
    <h1>Gestión de Tareas de TIC</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tareas Registradas</h3>
        <div class="card-tools">
            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nueva Tarea
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Cliente</th>
                    <th>Área</th>
                    <th>Responsable</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->client->razon_social }}</td>
                    <td>{{ $task->area->name }}</td>
                    <td>{{ $task->user->name ?? 'Sin asignar' }}</td>
                    <td>
                        <span class="badge badge-info">{{ $task->status->name }}</span>
                    </td>
                    <td>
                        @php
                            $badgeColor = [
                                'Baja' => 'badge-secondary',
                                'Media' => 'badge-info',
                                'Alta' => 'badge-warning',
                                'Urgente' => 'badge-danger'
                            ][$task->priority->name] ?? 'badge-dark';
                        @endphp
                        <span class="badge {{ $badgeColor }}">{{ $task->priority->name }}</span>
                    </td>
                    <td>
                        <div class="btn-group">
                            {{-- Botón Ver (Todos pueden) --}}
                            <a href="{{ route('tasks.show', $task->id) }}" 
                            class="btn btn-xs btn-default text-primary mx-1 shadow" 
                            title="Ver detalles">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>

                            {{-- Botón Editar (Admin o Responsable de la tarea) --}}
                            @if(auth()->user()->role === 'admin' || auth()->id() === $task->user_id)
                                <a href="{{ route('tasks.edit', $task->id) }}" 
                                class="btn btn-xs btn-default text-info mx-1 shadow" 
                                title="Editar">
                                    <i class="fa fa-lg fa-fw fa-pen"></i>
                                </a>
                            @endif

                            {{-- Botón Eliminar (Solo Admin) --}}
                            @if(auth()->user()->role === 'admin')
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                    @csrf
                                    @method('DELETE')
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