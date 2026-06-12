@extends('adminlte::page')

@section('title', 'Prioridades')

{{-- Cargamos el DataTables base integrado en AdminLTE --}}
@section('plugins.Datatables', true)

@section('content_header')
    <h1>Catálogo de Prioridades</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <a href="{{ route('priorities.create') }}" class="btn btn-primary btn-sm align-middle">
                    <i class="fas fa-plus"></i> Nueva Prioridad
                </a>
            </div>
            
            <div id="table-actions-container"></div>
        </div>
    </div>
    <div class="card-body">
        
        <table id="priorities-table" class="table table-bordered table-striped clickable-table">
            <thead class="bg-primary text-white">
                <tr>
                    <th style="width: 80px">ID</th>
                    <th>Nombre de la Prioridad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($priorities as $priority)
                <tr data-href="{{ route('priorities.show', $priority->id) }}">
                    <td><strong>{{ $priority->id }}</strong></td>
                    <td>{{ $priority->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

{{-- Estilo estandarizado para el Hover Azul Premium en toda la aplicación --}}
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
<link class="js-dependency" rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
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
        var table = $('#priorities-table').DataTable({
            "responsive": true, 
            "autoWidth": false,
            "lengthChange": true,
            "lengthMenu": [[-1, 10, 50, 100], ["Todos", 10, 50, 100]],
            "pageLength": -1, 
            "dom": 'lfrtip', // Permite el acoplamiento manual de botones sin romper estilos nativos

            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copiar', className: 'btn btn-sm btn-default' },
                { extend: 'excel', text: '<i class="fas fa-file-excel text-success"></i> Excel', className: 'btn btn-sm btn-default' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf text-danger"></i> PDF', className: 'btn btn-sm btn-default' },
                { extend: 'print', text: '<i class="fas fa-print text-info"></i> Imprimir', className: 'btn btn-sm btn-default' },
                { extend: 'colvis', text: '<i class="fas fa-columns text-secondary"></i> Columnas', className: 'btn btn-sm btn-default' }
            ],

            "language": {
                "emptyTable": "No hay información disponible en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ prioridades",
                "infoEmpty": "Mostrando 0 a 0 de 0 prioridades",
                "infoFiltered": "(Filtrado de _MAX_ prioridades totales)",
                "lengthMenu": "Mostrar _MENU_ registros",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron resultados coincidentes",
                "paginate": {
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "buttons": {
                    "copyTitle": "Copiado al portapapeles",
                    "copySuccess": {
                        "_": "%d filas copiadas",
                        "1": "1 fila copiada"
                    },
                    "colvis": "Columnas visibles"
                }
            }
        });

        // Movemos las herramientas generadas al contenedor de la derecha del card-header
        table.buttons().container().appendTo('#table-actions-container');

        // Evento de redirección segura al dar clic en la fila completa
        $('#priorities-table tbody').on('click', 'tr', function (e) {
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
@endpush