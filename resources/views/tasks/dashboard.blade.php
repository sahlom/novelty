@extends('adminlte::page')

@section('title', 'Monitor General de TIC')

@section('content_header')
    <h1><i class="fas fa-desktop"></i> Monitor General de Operaciones</h1>
@stop

@section('content')
<div class="row">
    @foreach($tasks as $task)
    <div class="col-md-4">
        <div class="small-box {{ $task->priority->name == 'Urgente' ? 'bg-danger' : 'bg-info' }}">
            <div class="inner">
                <h5>{{ Str::limit($task->title, 30) }}</h5>
                <p>{{ $task->client->razon_social }}</p>
                <small>Asignado a: {{ $task->user->name ?? 'Pendiente' }}</small>
            </div>
            <div class="icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="small-box-footer">
                Estado: {{ $task->status->name }}
            </div>
        </div>
    </div>
    @endforeach
</div>
@stop