@extends('adminlte::page')

@section('title', 'Detalle del Área - ' . $area->name)

{{-- Activamos DataTables base para el control del historial de tareas --}}
@section('plugins.Datatables', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Área: {{ $area->name }}</h1>
        <a href="{{ route('areas.index') }}" class="btn btn-default btn-sm shadow-sm">
            <i class="fas fa-arrow-left"></i> Volver al Listado
        </a>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center mb-3">
                    <i class="fas fa-building fa-3x text-primary"></i>
                </div>

                <h3 class="profile-username text-center font-weight-bold">{{ $area->name }}</h3>
                <p class="text-muted text-center">Departamento de la Institución</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>ID del Registro:</b> <a class="float-right text-dark font-weight-bold">#{{ $area->id }}</a>
                    </li>
                    <li class="list-group-item">
                        <b>Tareas Registradas:</b> <a class="float-right text-primary font-weight-bold">{{ $area->tasks?->count() ?? 0 }}</a>
                    </li>
                </ul>

                <div class="mt-4">
                    <a href="{{ route('areas.edit', $area->id) }}" class="btn btn-info btn-block shadow-sm mb-2">
                        <i class="fas fa-edit"></i> Editar Nombre
                    </a>

                    @if(auth()->user()->role === 'admin')
                        <hr>
                        <form action="{{ route('areas.destroy', $area->id) }}" 
                              method="POST" 
                              class="d-inline" 
                              onsubmit="return confirm('¿Estás seguro de eliminar esta área? Las tareas asociadas quedarán huérfanas.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block shadow-sm">
                                <i class="fas fa-trash-alt"></i> Eliminar Área
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="card-title font-weight-bold text-dark">
                        <i class="fas fa-tasks text-muted mr-1"></i> Tareas de este Departamento
                    </h3>
                    <div id="area-tasks-actions"></div>
                </div>
            </div>
            <div class="card-body">
                {{-- Validamos contra la relación real que sí tienes: tasks --}}
                @if($area->tasks && $area->tasks->count() > 0)
                    <table id="area-tasks-table" class="table table-bordered table-striped clickable-table">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 60px">Folio</th>
                                <th>Asunto / Descripción</th>
                                <th>Estatus</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($area->tasks as $task)
                                {{-- Suponiendo que tienes una ruta para ver el detalle de la tarea --}}
                                <tr data-href="{{ route('tasks.show', $task->id) }}">
                                    <td><strong>#{{ $task->id }}</strong></td>
                                    <td>
                                        <strong>{{ $task->title ?? $task->asunto ?? 'Sin Título' }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($task->description ?? $task->descripcion ?? '', 60) }}</small>
                                    </td>
                                    <td>
                                        {{-- Aquí puedes renderizar el badge del estatus si tienes la relación en la Tarea --}}
                                        <span class="badge badge-secondary py-1 px-2">
                                            {{ $task->status?->name ?? 'Asignada' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">No hay tareas o incidencias registradas para este departamento.</p>
                    </div>
                @endif
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
</style>
@stop

@push('js')
@if($area->tasks && $area->tasks->count() > 0)
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        var table = $('#area-tasks-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "lengthChange": false,
            "pageLength": 10,
            "dom": 'frtip',

            "buttons": [
                { extend: 'excel', text: '<i class="fas fa-file-excel text-success"></i> Excel', className: 'btn btn-sm btn-default' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf text-danger"></i> PDF', className: 'btn btn-sm btn-default' }
            ],

            "language": {
                "emptyTable": "No hay información disponible",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ tareas",
                "infoEmpty": "Mostrando 0 a 0 de 0 tareas",
                "infoFiltered": "(Filtrado de _MAX_ totales)",
                "search": "Buscar tarea:",
                "zeroRecords": "No se encontraron coincidencias",
                "paginate": {
                    "next": "Sig.",
                    "previous": "Ant."
                }
            }
        });

        table.buttons().container().appendTo('#area-tasks-actions');

        // Redirección al clic de la fila hacia el show de la tarea
        $('#area-tasks-table tbody').on('click', 'tr', function (e) {
            if ($(e.target).is('button') || $(e.target).is('i') || $(e.target).is('a')) {
                return;
            }
            var url = $(this).data('href');
            if (url) {
                window.location.href = url;
            }
        });
    });
</script>
@endif
@endpush