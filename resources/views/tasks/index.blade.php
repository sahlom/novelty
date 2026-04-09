@extends('adminlte::page')

@section('title', 'Listado de Tareas')

@section('content_header')
    <h1>Gestión de Tareas de TIC</h1>
@stop

@section('content')
<div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="tasks-open-tab" data-toggle="pill" href="#tasks-open" role="tab" aria-controls="tasks-open" aria-selected="true">
                    Tareas Abiertas <span class="badge badge-warning ml-1">{{ $openTasks->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tasks-closed-tab" data-toggle="pill" href="#tasks-closed" role="tab" aria-controls="tasks-closed" aria-selected="false">
                    Tareas Cerradas <span class="badge badge-success ml-1">{{ $closedTasks->count() }}</span>
                </a>
            </li>
            <div class="ml-auto p-2">
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Nueva Tarea
                </a>
            </div>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-three-tabContent">
            {{-- Pestaña Abiertas --}}
            <div class="tab-pane fade show active" id="tasks-open" role="tabpanel" aria-labelledby="tasks-open-tab">
                @if($openTasks->isEmpty())
                    <p class="text-center py-4">No tienes tareas abiertas. ¡Buen trabajo!</p>
                @else
                    @include('tasks.partials.table', ['tasks' => $openTasks])
                @endif
            </div>

            {{-- Pestaña Cerradas --}}
            <div class="tab-pane fade" id="tasks-closed" role="tabpanel" aria-labelledby="tasks-closed-tab">
                @if($closedTasks->isEmpty())
                    <p class="text-center py-4">No hay tareas completadas todavía.</p>
                @else
                    @include('tasks.partials.table', ['tasks' => $closedTasks])
                @endif
            </div>
        </div>
    </div>
</div>
@stop