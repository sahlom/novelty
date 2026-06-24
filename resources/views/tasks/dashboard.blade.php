@extends('adminlte::page')

@section('title', 'Dashboard de Control Operativo')

{{-- Cargamos Chart.js desde los plugins nativos de AdminLTE --}}
@section('plugins.Chartjs', true)

@section('css')
<style>
    /* Estilos para mantener consistencia con el Monitor */
    .small-box h3 {
        font-size: 2.5rem;
        font-weight: bold;
    }
    .small-box p {
        font-size: 1.1rem;
    }
    .table-dashboard th, .table-dashboard td {
        vertical-align: middle !important;
        font-size: 1rem;
    }
    .card-title-lg {
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center w-100">
        <h1><i class="fas fa-tachometer-alt text-muted mr-2"></i> Panel de Control Operativo TIC</h1>
        <h5 class="text-muted"><i class="fas fa-calendar-day mr-1"></i> {{ now()->format('d/m/Y') }}</h5>
    </div>
@stop

@section('content')

{{-- 1. SECCIÓN DE TARJETAS DE IMPACTO RÁPIDO (SMALL BOXES) --}}
<div class="row">
    {{-- Tareas Activas --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm">
            <div class="inner">
                <h3>{{ $activeTasksCount ?? 0 }}</h3>
                <p>Tareas Activas</p>
            </div>
            <div class="icon">
                <i class="fas fa-tasks"></i>
            </div>
            <a href="#" class="small-box-footer">Ver todas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    {{-- Tareas En Proceso --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary shadow-sm">
            <div class="inner">
                <h3>{{ $processingTasksCount ?? 0 }}</h3>
                <p>En Proceso</p>
            </div>
            <div class="icon">
                <i class="fas fa-cogs"></i>
            </div>
            <a href="#" class="small-box-footer">Ver historial <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    {{-- Tareas Urgentes --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger shadow-sm">
            <div class="inner">
                <h3>{{ $urgentTasksCount ?? 0 }}</h3>
                <p>Prioridad Urgente / Alta</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="#" class="small-box-footer">Atender de inmediato <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    {{-- Clientes Activos --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary shadow-sm">
            <div class="inner">
                <h3>{{ $activeClientsCount ?? 0 }}</h3>
                <p>Clientes con Movimiento</p>
            </div>
            <div class="icon">
                <i class="fas fa-building"></i>
            </div>
            <a href="#" class="small-box-footer">Ver Clientes <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

{{-- 2. SECCIÓN DE MÉTRICAS GRÁFICAS --}}
{{-- 2. SECCIÓN DE MÉTRICAS GRÁFICAS (Estructura de 3 Columnas) --}}
<div class="row">
    {{-- Gráfica 1: Carga de Trabajo por Área (Ancho 4) --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title card-title-lg">
                    <i class="fas fa-chart-bar text-primary mr-2"></i> Tareas por Área
                </h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="areasChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfica 2: Carga de Trabajo por Usuario (Ancho 4 - NUEVA) --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title card-title-lg">
                    <i class="fas fa-users text-success mr-2"></i> Tareas por Usuario
                </h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="usersChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfica 3: Distribución por Estado / Semáforo (Ancho 4) --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title card-title-lg">
                    <i class="fas fa-chart-pie text-warning mr-2"></i> Tareas por Estado
                </h3>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Gráfica: Top 5 Clientes con Más Carga --}}
    <div class="col-md-6">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title card-title-lg">
                    <i class="fas fa-fire text-danger mr-2"></i> Top 5 Clientes con Mayor Demanda
                </h3>
            </div>
            <div class="card-body">
                <canvas id="clientsChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>

    {{-- Alertas: El Top 5 del Abandono (Tareas más rezagadas) --}}
    <div class="col-md-6">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title card-title-lg">
                    <i class="fas fa-hourglass-start text-warning mr-2"></i> Tareas Críticas de Mayor Antigüedad
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-dashboard mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>Tarea / Título</th>
                            <th>Asignado a</th>
                            <th>Tiempo Transcurrido</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($oldestTasks ?? [] as $task)
                        <tr>
                            <td><strong>#{{ $task->id }}</strong></td>
                            <td>
                                <span class="font-weight-bold">{{ Str::limit($task->title, 40) }}</span><br>
                                <small class="text-muted">{{ $task->client->razon_social ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $task->user->name ?? 'Sin asignar' }}</td>
                            <td>
                                <span class="badge badge-warning py-1 px-2 text-dark">
                                    <i class="fas fa-clock mr-1"></i> {{ $task->created_at->diffForHumans() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <i class="fas fa-check-double text-success fa-2x mb-2"></i><br>
                                ¡Excelente! No hay tareas rezagadas en el sistema.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        // --- 1. CONFIGURACIÓN GRÁFICA: CARGA POR ÁREAS (Barras Verticales) ---
        var areasCanvas = $('#areasChart').get(0).getContext('2d');
        var areasData = {
            labels  : {!! json_encode($areasLabels ?? ['Contabilidad', 'Nóminas', 'Auditoría', 'TIC', 'Fiscal']) !!},
            datasets: [{
                label               : 'Tareas Activas',
                backgroundColor     : '#007bff', // Azul Primario Sistema
                borderColor         : '#0069d9',
                data                : {!! json_encode($areasValues ?? [12, 8, 5, 3, 6]) !!}
            }]
        };
        new Chart(areasCanvas, {
            type: 'bar',
            data: areasData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
                }
            }
        });

        // --- NEW. CONFIGURACIÓN GRÁFICA: CARGA POR USUARIOS (Barras Verticales) ---
        var usersCanvas = $('#usersChart').get(0).getContext('2d');
        var usersData = {
            labels  : {!! json_encode($usersLabels ?? ['Carlos', 'Ana', 'Luis', 'Sofía']) !!},
            datasets: [{
                label               : 'Tareas Asignadas',
                backgroundColor     : '#28a745', // Verde para hacer juego con el icono
                borderColor         : '#1e7e34',
                data                : {!! json_encode($usersValues ?? [5, 9, 4, 7]) !!}
            }]
        };
        new Chart(usersCanvas, {
            type: 'bar',
            data: usersData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
                }
            }
        });

        // --- 2. CONFIGURACIÓN GRÁFICA: SEMÁFORO (Dona) ---
        var statusCanvas = $('#statusChart').get(0).getContext('2d');
        var statusData = {
            labels: ['Nuevas/Pendientes', 'En Proceso', 'En Espera/Detenidas'],
            datasets: [{
                data: {!! json_encode($statusValues ?? [4, 10, 3]) !!},
                backgroundColor: ['#ffc107', '#007bff', '#dc3545'] // Amarillo, Azul, Rojo (Mismo Semáforo)
            }]
        };
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: statusData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { position: 'bottom', labels: { boxWidth: 15, padding: 15 } }
            }
        });

        // --- 3. CONFIGURACIÓN GRÁFICA: TOP CLIENTES (Barras Horizontales) ---
        var clientsCanvas = $('#clientsChart').get(0).getContext('2d');
        var clientsData = {
            labels  : {!! json_encode($clientsLabels ?? ['Cliente A X.A.', 'Empresa B S.C.', 'Grupo C Mex', 'Comercial D', 'Logística E']) !!},
            datasets: [{
                label               : 'Tareas Totales',
                backgroundColor     : '#6c757d', // Gris elegante corporativo
                data                : {!! json_encode($clientsValues ?? [9, 7, 6, 4, 3]) !!}
            }]
        };
        new Chart(clientsCanvas, {
            type: 'horizontalBar',
            data: clientsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }]
                }
            }
        });
    });
</script>
@stop