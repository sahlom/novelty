@extends('adminlte::page')

@section('title', 'Monitor Operativo TIC')

{{-- Agregamos un poco de CSS para mejorar la legibilidad del monitor --}}
@section('css')
<style>
    .table-monitor td {
        vertical-align: middle;
        font-size: 1.1rem; /* Texto un poco más grande */
        padding: 15px;
    }
    .priority-indicator {
        width: 10px;
        height: 10px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 8px;
    }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1><i class="fas fa-tv text-muted"></i> Monitor de Operaciones TIC</h1>
        <h4 id="clock" class="text-muted"></h4>
    </div>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-monitor mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Prioridad</th>
                    <th>Tarea / Título</th>
                    <th>Cliente</th>
                    <th>Área</th>
                    <th>Asignado a</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td>
                        {{-- Indicador visual de prioridad --}}
                        @php
                            $color = [
                                'Urgente' => 'bg-danger',
                                'Alta' => 'bg-warning',
                                'Media' => 'bg-info',
                                'Baja' => 'bg-secondary'
                            ][$task->priority->name] ?? 'bg-dark';
                        @endphp
                        <span class="priority-indicator {{ $color }}"></span>
                        <strong>{{ $task->priority->name }}</strong>
                    </td>
                    <td>
                        <span class="text-bold">{{ $task->title }}</span>
                        <br>
                        <small class="text-muted">Solicitado: {{ $task->created_at->diffForHumans() }}</small>
                    </td>
                    <td>{{ $task->client->razon_social }}</td>
                    <td>{{ $task->area->name }}</td>
                    <td>
                        <i class="fas fa-user-circle text-muted"></i> 
                        {{ $task->user->name ?? 'Por asignar' }}
                    </td>
                    <td class="text-center">
                        <span class="badge p-2 w-75 {{ $task->status->name == 'En Proceso' ? 'badge-primary' : 'badge-light border' }}">
                            {{ $task->status->name }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <h4>No hay tareas pendientes en este momento</h4>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Script para que se refresque solo cada 5 min y tenga un reloj --}}
@section('js')
<script>
    // Reloj en tiempo real
    setInterval(() => {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString();
    }, 1000);

    // Auto-refresh cada 5 minutos (300000 ms)
    setTimeout(() => {
        location.reload();
    }, 300000);
</script>
@stop
@stop