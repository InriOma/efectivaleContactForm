<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Formulario de Contacto</h3>
                    </div>
                    <div class="card-body">
                        <!-- Alertas -->
                        <div id="alert-container"></div>
                        
                        <form id="contactForm">
                            @csrf
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="invalid-feedback" id="nombre-error"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="mensaje" class="form-label">Mensaje *</label>
                                <textarea class="form-control" id="mensaje" name="mensaje" rows="5" required></textarea>
                                <div class="invalid-feedback" id="mensaje-error"></div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Enviar Mensaje
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-3 text-center">
                            <a href="{{ route('contact.registros') }}" class="btn btn-outline-secondary">
                                Ver Solo Registros
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lista de mensajes debajo del formulario -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Mensajes Registrados</h3>
                        <div>
                            @if(!empty($mensajes) && count($mensajes) > 0)
                                <button class="btn btn-danger btn-sm" id="clearAllBtn" data-bs-toggle="modal" data-bs-target="#clearAllModal">
                                    <i class="bi bi-trash"></i> Limpiar Todo
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body" id="mensajes-container">
                        @if(!empty($mensajes) && count($mensajes) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="mensajes-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Mensaje</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(array_reverse($mensajes) as $mensaje)
                                            <tr id="mensaje-{{ $mensaje['id'] }}">
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
                                                        {{ Str::limit(htmlspecialchars($mensaje['mensaje']), 80) }}
                                                        @if(strlen($mensaje['mensaje']) > 80)
                                                            <button class="btn btn-sm btn-link p-0 view-message-btn" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#messageModal"
                                                                    data-id="{{ $mensaje['id'] }}"
                                                                    data-nombre="{{ $mensaje['nombre'] }}"
                                                                    data-email="{{ $mensaje['email'] }}"
                                                                    data-mensaje="{{ htmlspecialchars($mensaje['mensaje']) }}"
                                                                    data-fecha="{{ \Carbon\Carbon::parse($mensaje['fecha'])->format('d/m/Y H:i') }}">
                                                                Ver completo
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($mensaje['fecha'])->format('d/m/Y H:i') }}
                                                        @if(isset($mensaje['fecha_modificacion']))
                                                            <br><span class="badge bg-warning text-dark">Editado</span>
                                                        @endif
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-warning btn-sm edit-btn" 
                                                                data-id="{{ $mensaje['id'] }}"
                                                                data-nombre="{{ $mensaje['nombre'] }}"
                                                                data-email="{{ $mensaje['email'] }}"
                                                                data-mensaje="{{ $mensaje['mensaje'] }}"
                                                                title="Editar mensaje">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm delete-btn" 
                                                                data-id="{{ $mensaje['id'] }}"
                                                                data-nombre="{{ $mensaje['nombre'] }}"
                                                                title="Eliminar mensaje">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <strong>Total de mensajes:</strong> <span id="total-mensajes">{{ count($mensajes ?? []) }}</span>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4" id="empty-state">
                                <div class="mb-3">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                                </div>
                                <h5 class="text-muted">No hay mensajes registrados</h5>
                                <p class="text-muted">Los mensajes enviados aparecerán aquí automáticamente</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver mensaje completo -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Mensaje Completo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>De:</strong> <span id="modal-nombre"></span></p>
                    <p><strong>Email:</strong> <span id="modal-email"></span></p>
                    <p><strong>Fecha:</strong> <span id="modal-fecha"></span></p>
                    <hr>
                    <p id="modal-mensaje"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar mensaje -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit-id">
                        
                        <div class="mb-3">
                            <label for="edit-nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="edit-nombre" name="nombre" required>
                            <div class="invalid-feedback" id="edit-nombre-error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                            <div class="invalid-feedback" id="edit-email-error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-mensaje" class="form-label">Mensaje *</label>
                            <textarea class="form-control" id="edit-mensaje" name="mensaje" rows="4" required></textarea>
                            <div class="invalid-feedback" id="edit-mensaje-error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="updateBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación individual -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar el mensaje de <strong id="delete-nombre"></strong>?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar limpiar todo -->
    @if(!empty($mensajes) && count($mensajes) > 0)
        <div class="modal fade" id="clearAllModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Limpiar Todos los Registros</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>¡Atención!</strong> Esta acción eliminará todos los <span id="total-count">{{ count($mensajes ?? []) }}</span> mensajes registrados.
                        </div>
                        <p>¿Estás completamente seguro que deseas eliminar <strong>TODOS</strong> los mensajes?</p>
                        <p class="text-muted">Esta acción es irreversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmClearAllBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Sí, Eliminar Todo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para confirmar eliminación individual -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro que deseas eliminar el mensaje de <strong id="delete-nombre"></strong>?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar limpiar todo -->
    @if(!empty($mensajes) && count($mensajes) > 0)
        <div class="modal fade" id="clearAllModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Limpiar Todos los Registros</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>¡Atención!</strong> Esta acción eliminará todos los {{ count($mensajes ?? []) }} mensajes registrados.
                        </div>
                        <p>¿Estás completamente seguro que deseas eliminar <strong>TODOS</strong> los mensajes?</p>
                        <p class="text-muted">Esta acción es irreversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmClearAllBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Sí, Eliminar Todo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let deleteId = null;

            // Configurar CSRF token para fetch
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Manejar clicks en botones de eliminar
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    deleteId = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    
                    document.getElementById('delete-nombre').textContent = nombre;
                    
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    modal.show();
                });
            });

            // Confirmar eliminación individual
            document.getElementById('confirmDeleteBtn')?.addEventListener('click', function() {
                if (!deleteId) return;

                const btn = this;
                const spinner = btn.querySelector('.spinner-border');
                
                btn.disabled = true;
                spinner.classList.remove('d-none');

                fetch(`/mensaje/${deleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        
                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                        
                        // Recargar página después de un momento
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('Error al eliminar el mensaje', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar el mensaje', 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });

            // Confirmar limpiar todo
            document.getElementById('confirmClearAllBtn')?.addEventListener('click', function() {
                const btn = this;
                const spinner = btn.querySelector('.spinner-border');
                
                btn.disabled = true;
                spinner.classList.remove('d-none');

                fetch('/mensajes/limpiar', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        
                        // Cerrar modal
                        bootstrap.Modal.getInstance(document.getElementById('clearAllModal')).hide();
                        
                        // Recargar página después de un momento
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showAlert('Error al limpiar los mensajes', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al limpiar los mensajes', 'danger');
                })
                .finally(() => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                });
            });

            function showAlert(message, type) {
                // Crear y mostrar alerta temporal
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.body.appendChild(alertDiv);
                
                // Auto-remover después de 3 segundos
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Configurar CSRF token para AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                
                // Limpiar errores previos
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('#alert-container').empty();
                
                // Validación del cliente
                let isValid = true;
                
                const nombre = $('#nombre').val().trim();
                const email = $('#email').val().trim();
                const mensaje = $('#mensaje').val().trim();
                
                if (!nombre) {
                    $('#nombre').addClass('is-invalid');
                    $('#nombre-error').text('El nombre es requerido');
                    isValid = false;
                }
                
                if (!email) {
                    $('#email').addClass('is-invalid');
                    $('#email-error').text('El email es requerido');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    $('#email').addClass('is-invalid');
                    $('#email-error').text('Ingrese un email válido');
                    isValid = false;
                }
                
                if (!mensaje) {
                    $('#mensaje').addClass('is-invalid');
                    $('#mensaje-error').text('El mensaje es requerido');
                    isValid = false;
                }
                
                if (!isValid) {
                    return;
                }
                
                // Mostrar loading
                const submitBtn = $('#submitBtn');
                const spinner = submitBtn.find('.spinner-border');
                submitBtn.prop('disabled', true);
                spinner.removeClass('d-none');
                
                // Envío AJAX
                $.ajax({
                    url: '{{ route("contact.store") }}',
                    method: 'POST',
                    data: {
                        nombre: nombre,
                        email: email,
                        mensaje: mensaje
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Mensaje enviado exitosamente', 'success');
                            $('#contactForm')[0].reset();
                            
                            // Recargar la tabla de mensajes
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(errors[field][0]);
                            }
                        } else {
                            showAlert('Error al enviar el mensaje. Intente nuevamente.', 'danger');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });
            
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            function showAlert(message, type) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#alert-container').html(alertHtml);
            }
        });
    </script>
</body>
</html>