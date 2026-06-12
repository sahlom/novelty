@extends('adminlte::page')

@section('title', 'Editar Cliente')

@section('content_header')
    <h1>Editar Expediente de Cliente</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Modificar Datos de: {{ $client->razon_social }}</h3>
            </div>
            
            <form action="{{ route('clients.update', $client->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5><i class="icon fas fa-ban"></i> Error al actualizar</h5>
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="razon_social">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $client->razon_social) }}" required>
                            </div>

                            <div class="form-group">
                                <label for="rfc">RFC <span class="text-danger">*</span></label>
                                <input type="text" name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc', $client->rfc) }}" maxlength="13" style="text-transform: uppercase;" required>
                                <small class="text-muted">Si se modifica, la validación se encargará de que no choque con otro cliente.</small>
                            </div>

                            <div class="form-group">
                                <label for="contacto">Persona de Contacto</label>
                                <input type="text" name="contacto" id="contacto" class="form-control" value="{{ old('contacto', $client->contacto) }}">
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $client->email) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="tel">Teléfono</label>
                                        <input type="text" name="tel" id="tel" class="form-control" value="{{ old('tel', $client->tel) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fiel_vigencia">Vigencia de la FIEL (e.firma)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="fiel_vigencia" id="fiel_vigencia" class="form-control @error('fiel_vigencia') is-invalid @enderror" value="{{ old('fiel_vigencia', $client->fiel_vigencia ? $client->fiel_vigencia->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="csd_vigencia">Vigencia del CSD (Sellos Digitales)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="csd_vigencia" id="csd_vigencia" class="form-control @error('csd_vigencia') is-invalid @enderror" value="{{ old('csd_vigencia', $client->csd_vigencia ? $client->csd_vigencia->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="callout callout-warning mt-4">
                                <h5><i class="fas fa-exclamation-triangle"></i> Próxima Fase: Carga de Archivos</h5>
                                <p class="small text-muted mb-0">
                                    En la siguiente etapa implementaremos los campos de tipo <code>file</code> para almacenar físicamente en el servidor la Constancia (CSF), la Opinión de Cumplimiento, y los certificados (.cer / .key). Por ahora, modificar las fechas actualizará directamente los semáforos preventivos de la lista.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-sync-alt"></i> Actualizar Expediente
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop