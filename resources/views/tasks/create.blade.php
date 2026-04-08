@extends('adminlte::page')

@section('title', 'Crear Tarea')

@section('content_header')
    <h1>Registrar Nueva Tarea</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Formulario de Tareas</h3>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form action="{{ route('tasks.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Título --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Título de la Tarea</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                    </div>

                    {{-- Cliente --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Cliente</label>
                            <select name="client_id" class="form-control" required>
                                <option value="">Seleccione un cliente...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->razon_social }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Usuario Responsable --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Asignar a (Usuario)</label>
                            <select name="user_id" class="form-control">
                                <option value="">Sin asignar</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Área --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Área</label>
                            <select name="area_id" class="form-control" required>
                                <option value="">Seleccione área...</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Estado Inicial</label>
                            <select name="status_id" class="form-control" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : ( $status->name == 'Pendiente' ? 'selected' : '' ) }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Prioridad --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Prioridad</label>
                            <select name="priority_id" class="form-control" required>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->id }}" {{ old('priority_id') == $priority->id ? 'selected' : ( $priority->name == 'Media' ? 'selected' : '' ) }}>
                                        {{ $priority->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Fecha de Entrega --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Fecha Compromiso</label>
                            <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Descripción detallada</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Crear Tarea</button>
                    <a href="{{ route('tasks.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>


        </div>
    </div>
@stop