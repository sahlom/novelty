@extends('adminlte::page')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Detalles de la Tarea</h3>
            </div>
            <div class="card-body">
                <h4>{{ $task->title }}</h4>
                <hr>
                <p><strong>Descripción:</strong></p>
                <p>{{ $task->description }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Información General</h3>
            </div>
            <div class="card-body p-0">
                <table class="table">
                    <tr><th>Cliente</th><td>{{ $task->client->razon_social }}</td></tr>
                    <tr><th>Área</th><td>{{ $task->area->name }}</td></tr>
                    <tr><th>Estado</th><td><span class="badge badge-info">{{ $task->status->name }}</span></td></tr>
                    <tr><th>Responsable</th><td>{{ $task->user->name ?? 'Sin asignar' }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop