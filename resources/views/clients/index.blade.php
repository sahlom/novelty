@extends('adminlte::page')

@section('title', 'Clientes')

{{-- Dejamos solo el plugin base que sí funciona impecable en tu entorno --}}
@section('plugins.Datatables', true)

@section('content_header')
    <h1>Clientes</h1>
@stop

@section('content')
<!-- Bloque de alertas para capturar el éxito o error del controlador -->
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="icon fas fa-check"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div>
                <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm align-middle">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </a>
            </div>
            
            <div id="table-actions-container"></div>
        </div>
    </div>
    <div class="card-body">
        <table id="clients-table" class="table table-bordered table-striped clickable-table">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Razón Social / RFC</th>
                    <th>Contacto</th>
                    <th>Email / Tel</th>
                    <th>Vigencias (FIEL/CSD)</th>
                    <th>Docs</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr data-href="{{ route('clients.show', $client->id) }}">
                    <td>
                        <strong>{{ $client->razon_social }}</strong><br>
                        <small class="text-muted">{{ $client->rfc }}</small>
                    </td>
                    <td>{{ $client->contacto ?? 'N/A' }}</td>
                    <td>
                        <small>
                            <i class="fas fa-envelope"></i> {{ $client->email ?? 'S/N' }}<br>
                            <i class="fas fa-phone"></i> {{ $client->tel ?? 'S/N' }}
                        </small>
                    </td>
                    <td>
                        @php
                            // 1. Lógica de alertas para la FIEL
                            if (!$client->fiel_vigencia) {
                                // Si es null, lo pintamos de un color gris neutro
                                $fielAlert = 'text-muted'; 
                            } else {
                                $fielDate = \Carbon\Carbon::parse($client->fiel_vigencia);
                                
                                if ($fielDate->isPast()) {
                                    $fielAlert = 'text-danger font-weight-bold';
                                } else {
                                    $daysToFielExpire = now()->diffInDays($fielDate, false);
                                    if ($daysToFielExpire <= 5) {
                                        $fielAlert = 'text-danger font-weight-bold';
                                    } elseif ($daysToFielExpire <= 30) {
                                        $fielAlert = 'text-warning font-weight-bold';
                                    } else {
                                        $fielAlert = 'text-success'; // Solo es verde si está vigente de verdad
                                    }
                                }
                            }

                            // 2. Lógica de alertas para el CSD
                            if (!$client->csd_vigencia) {
                                // Si es null, lo pintamos de un color gris neutro
                                $csdAlert = 'text-muted'; 
                            } else {
                                $csdDate = \Carbon\Carbon::parse($client->csd_vigencia);
                                
                                if ($csdDate->isPast()) {
                                    $csdAlert = 'text-danger font-weight-bold';
                                } else {
                                    $daysToCsdExpire = now()->diffInDays($csdDate, false);
                                    if ($daysToCsdExpire <= 5) {
                                        $csdAlert = 'text-danger font-weight-bold';
                                    } elseif ($daysToCsdExpire <= 30) {
                                        $csdAlert = 'text-warning font-weight-bold';
                                    } else {
                                        $csdAlert = 'text-success'; // Solo es verde si está vigente de verdad
                                    }
                                }
                            }
                        @endphp
                        
                        <small>
                            <b>FIEL:</b> <span class="{{ $fielAlert }}">{{ $client->fiel_vigencia ? \Carbon\Carbon::parse($client->fiel_vigencia)->format('d/m/Y') : 'N/A' }}</span><br>
                            <b>CSD:</b> <span class="{{ $csdAlert }}">{{ $client->csd_vigencia ? \Carbon\Carbon::parse($client->csd_vigencia)->format('d/m/Y') : 'N/A' }}</span>
                        </small>
                    </td>
                    <td class="text-center" style="font-size: 1.1rem;">
                        <i class="fas fa-file-pdf {{ $client->csf ? 'text-success' : 'text-gray' }} mx-1" title="CSF"></i>
                        <i class="fas fa-file-invoice {{ $client->opinion ? 'text-success' : 'text-gray' }} mx-1" title="Opinión de Cumplimiento"></i>
                        <i class="fas fa-certificate {{ $client->fiel ? 'text-warning' : 'text-gray' }} mx-1" title="Archivos FIEL"></i>
                        <i class="fas fa-key {{ $client->csd ? 'text-info' : 'text-gray' }} mx-1" title="Archivos CSD"></i>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
        var table = $('#clients-table').DataTable({
            "responsive": true, 
            "autoWidth": false,
            "lengthChange": true,
            "lengthMenu": [[-1, 10, 50, 100], ["Todos", 10, 50, 100]],
            "pageLength": -1, 
            "dom": 'lfrtip', // Forzamos estructura limpia para permitir la inyección manual

            "buttons": [
                { extend: 'copy', text: '<i class="fas fa-copy"></i> Copiar', className: 'btn btn-sm btn-default' },
                { extend: 'excel', text: '<i class="fas fa-file-excel text-success"></i> Excel', className: 'btn btn-sm btn-default' },
                { extend: 'pdf', text: '<i class="fas fa-file-pdf text-danger"></i> PDF', className: 'btn btn-sm btn-default' },
                { extend: 'print', text: '<i class="fas fa-print text-info"></i> Imprimir', className: 'btn btn-sm btn-default' },
                { extend: 'colvis', text: '<i class="fas fa-columns text-secondary"></i> Columnas', className: 'btn btn-sm btn-default' }
            ],

            "language": {
                "emptyTable": "No hay información disponible en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ clientes",
                "infoEmpty": "Mostrando 0 a 0 de 0 clientes",
                "infoFiltered": "(Filtrado de _MAX_ clientes totales)",
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

        // Movemos el contenedor de botones al espacio reservado en el header
        table.buttons().container().appendTo('#table-actions-container');

        // Evento de redirección por fila protegiendo clics accidentales en iconos
        $('#clients-table tbody').on('click', 'tr', function (e) {
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