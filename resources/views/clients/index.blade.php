@extends('adminlte::page')

@section('title', 'Listado de Clientes')

@section('content_header')
    <h1>Gestión de Clientes</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Expedientes de Clientes</h3>
        <div class="card-tools">
            <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Nuevo Cliente
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Razón Social / RFC</th>
                    <th>Contacto</th>
                    <th>Email / Tel</th>
                    <th>Vigencias (FIEL/CSD)</th>
                    <th>Docs</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
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
                            // Lógica del semáforo preventivo para la FIEL
                            $fielAlert = 'text-success';
                            if ($client->fiel_vigencia) {
                                if ($client->fiel_vigencia->isPast()) {
                                    $fielAlert = 'text-danger font-weight-bold';
                                } else {
                                    $daysToFielExpire = now()->diffInDays($client->fiel_vigencia, false);
                                    if ($daysToFielExpire <= 5) {
                                        $fielAlert = 'text-danger font-weight-bold';
                                    } elseif ($daysToFielExpire <= 30) {
                                        $fielAlert = 'text-warning font-weight-bold';
                                    }
                                }
                            }

                            // Lógica del semáforo preventivo para el CSD
                            $csdAlert = 'text-success';
                            if ($client->csd_vigencia) {
                                if ($client->csd_vigencia->isPast()) {
                                    $csdAlert = 'text-danger font-weight-bold';
                                } else {
                                    $daysToCsdExpire = now()->diffInDays($client->csd_vigencia, false);
                                    if ($daysToCsdExpire <= 5) {
                                        $csdAlert = 'text-danger font-weight-bold';
                                    } elseif ($daysToCsdExpire <= 30) {
                                        $csdAlert = 'text-warning font-weight-bold';
                                    }
                                }
                            }
                        @endphp
                        <small>
                            <b>FIEL:</b> <span class="{{ $fielAlert }}">{{ $client->fiel_vigencia ? $client->fiel_vigencia->format('d/m/Y') : 'N/A' }}</span><br>
                            <b>CSD:</b> <span class="{{ $csdAlert }}">{{ $client->csd_vigencia ? $client->csd_vigencia->format('d/m/Y') : 'N/A' }}</span>
                        </small>
                    </td>
                    <td class="text-center" style="font-size: 1.1rem;">
                        {{-- Indicadores visuales basados en los campos de tu nuevo modelo --}}
                        <i class="fas fa-file-pdf {{ $client->csf ? 'text-success' : 'text-gray' }} mx-1" title="CSF (Constancia de Situación Fiscal)"></i>
                        <i class="fas fa-file-invoice {{ $client->opinion_cumplimiento ? 'text-success' : 'text-gray' }} mx-1" title="Opinión de Cumplimiento"></i>
                        <i class="fas fa-certificate {{ $client->fiel ? 'text-warning' : 'text-gray' }} mx-1" title="Archivos FIEL"></i>
                        <i class="fas fa-key {{ $client->csd ? 'text-info' : 'text-gray' }} mx-1" title="Archivos CSD"></i>
                    </td>
                    <td>
                        <div class="btn-group">
                            <a href="{{ route('clients.show', $client->id) }}" class="btn btn-xs btn-default text-primary mx-1 shadow" title="Ver Expediente">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-xs btn-default text-info mx-1 shadow" title="Editar">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>
                            @if(auth()->user()->role === 'admin')
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este cliente? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Eliminar">
                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop