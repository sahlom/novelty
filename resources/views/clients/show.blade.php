@extends('adminlte::page')

@section('title', 'Expediente del Cliente - ' . $client->razon_social)

{{-- Activamos DataTables para las tablas de historial --}}
@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
        <h1>Expediente Digital: {{ $client->razon_social }}</h1>
        
        <div class="mt-2 mt-md-0">
            <a href="{{ route('tasks.create', ['client_id' => $client->id]) }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                <i class="fas fa-plus"></i> Nueva Tarea
            </a>
            
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                <i class="fas fa-edit"></i> Editar Cliente
            </a>

            @if(auth()->user()->role === 'admin')
                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás absolutamente seguro de eliminar este cliente? Se borrará todo su historial permanentemente.')">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm">
                        <i class="fas fa-trash-alt"></i> Eliminar Cliente
                    </button>
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-primary card-outline h-100">
            <div class="card-body box-profile d-flex flex-column justify-content-center">
                <div class="text-center mb-3">
                    <i class="fas fa-building fa-3x text-muted"></i>
                </div>

                <h3 class="profile-username text-center font-weight-bold" style="font-size: 1.25rem;">
                    {{ $client->razon_social }}
                </h3>
                <p class="text-muted text-center mb-3">{{ $client->rfc }}</p>

                <ul class="list-group list-group-unbordered mb-0">
                    <li class="list-group-item">
                        <b>Contacto:</b> <span class="float-right text-dark">{{ $client->contacto ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item">
                        <b>Teléfono:</b> <span class="float-right text-dark">{{ $client->tel ?? 'S/N' }}</span>
                    </li>
                    <li class="list-group-item" style="border-bottom: none;">
                        <b>Email:</b> <br>
                        <span class="text-dark d-block text-break mt-1">
                            <i class="fas fa-envelope text-muted mr-1"></i> {{ $client->email ?? 'S/N' }}
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card card-secondary card-outline h-100">
            <div class="card-header">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-folder-open text-muted mr-1"></i> Documentación SAT
                </h3>
            </div>
            <div class="card-body">
                @php
                    // Lógica de semáforos preventivos para vigencias
                    $fielClass = 'badge-success'; $fielTxt = 'Vigente';
                    if($client->fiel_vigencia) {
                        $fielDate = \Carbon\Carbon::parse($client->fiel_vigencia);
                        if($fielDate->isPast()) { $fielClass = 'badge-danger'; $fielTxt = 'Vencida'; }
                        else {
                            $daysToFiel = now()->diffInDays($fielDate, false);
                            if ($daysToFiel <= 5) { $fielClass = 'badge-danger'; $fielTxt = 'Crítico (< 5 días)'; }
                            elseif ($daysToFiel <= 30) { $fielClass = 'badge-warning'; $fielTxt = 'Próxima a vencer'; }
                        }
                    }
                    
                    $csdClass = 'badge-success'; $csdTxt = 'Vigente';
                    if($client->csd_vigencia) {
                        $csdDate = \Carbon\Carbon::parse($client->csd_vigencia);
                        if($csdDate->isPast()) { $csdClass = 'badge-danger'; $csdTxt = 'Vencido'; }
                        else {
                            $daysToCsd = now()->diffInDays($csdDate, false);
                            if ($daysToCsd <= 5) { $csdClass = 'badge-danger'; $csdTxt = 'Crítico (< 5 días)'; }
                            elseif ($daysToCsd <= 30) { $csdClass = 'badge-warning'; $csdTxt = 'Próximo a vencer'; }
                        }
                    }

                    // Separación analítica de tareas
                    $closedStatusNames = ['cerrada', 'cerrado', 'completada', 'completado', 'resuelta', 'resuelto'];
                    
                    $openTasks = $client->tasks->filter(function($task) use ($closedStatusNames) {
                        return !in_array(strtolower($task->status?->name ?? ''), $closedStatusNames);
                    });

                    $closedTasks = $client->tasks->filter(function($task) use ($closedStatusNames) {
                        return in_array(strtolower($task->status?->name ?? ''), $closedStatusNames);
                    });
                @endphp

                <div class="row text-center mb-3">
                    <div class="col-sm-6">
                        <div class="info-box bg-light shadow-sm mb-2">
                            <div class="info-box-content">
                                <span class="info-box-text text-muted small">VENCIMIENTO FIEL</span>
                                <span class="info-box-number text-dark">
                                    {{ $client->fiel_vigencia ? \Carbon\Carbon::parse($client->fiel_vigencia)->format('d/m/Y') : 'No Registrada' }}
                                </span>
                                <span class="badge {{ $fielClass }} mt-1 py-1" style="font-size: 85%;">{{ $fielTxt }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="info-box bg-light shadow-sm mb-2">
                            <div class="info-box-content">
                                <span class="info-box-text text-muted small">VENCIMIENTO CSD</span>
                                <span class="info-box-number text-dark">
                                    {{ $client->csd_vigencia ? \Carbon\Carbon::parse($client->csd_vigencia)->format('d/m/Y') : 'No Registrado' }}
                                </span>
                                <span class="badge {{ $csdClass }} mt-1 py-1" style="font-size: 85%;">{{ $csdTxt }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div><i class="fas fa-file-pdf text-danger mr-2"></i> <small class="font-weight-bold">Constancia de Situación Fiscal (CSF)</small></div>
                        <span class="badge {{ $client->csf ? 'badge-success' : 'badge-secondary' }} py-1 px-2">{{ $client->csf ? 'Cargado' : 'Faltante' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div><i class="fas fa-file-invoice text-success mr-2"></i> <small class="font-weight-bold">Opinión de Cumplimiento</small></div>
                        <span class="badge {{ $client->opinion_cumplimiento ? 'badge-success' : 'badge-secondary' }} py-1 px-2">{{ $client->opinion_cumplimiento ? 'Cargado' : 'Faltante' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div><i class="fas fa-certificate text-warning mr-2"></i> <small class="font-weight-bold">Certificados FIEL (.cer / .key)</small></div>
                        <span class="badge {{ $client->fiel ? 'badge-success' : 'badge-secondary' }} py-1 px-2">{{ $client->fiel ? 'Cargado' : 'Faltante' }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div><i class="fas fa-key text-info mr-2"></i> <small class="font-weight-bold">Sellos Digitales CSD (.cer / .key)</small></div>
                        <span class="badge {{ $client->csd ? 'badge-success' : 'badge-secondary' }} py-1 px-2">{{ $client->csd ? 'Cargado' : 'Faltante' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="tasksTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="open-tasks-tab" data-toggle="pill" href="#open-tasks" role="tab" aria-controls="open-tasks" aria-selected="true">
                            <i class="fas fa-folder-open mr-1"></i> Tareas Abiertas 
                            <span class="badge badge-light ml-1 font-weight-bold">{{ $openTasks->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="closed-tasks-tab" data-toggle="pill" href="#closed-tasks" role="tab" aria-controls="closed-tasks" aria-selected="false">
                            <i class="fas fa-folder mr-1"></i> Tareas Cerradas 
                            <span class="badge badge-light ml-1 font-weight-bold">{{ $closedTasks->count() }}</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="card-body">
                <div class="tab-content" id="tasksTabContent">
                    
                    <div class="tab-pane fade show active" id="open-tasks" role="tabpanel" aria-labelledby="open-tasks-tab">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <h5 class="text-secondary font-weight-bold m-0 mb-2 mb-md-0">Pendientes de Soporte Técnico</h5>
                            <div id="open-actions-container"></div>
                        </div>
                        
                        @if($openTasks->count() > 0)
                            <table id="open-tasks-table" class="table table-bordered table-striped clickable-table w-100">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 70px">ID</th>
                                        <th>Título / Descripción</th>
                                        <th>Área</th>
                                        <th>Responsable</th>
                                        <th style="width: 130px">Estado</th>
                                        <th style="width: 130px">Prioridad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($openTasks as $task)
                                        @php
                                            // Semáforo dinámico para ESTADOS abiertos
                                            $statusName = strtolower($task->status?->name ?? '');
                                            $statusBadge = 'badge-info'; // Por defecto azul (En Proceso / Asignada)
                                            
                                            if (str_contains($statusName, 'nuev') || str_contains($statusName, 'pendient') || str_contains($statusName, 'registr')) {
                                                $statusBadge = 'badge-warning'; // Amarillo para atención inicial
                                            } elseif (str_contains($statusName, 'espera') || str_contains($statusName, 'detenid') || str_contains($statusName, 'pausa')) {
                                                $statusBadge = 'badge-danger'; // Rojo si el flujo se detuvo
                                            }

                                            // Semáforo dinámico para PRIORIDADES abiertas
                                            $priorityName = strtolower($task->priority?->name ?? '');
                                            $priorityBadge = 'badge-secondary'; // Por defecto gris (Baja)
                                            
                                            if (str_contains($priorityName, 'alta') || str_contains($priorityName, 'urgent') || str_contains($priorityName, 'critic')) {
                                                $priorityBadge = 'badge-danger text-uppercase font-weight-bold'; // Rojo llamativo
                                            } elseif (str_contains($priorityName, 'medi') || str_contains($priorityName, 'normal')) {
                                                $priorityBadge = 'badge-warning'; // Amarillo
                                            }
                                        @endphp
                                        <tr data-href="{{ route('tasks.show', $task->id) }}">
                                            <td><strong>#{{ $task->id }}</strong></td>
                                            <td>
                                                <strong>{{ $task->title }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($task->description, 90) }}</small>
                                            </td>
                                            <td>{{ $task->area?->name ?? 'N/A' }}</td>
                                            <td>{{ $task->user?->display_name ?? $task->user?->name ?? 'Sin Asignar' }}</td>
                                            <td>
                                                <span class="badge {{ $statusBadge }} py-1 px-2 w-100 text-center shadow-sm">
                                                    {{ $task->status?->name ?? 'Abierta' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $priorityBadge }} py-1 px-2 w-100 text-center shadow-sm">
                                                    {{ $task->priority?->name ?? 'Normal' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-secondary">¡Al día!</h5>
                                <p class="text-muted small mb-0">No hay tareas abiertas pendientes para este cliente.</p>
                            </div>
                        @endif
                    </div>

                    <div class="tab-pane fade" id="closed-tasks" role="tabpanel" aria-labelledby="closed-tasks-tab">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <h5 class="text-secondary font-weight-bold m-0 mb-2 mb-md-0">Histórico de Casos Resueltos</h5>
                            <div id="closed-actions-container"></div>
                        </div>

                        @if($closedTasks->count() > 0)
                            <table id="closed-tasks-table" class="table table-bordered table-striped clickable-table w-100">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 70px">ID</th>
                                        <th>Título / Descripción</th>
                                        <th>Área</th>
                                        <th>Responsable</th>
                                        <th style="width: 130px">Estado</th>
                                        <th style="width: 130px">Prioridad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($closedTasks as $task)
                                        <tr data-href="{{ route('tasks.show', $task->id) }}">
                                            <td><strong>#{{ $task->id }}</strong></td>
                                            <td>
                                                <strong>{{ $task->title }}</strong><br>
                                                <small class="text-muted">{{ Str::limit($task->description, 90) }}</small>
                                            </td>
                                            <td>{{ $task->area?->name ?? 'N/A' }}</td>
                                            <td>{{ $task->user?->display_name ?? $task->user?->name ?? 'Sin Asignar' }}</td>
                                            <td>
                                                <span class="badge badge-success py-1 px-2 w-100 text-center shadow-sm">
                                                    <i class="fas fa-check-circle mr-1"></i> {{ $task->status?->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light border py-1 px-2 w-100 text-center text-muted">
                                                    {{ $task->priority?->name ?? 'Normal' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                <h5 class="text-secondary">Sin Historial Cerrado</h5>
                                <p class="text-muted small mb-0">No se registran tareas cerradas o archivadas en este expediente.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .clickable-table tbody tr {
        cursor: pointer;
        transition: background-color 0.15s ease-in-out;
    }
    .clickable-table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.15) !important; 
    }
    .nav-tabs .nav-link.active {
        background-color: #ffffff !important;
        color: #007bff !important;
        border-bottom-color: transparent !important;
    }
</style>
@stop

@push('js')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<script>
    $(document).ready(function () {
        var indexTableConfig = {
            "responsive": true,
            "autoWidth": false,
            "lengthChange": true,
            "lengthMenu": [[-1, 10, 50, 100], ["Todos", 10, 50, 100]],
            "pageLength": -1,
            "dom": 'lfrtip',
            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copiar', className: 'btn btn-sm btn-default' },
                { extend: 'excel', text: '<i class="fas fa-file-excel text-success"></i> Excel', className: 'btn btn-sm btn-default' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf text-danger"></i> PDF', className: 'btn btn-sm btn-default' },
                { extend: 'print', text: '<i class="fas fa-print text-info"></i> Imprimir', className: 'btn btn-sm btn-default' },
                { extend: 'colvis', text: '<i class="fas fa-columns"></i> Columnas', className: 'btn btn-sm btn-default' }
            ],
            "language": {
                "emptyTable": "No hay información disponible en esta sección",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                "infoEmpty": "Mostrando 0 a 0 de 0 registros",
                "infoFiltered": "(Filtrado de _MAX_ registros totales)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron coincidencias",
                "paginate": { "next": "Sig.", "previous": "Ant." }
            }
        };

        if ($('#open-tasks-table').length) {
            var openTable = $('#open-tasks-table').DataTable(indexTableConfig);
            openTable.buttons().container().appendTo('#open-actions-container');
        }

        if ($('#closed-tasks-table').length) {
            var closedTable = $('#closed-tasks-table').DataTable(indexTableConfig);
            closedTable.buttons().container().appendTo('#closed-actions-container');
        }

        $('.clickable-table tbody').on('click', 'tr', function (e) {
            if ($(e.target).is('button') || $(e.target).is('i') || $(e.target).is('a') || $(e.target).hasClass('badge')) {
                return;
            }
            var url = $(this).data('href');
            if (url) {
                window.location.href = url;
            }
        });

        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
        });
    });
</script>
@endpush