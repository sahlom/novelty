@extends('adminlte::page')

@section('title', 'Prioridad - ' . $priority->name)

{{-- Activamos DataTables base para las tablas de tareas --}}
@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
        <h1>{{ $priority->name }}</h1>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('priorities.create') }}" class="btn btn-primary btn-sm align-middle">
                <i class="fas fa-plus"></i> Nuevo
            </a>
            <a href="{{ route('priorities.edit', $priority->id) }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                <i class="fas fa-edit"></i> Editar
            </a>

            @if(auth()->user()->role === 'admin')
                @if($priority->tasks()->exists())
                    <button class="btn btn-primary btn-sm shadow-sm mr-2" style="opacity: 0.6; cursor: not-allowed;" 
                            title="No se puede eliminar: Esta Prioridad cuenta con tareas registradas en el sistema." disabled>
                        <i class="fas fa-ban"></i> Eliminar
                    </button>
                @else
                    <form action="{{ route('priorities.destroy', $priority->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás absolutamente seguro de eliminar esta Prioridad? Esta acción borrará el registro del catálogo de forma irreversible.')">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-primary btn-sm shadow-sm mr-2">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
@stop

@section('content')
@php
    $closedStatusNames = ['cerrada', 'cerrado', 'completada', 'completado', 'resuelta', 'resuelto'];

    $openTasks = $priority->tasks->filter(function($task) use ($closedStatusNames) {
        return !in_array(strtolower($task->status?->name ?? ''), $closedStatusNames);
    });

    $closedTasks = $priority->tasks->filter(function($task) use ($closedStatusNames) {
        return in_array(strtolower($task->status?->name ?? ''), $closedStatusNames);
    });
@endphp
<div class="row">
    <div class="col-12 mb-4">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 text-primary">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-dark mb-1">{{ $priority->name }}</h3>
                        </div>
                    </div>
                    
                    <div>
                        {{-- <span><strong>Total Tareas:</strong> {{ $priority->tasks?->count() ?? 0 }}</span> --}}
                        <h3 class="font-weight-bold text-dark mb-1">Total Tareas: {{ $priority->tasks?->count() ?? 0 }}</h3>
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
                    
                    {{-- Pestaña Tareas Abiertas --}}
                    <div class="tab-pane fade show active" id="open-tasks" role="tabpanel" aria-labelledby="open-tasks-tab">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <h5 class="text-secondary font-weight-bold m-0 mb-2 mb-md-0">Tareas Abiertas</h5>
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
                                            $statusName = strtolower($task->status?->name ?? '');
                                            $statusBadge = 'badge-info';
                                            
                                            if (str_contains($statusName, 'nuev') || str_contains($statusName, 'pendient') || str_contains($statusName, 'registr')) {
                                                $statusBadge = 'badge-warning';
                                            } elseif (str_contains($statusName, 'espera') || str_contains($statusName, 'detenid') || str_contains($statusName, 'pausa')) {
                                                $statusBadge = 'badge-danger';
                                            }

                                            $priorityName = strtolower($task->priority?->name ?? '');
                                            $priorityBadge = 'badge-secondary';
                                            
                                            if (str_contains($priorityName, 'alta') || str_contains($priorityName, 'urgent') || str_contains($priorityName, 'critic')) {
                                                $priorityBadge = 'badge-danger text-uppercase font-weight-bold';
                                            } elseif (str_contains($priorityName, 'medi') || str_contains($priorityName, 'normal')) {
                                                $priorityBadge = 'badge-warning';
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
                                <p class="text-muted small mb-0">No hay tareas abiertas pendientes para esta Prioridad.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Pestaña Tareas Cerradas --}}
                    <div class="tab-pane fade" id="closed-tasks" role="tabpanel" aria-labelledby="closed-tasks-tab">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                            <h5 class="text-secondary font-weight-bold m-0 mb-2 mb-md-0">Tareas Cerradas</h5>
                            <div id="closed-actions-container"></div>
                        </div>

                        @if($closedTasks->count() > 0)
                            <table id="closed-tasks-table" class="table table-bordered table-striped clickable-table w-100">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 70px">ID</th>
                                        <th>Título / Descripción</th>
                                        <th>Prioridad</th>
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
                                <p class="text-muted small mb-0">No se hay tareas cerradas en esta Prioridad.</p>
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
    /* Pequeño ajuste para que el selector de registros y los botones no se encimen */
    .dataTables_length {
        float: left;
        margin-right: 15px;
    }
    .dt-buttons {
        margin-bottom: 10px;
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
            "pageLength": -1, // Por buena práctica en usabilidad, iniciamos mostrando 10 en lugar de "Todos" directamente
            "dom": 'lBfrtip', // SOLUCIÓN: Agregada la 'B' junto a la 'l' para pintar simultáneamente el selector y los botones
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