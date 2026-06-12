@extends('adminlte::page')

@section('title', 'Áreas')

{{-- Cargamos únicamente el DataTable base integrado en AdminLTE --}}
@section('plugins.Datatables', true)

@section('content_header')
    <h1>Áreas de la Institución</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <a href="{{ route('areas.create') }}" class="btn btn-primary btn-sm align-middle">
                    <i class="fas fa-plus"></i> Nueva Área
                </a>
            </div>
            
            <div id="table-actions-container"></div>
        </div>
    </div>
    <div class="card-body">
        
        <table id="areas-table" class="table table-bordered table-striped clickable-table">
            <thead class="bg-primary text-white">
                <tr>
                    <th style="width: 80px"># ID</th>
                    <th>Nombre del Área</th>
                </tr>
            </thead>
            <tbody>
                @foreach($areas as $area)
                <tr data-href="{{ route('areas.show', $area->id) }}">
                    <td><strong>{{ $area->id }}</strong></td>
                    <td>{{ $area->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop

{{-- Mismo estilo estético de hover azul traslúcido para mantener uniformidad --}}
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
        var table = $('#areas-table').DataTable({
            "responsive": true, 
            "autoWidth": false,
            "lengthChange": true,
            "lengthMenu": [[-1, 10, 50, 100], ["Todos", 10, 50, 100]],
            "pageLength": -1, 
            "dom": 'lfrtip', // Mantiene la estructura de layouts compatible con la inyección

            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copiar', className: 'btn btn-sm btn-default' },
                { extend: 'excel', text: '<i class="fas fa-file-excel text-success"></i> Excel', className: 'btn btn-sm btn-default' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf text-danger"></i> PDF', className: 'btn btn-sm btn-default' },
                { extend: 'print', text: '<i class="fas fa-print text-info"></i> Imprimir', className: 'btn btn-sm btn-default' },
                { extend: 'colvis', text: '<i class="fas fa-columns text-secondary"></i> Columnas', className: 'btn btn-sm btn-default' }
            ],

            "language": {
                "emptyTable": "No hay información disponible en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ áreas",
                "infoEmpty": "Mostrando 0 a 0 de 0 áreas",
                "infoFiltered": "(Filtrado de _MAX_ áreas totales)",
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

        // Desplazamos las herramientas de exportación al card-header
        table.buttons().container().appendTo('#table-actions-container');

        // Captura el clic en la fila completa y redirige con seguridad
        $('#areas-table tbody').on('click', 'tr', function (e) {
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