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
            
            {{-- 1. AGREGADO: enctype para permitir subida de archivos --}}
            <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" id="createClientForm">
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
                        {{-- Columna Izquierda: Datos Generales --}}
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

                        {{-- Columna Derecha: Vigencias y Carga de Archivos Optimizada --}}
                        <div class="col-md-6">

                            {{-- Bloque FIEL Compac-Row --}}
                            <div class="form-group mb-4">
                                <label>Expediente FIEL (e.firma) <span id="req_fiel" class="text-danger d-none">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend" style="width: 35%;">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        <input type="date" name="fiel_vigencia" id="fiel_vigencia" 
                                            class="form-control @error('fiel_vigencia') is-invalid @enderror" 
                                            value="{{ old('fiel_vigencia') }}" 
                                            title="Fecha de vencimiento">
                                    </div>
                                    
                                    <div class="custom-file">
                                        <input type="file" name="file_fiel" id="file_fiel" 
                                            class="custom-file-input @error('file_fiel') is-invalid @enderror" 
                                            accept=".zip">
                                        <label class="custom-file-label" for="file_fiel" data-browse="Buscar ZIP">Seleccionar paquete FIEL...</label>
                                    </div>
                                </div>
                                <small class="text-muted">Indica la fecha de vencimiento y adjunta el archivo .zip correlativo.</small>
                            </div>

                            <hr class="my-4">

                            {{-- Bloque CSD Compac-Row --}}
                            <div class="form-group mb-4">
                                <label>Sellos Digitales (CSD) <span id="req_csd" class="text-danger d-none">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend" style="width: 35%;">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        <input type="date" name="csd_vigencia" id="csd_vigencia" 
                                            class="form-control @error('csd_vigencia') is-invalid @enderror" 
                                            value="{{ old('csd_vigencia') }}" 
                                            title="Fecha de vencimiento">
                                    </div>
                                    
                                    <div class="custom-file">
                                        <input type="file" name="file_csd" id="file_csd" 
                                            class="custom-file-input @error('file_csd') is-invalid @enderror" 
                                            accept=".zip">
                                        <label class="custom-file-label" for="file_csd" data-browse="Buscar ZIP">Seleccionar paquete CSD...</label>
                                    </div>
                                </div>
                                <small class="text-muted">Indica la fecha de vencimiento y adjunta el archivo .zip correlativo.</small>
                            </div>

                            <hr class="my-4">

                            {{-- Documentación SAT Opcional (Se queda igual en estructura base) --}}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="file_csf">Constancia Fiscal (CSF)</label>
                                        <div class="custom-file">
                                            <input type="file" name="file_csf" id="file_csf" class="custom-file-input" accept=".pdf">
                                            <label class="custom-file-label" for="file_csf" data-browse="PDF">Subir CSF...</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="file_opinion">Opinión de Cumplimiento</label>
                                        <div class="custom-file">
                                            <input type="file" name="file_opinion" id="file_opinion" class="custom-file-input" accept=".pdf">
                                            <label class="custom-file-label" for="file_opinion" data-browse="PDF">Subir Opinión...</label>
                                        </div>
                                    </div>
                                </div>
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

@section('js')
<script>
    $(document).ready(function () {
        // Render dinámico del nombre del archivo en los inputs de Bootstrap 4 (AdminLTE)
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        const $fileFiel = $('#file_fiel');
        const $fielVigencia = $('#fiel_vigencia');
        const $reqFiel = $('#req_fiel');

        const $fileCsd = $('#file_csd');
        const $csdVigencia = $('#csd_vigencia');
        const $reqCsd = $('#req_csd');

        // Monitoreo en tiempo real para activar asteriscos visuales de "obligatorio"
        $fileFiel.on('change', function() {
            if (this.files.length > 0) {
                $reqFiel.removeClass('d-none');
                $fielVigencia.attr('required', true);
            } else {
                $reqFiel.addClass('d-none');
                $fielVigencia.attr('required', false);
            }
        });

        $fileCsd.on('change', function() {
            if (this.files.length > 0) {
                $reqCsd.removeClass('d-none');
                $csdVigencia.attr('required', true);
            } else {
                $reqCsd.addClass('d-none');
                $csdVigencia.attr('required', false);
            }
        });

        // Interceptor del Submit: Doble validación estricta antes de mandar datos a PHP
        $('#createClientForm').on('submit', function(e) {
            let errores = [];

            if ($fileFiel[0].files.length > 0 && !$fielVigencia.val()) {
                errores.push("Has seleccionado un paquete FIEL, es obligatorio capturar su fecha de vigencia.");
                $fielVigencia.addClass('is-invalid');
            } else {
                $fielVigencia.removeClass('is-invalid');
            }

            if ($fileCsd[0].files.length > 0 && !$csdVigencia.val()) {
                errores.push("Has seleccionado sellos CSD, es obligatorio capturar su fecha de vigencia.");
                $csdVigencia.addClass('is-invalid');
            } else {
                $csdVigencia.removeClass('is-invalid');
            }

            if (errores.length > 0) {
                e.preventDefault(); // Frena el envío
                alert(errores.join("\n"));
            }
        });
    });
</script>
@stop