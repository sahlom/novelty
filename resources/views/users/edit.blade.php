@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario: {{ $user->display_name }}</h1>
@stop

@section('content')
<div class="card card-info">
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nombre Completo</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="display_name">Nombre Corto (Para tablas)</label>
                        <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $user->display_name) }}" maxlength="20" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="role">Rol de Usuario</label>
                        <select name="role" class="form-control">
                            <option value="usuario" {{ $user->role == 'usuario' ? 'selected' : '' }}>Usuario (Técnico)</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr>
            <p class="text-muted">Deje los campos de contraseña en blanco si no desea cambiarla.</p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">Nueva Contraseña</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info">Actualizar Usuario</button>
            <a href="{{ route('users.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop