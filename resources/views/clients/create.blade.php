@extends('adminlte::page')

@section('title', 'Nuevo Cliente')

@section('content_header')
    <h1>Registrar Nuevo Cliente</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Datos del Expediente Fiscal</h3>
            </div>
            
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
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
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="razon_social">Razón Social <span class="text-danger">*</span></label>
                                <input type="text" name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social') }}" placeholder="Ej. Novelty S.A. de C.V." required>
                            </div>

                            <div class="form-group">
                                <label for="rfc">RFC <span class="text-danger">*</span></label>
                                <input type="text" name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc') }}" placeholder="Ej. NOV123456XYZ" maxlength="13" style="text-transform: uppercase;" required>
                                <small class="text-muted">12 caracteres para morales, 13 para personas físicas.</small>
                            </div>

                            <div class="form-group">
                                <label for="contacto">Persona de Contacto</label>
                                <input type="text" name="contacto" id="contacto" class="form-control" value="{{ old('contacto') }}" placeholder="Ej. Ing. Alejandro Ramos">
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="email">Correo Electrónico</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="correo@cliente.com">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="tel">Teléfono</label>
                                        <input type="text" name="tel" id="tel" class="form-control" value="{{ old('tel') }}" placeholder="3312345678">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="bootstrap-timepicker">
                                <div class="form-group">
                                    <label for="fiel_vigencia">Vigencia de la FIEL (e.firma)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="fiel_vigencia" id="fiel_vigencia" class="form-control @error('fiel_vigencia') is-invalid @enderror" value="{{ old('fiel_vigencia') }}">
                                    </div>
                                    <small class="text-muted">Fecha de vencimiento del archivo .cer de la firma electrónica.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="csd_vigencia">Vigencia del CSD (Sellos Digitales)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="csd_vigencia" id="csd_vigencia" class="form-control @error('csd_vigencia') is-invalid @enderror" value="{{ old('csd_vigencia') }}">
                                </div>
                                <small class="text-muted">Requerido para la facturación electrónica.</small>
                            </div>

                            <div class="callout callout-info mt-4">
                                <h5><i class="fas fa-info"></i> Control de Documentos</h5>
                                <p class="small text-muted mb-0">
                                    Al crear el cliente, los indicadores de archivos (CSF, Opinión, FIEL y CSD) se iniciarán marcados en gris de manera automática. Podrás subir y actualizar los archivos físicos desde la sección "Editar Expediente" en los siguientes pasos de desarrollo.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Cliente
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@stop