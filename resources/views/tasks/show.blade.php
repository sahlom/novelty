@extends('adminlte::page')

@section('title', 'Detalle de Tarea')

@section('content_header')
    <h1>Tarea #{{ $task->id }}: {{ $task->title }}</h1>
@stop

@section('content')
<div class="row">
    {{-- Columna Izquierda: Información General --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <h3 class="profile-username text-center">{{ $task->status->name }}</h3>
                <p class="text-muted text-center">{{ $task->priority->name }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Cliente:</b> <a class="float-right">{{ $task->client->razon_social }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Área:</b> <a class="float-right">{{ $task->area->name }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Asignado a:</b> <a class="float-right">{{ $task->user->name ?? 'Sin asignar' }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Vence:</b> <a class="float-right">{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A' }}</a>
                    </li>
                </ul>

                <strong><i class="fas fa-book mr-1"></i> Descripción</strong>
                <p class="text-muted">{{ $task->description }}</p>

                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-warning btn-block"><b>Editar Tarea</b></a>
            </div>
        </div>
    </div>

    {{-- Columna Derecha: Bitácora de Comentarios --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header p-2">
                <h3 class="card-title">Historial de Seguimiento</h3>
            </div>
            <div class="card-body">
                {{-- Formulario para nuevo comentario --}}
                <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="body" placeholder="Escribir avance técnico..." class="form-control" required>
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-primary">Agregar Seguimiento</button>
                        </span>
                    </div>
                </form>

                {{-- Timeline de AdminLTE --}}
                <div class="timeline timeline-inverse">
                    @forelse($task->comments as $comment)
                        <div class="time-label">
                            <span class="bg-info">{{ $comment->created_at->format('d M. Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-comments bg-secondary"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="far fa-clock"></i> {{ $comment->created_at->format('H:i') }}</span>
                                <h3 class="timeline-header">
                                    <a href="#">{{ $comment->user->name }}</a> añadió un comentario
                                </h3>
                                <div class="timeline-body">
                                    {{ $comment->body }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center p-4">No hay seguimientos registrados aún.</p>
                    @endforelse
                    
                    <div>
                        <i class="far fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop