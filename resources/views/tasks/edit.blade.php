@extends('adminlte::page')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editando Tarea: {{ $task->title }}</h3>
    </div>
    <div class="card-body">
        {{-- Mostrar errores de validación si existen --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Título</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $task->title) }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Cliente</label>
                        <select name="client_id" class="form-control" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $task->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->razon_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Asignado a</label>
                        <select name="user_id" class="form-control">
                            <option value="">Sin asignar</option>
                            @foreach($usuarios as $user)
                                <option value="{{ $user->id }}" {{ $task->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Área</label>
                        <select name="area_id" class="form-control" required>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $task->area_id == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="status_id" class="form-control" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ $task->status_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Prioridad</label>
                        <select name="priority_id" class="form-control" required>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->id }}" {{ $task->priority_id == $priority->id ? 'selected' : '' }}>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Fecha Compromiso</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ old('description', $task->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-default">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@stop