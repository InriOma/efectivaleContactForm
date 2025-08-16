<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                                Ver Registros
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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