@extends('adminlte::page')

@section('title', 'Editar Estatus')

@section('content_header')
    <h1>Editar Estatus</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Editar Estatus: {{ $status->name }}</h3>
            </div>
            
            <form action="{{ route('statuses.update', $status->id) }}" method="POST" id="editStatusForm">
                @csrf
                @method('PUT') {{-- Directiva obligatoria para ruteo de actualización en Laravel --}}
                
                <div class="card-body">
                    
                    {{-- Bloque de errores global idéntico al de clientes --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5><i class="icon fas fa-ban"></i> Error en la captura</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        {{-- Ancho completo y responsivo por defecto --}}
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">Nombre del Estatus <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $status->name) }}" 
                                       placeholder="Ej. Soporte Técnico" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('statuses.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Listo por si requieres agregar lógica JS más adelante
    });
</script>
@stop