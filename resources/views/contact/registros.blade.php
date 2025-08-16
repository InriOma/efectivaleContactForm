<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Registros de Contacto</h3>
                        <a href="{{ route('contact.index') }}" class="btn btn-light btn-sm">
                            Nuevo Mensaje
                        </a>
                    </div>
                    <div class="card-body">
                        @if(count($mensajes) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Mensaje</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(array_reverse($mensajes) as $mensaje)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">{{ substr($mensaje['id'], -8) }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ htmlspecialchars($mensaje['nombre']) }}</strong>
                                                </td>
                                                <td>
                                                    <a href="mailto:{{ $mensaje['email'] }}" class="text-decoration-none">
                                                        {{ htmlspecialchars($mensaje['email']) }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="message-preview" style="max-width: 300px;">
                                                        {{ Str::limit(htmlspecialchars($mensaje['mensaje']), 100) }}
                                                        @if(strlen($mensaje['mensaje']) > 100)
                                                            <button class="btn btn-sm btn-link p-0" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#messageModal{{ $loop->index }}">
                                                                Ver completo
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($mensaje['fecha'])->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal para mensaje completo -->
                                            @if(strlen($mensaje['mensaje']) > 100)
                                                <div class="modal fade" id="messageModal{{ $loop->index }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Mensaje de {{ $mensaje['nombre'] }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Email:</strong> {{ $mensaje['email'] }}</p>
                                                                <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($mensaje['fecha'])->format('d/m/Y H:i') }}</p>
                                                                <hr>
                                                                <p>{{ htmlspecialchars($mensaje['mensaje']) }}</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <strong>Total de mensajes:</strong> {{ count($mensajes) }}
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                                </div>
                                <h5 class="text-muted">No hay mensajes registrados</h5>
                                <p class="text-muted">Los mensajes enviados aparecerán aquí</p>
                                <a href="{{ route('contact.index') }}" class="btn btn-primary">
                                    Enviar primer mensaje
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>