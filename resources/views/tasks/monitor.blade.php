@extends('adminlte::page')

@section('title', 'Monitor Operativo TIC')

{{-- Agregamos un poco de CSS para mejorar la legibilidad del monitor --}}
@section('css')
<style>
    .table-monitor td {
        vertical-align: middle;
        font-size: 1.15rem; /* Texto ligeramente más grande para lectura a distancia */
        padding: 15px;
    }
    .priority-indicator {
        width: 12px;
        height: 12px;
        display: inline-block;
        border-radius: 50%;
        margin-right: 8px;
    }
    /* Animación de pulso sutil para estados críticos o detenidos */
    .animate-pulse {
        animation: pulse-danger 2s infinite;
    }
    @keyframes pulse-danger {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.6); }
        70% { transform: scale(1.03); box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center w-100">
        <h1><i class="fas fa-tv text-muted mr-2"></i>Monitor de Operaciones TIC</h1>
        <h4 id="clock" class="text-dark font-weight-bold mb-0"></h4>
    </div>
@stop

@section('content')
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover table-monitor mb-0">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Prioridad</th>
                    <th>Tarea / Título</th>
                    <th>Cliente</th>
                    <th>Área</th>
                    <th>Asignado a</th>
                    <th class="text-center" style="width: 180px;">Estatus</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                <tr>
                    <td>
                        {{-- Indicador visual de prioridad --}}
                        @php
                            $priorityColor = [
                                'Urgente' => 'bg-danger',
                                'Alta'    => 'bg-warning',
                                'Media'   => 'bg-info',
                                'Baja'    => 'bg-secondary'
                            ][$task->priority?->name ?? 'Baja'] ?? 'bg-dark';
                        @endphp
                        <span class="priority-indicator {{ $priorityColor }}"></span>
                        <strong>{{ $task->priority?->name ?? 'N/A' }}</strong>
                    </td>
                    <td>
                        <span class="text-bold text-dark">{{ $task->title }}</span>
                        <br>
                        <small class="text-muted">
                            Solicitado: {{ \Carbon\Carbon::parse($task->created_at)->locale('es')->diffForHumans() }}
                        </small>
                    </td>
                    <td>{{ $task->client->razon_social ?? 'N/A' }}</td>
                    <td>{{ $task->area->name ?? 'N/A' }}</td>
                    <td>
                        <i class="fas fa-user-circle text-muted mr-1"></i> 
                        {{ $task->user->display_name ?? ($task->user->name ?? 'Sin asignar') }}
                    </td>
                    <td class="text-center">
                        {{-- Sistema de Semáforos Inteligente para Status --}}
                        @php
                            $statusName = strtolower($task->status?->name ?? '');
                            
                            // Determinamos la clase del badge y si requiere animación de pulso
                            if (str_contains($statusName, 'nuev') || str_contains($statusName, 'pendient') || str_contains($statusName, 'registr')) {
                                $statusClass = 'badge-warning text-dark';
                            } elseif (str_contains($statusName, 'proceso') || str_contains($statusName, 'atendiend')) {
                                $statusClass = 'badge-primary';
                            } elseif (str_contains($statusName, 'espera') || str_contains($statusName, 'detenid') || str_contains($statusName, 'pausa') || str_contains($statusName, 'atrasad')) {
                                $statusClass = 'badge-danger animate-pulse';
                            } elseif (str_contains($statusName, 'cerrad') || str_contains($statusName, 'complet') || str_contains($statusName, 'resuelt')) {
                                $statusClass = 'badge-success';
                            } else {
                                $statusClass = 'badge-light border text-muted';
                            }
                        @endphp
                        
                        <span class="badge {{ $statusClass }} p-2 w-100 text-center shadow-sm font-weight-bold" style="font-size: 1rem;">
                            {{ $task->status?->name ?? 'N/A' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4>No hay tareas pendientes en este momento</h4>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Reloj en tiempo real en formato local (HH:MM:SS)
        setInterval(() => {
            const now = new Date();
            $('#clock').text(now.toLocaleTimeString());
        }, 1000);

        // Auto-refresh limpio del navegador cada 5 minutos (300000 ms)
        setTimeout(() => {
            window.location.reload(true);
        }, 300000);
    });
</script>
@stop