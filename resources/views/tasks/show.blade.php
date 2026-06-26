@extends('adminlte::page')

@section('title', 'Tarea #' . $task->id)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
        <h1>Detalle de Tarea #{{ $task->id }}</h1>
        <div class="mt-2 mt-md-0 d-flex align-items-center">
            {{-- Botón Nuevo y Editar visible para todos los usuarios con acceso --}}
            <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm align-middle mr-2">
                <i class="fas fa-plus"></i> Nuevo
            </a>
            {{-- Botón Editar visible para todos los usuarios con acceso --}}
            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                <i class="fas fa-edit"></i> Editar
            </a>

            {{-- SOLUCIÓN: Botón Eliminar exclusivo para Administradores con confirmación segura --}}
            @if(auth()->user()->role === 'admin')
                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline" 
                      onsubmit="return confirm('¿Estás absolutamente seguro de eliminar esta Tarea? Esta acción borrará de forma irreversible el registro y todo su historial de comentarios.')">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')
@php
    // Lógica dinámica para los badges de Estado
    $stName = strtolower($task->status?->name ?? '');
    $stBadge = 'badge-info';
    if (str_contains($stName, 'nuev') || str_contains($stName, 'pendient') || str_contains($stName, 'registr')) {
        $stBadge = 'badge-warning';
    } elseif (str_contains($stName, 'espera') || str_contains($stName, 'detenid') || str_contains($stName, 'pausa')) {
        $stBadge = 'badge-danger';
    } elseif (str_contains($stName, 'cerrad') || str_contains($stName, 'complet') || str_contains($stName, 'resuelt')) {
        $stBadge = 'badge-success';
    }

    // Lógica dinámica para los badges de Prioridad
    $prName = strtolower($task->priority?->name ?? '');
    $prBadge = 'badge-secondary';
    if (str_contains($prName, 'alta') || str_contains($prName, 'urgent') || str_contains($prName, 'critic')) {
        $prBadge = 'badge-danger text-uppercase font-weight-bold';
    } elseif (str_contains($prName, 'medi') || str_contains($prName, 'normal')) {
        $prBadge = 'badge-warning';
    }
@endphp

{{-- Tarjeta Maestra Superior: Información Consolidada con Header Primary --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header bg-primary text-white pt-3 pb-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap w-100">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 d-none d-sm-block">
                            <i class="fas fa-tasks fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="card-title font-weight-bold m-0" style="font-size: 1.4rem;">{{ $task->title }}</h3>
                            <span class="d-block small text-white-50 mt-1">ID de Control: <strong>#{{ $task->id }}</strong></span>
                        </div>
                    </div>
                    <div class="text-right mt-2 mt-md-0 d-flex flex-row align-items-center gap-2">
                        <span class="badge {{ $stBadge }} text-uppercase px-3 py-2 mr-2 shadow-sm">{{ $task->status->name }}</span>
                        <span class="badge {{ $prBadge }} px-3 py-2 shadow-sm">{{ $task->priority->name }}</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body pt-3">
                {{-- Bloque Grid de Datos de Control --}}
                <div class="row bg-light p-3 rounded border mb-3 mx-0">
                    <div class="col-sm-6 col-md-3 mb-2 mb-md-0 border-right-md">
                        <span class="text-muted small d-block"><i class="fas fa-building mr-1"></i> Cliente</span>
                        <span class="text-dark font-weight-bold">{{ $task->client->razon_social ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3 mb-2 mb-md-0 border-right-md">
                        <span class="text-muted small d-block"><i class="fas fa-laptop-house mr-1"></i> Área Solicitante</span>
                        <span class="text-dark font-weight-bold">{{ $task->area->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3 mb-2 mb-md-0 border-right-md">
                        <span class="text-muted small d-block"><i class="fas fa-user-check mr-1"></i> Asignado a</span>
                        <span class="text-dark font-weight-bold">{{ $task->user->display_name ?? $task->user->name ?? 'Sin asignar' }}</span>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <span class="text-muted small d-block"><i class="fas fa-calendar-alt mr-1"></i> Fechas Clave</span>
                        <span class="text-dark d-block small">Alta: <strong>{{ $task->created_at->format('d/m/Y H:i') }}</strong></span>
                        <span class="text-danger d-block small">Vence: <strong>{{ $task->due_date ? $task->due_date->format('d/m/Y') : 'N/A' }}</strong></span>
                    </div>
                </div>

                {{-- Bloque de Requerimiento / Descripción Original --}}
                <div class="px-2">
                    <span class="text-muted small d-block font-weight-bold mb-1"><i class="fas fa-align-left mr-1"></i> Descripción del Requerimiento:</span>
                    <div class="text-secondary p-2 rounded" style="white-space: pre-line; background-color: rgba(0,0,0,0.02); border-left: 3px solid #007bff;">
                        {{ $task->description }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tarjeta Única Inferior: Historial de Seguimiento con Header Primary --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title font-weight-bold m-0 align-middle">
                    <i class="fas fa-history mr-1"></i> Historial de Seguimiento
                </h3>
                <div class="card-tools">
                    <span class="badge badge-light px-2 py-1 font-weight-bold text-primary">{{ $task->comments->count() }} Actualizaciones</span>
                </div>
            </div>
            
            <div class="card-body">
                {{-- Formulario de Registro Rápido --}}
                <form action="{{ route('tasks.comments.store', $task->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="body" placeholder="Escribir nuevo avance o notas de seguimiento..." class="form-control" required autocomplete="off">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane mr-1"></i> Agregar Seguimiento
                            </button>
                        </span>
                    </div>
                </form>

                {{-- Timeline de AdminLTE Limpio a Ancho Completo --}}
                <div class="timeline timeline-inverse">
                    @forelse($task->comments->sortByDesc('created_at') as $comment)
                        <div class="time-label">
                            <span class="bg-info shadow-sm">{{ $comment->created_at->format('d M. Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-comment-dots bg-secondary text-white shadow-sm"></i>
                            <div class="timeline-item shadow-sm">
                                <span class="time"><i class="far fa-clock"></i> {{ $comment->created_at->format('H:i') }} hrs</span>
                                <h3 class="timeline-header font-weight-normal">
                                    <a href="#" class="font-weight-bold">{{ $comment->user->name }}</a> añadió una actualización
                                </h3>
                                <div class="timeline-body text-secondary">
                                    {{ $comment->body }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="far fa-folder-open fa-3x mb-3 text-gray"></i>
                            <h5>Sin bitácora actual</h5>
                            <p class="small mb-0 text-muted">No se han registrado avances en esta tarea todavía.</p>
                        </div>
                    @endforelse
                    
                    <div>
                        <i class="far fa-clock bg-gray shadow-sm"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    @media (min-width: 768px) {
        .border-right-md {
            border-right: 1px solid #dee2e6 !important;
        }
    }
    .gap-2 > * {
        margin-left: 4px;
    }
    .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
</style>
@stop