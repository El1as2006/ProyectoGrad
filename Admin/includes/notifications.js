// Notificaciones en tiempo real (AJAX)
document.addEventListener('DOMContentLoaded', function() {
      // Determinar la ruta correcta para ajax_notifications.php
    function getAjaxNotificationsPath() {
        const currentPath = window.location.pathname;
        const currentLocation = window.location.href;
        
        // Detectar si estamos en Admin directamente
        if (currentPath.includes('/Admin/') && currentPath.match(/\/Admin\/[^\/]+\.php$/)) {
            return 'includes/ajax_notifications.php';
        }
        // Detectar si estamos en un subdirectorio de Admin
        else if (currentPath.includes('/Admin/') && currentPath.match(/\/Admin\/.*\/[^\/]+\.php$/)) {
            return '../includes/ajax_notifications.php';
        }
        // Si estamos fuera de Admin, usar ruta absoluta
        else {
            // Construir ruta absoluta basada en el origen
            const origin = window.location.origin;
            const pathParts = currentPath.split('/');
            let adminIndex = pathParts.indexOf('ProyectoGrad-Logica-ProyectoGrad');
            if (adminIndex !== -1) {
                const basePath = pathParts.slice(0, adminIndex + 1).join('/');
                return `${origin}${basePath}/Admin/includes/ajax_notifications.php`;
            }
        }
        // Ruta por defecto
        return 'includes/ajax_notifications.php';
    }
    
    // Función para actualizar notificaciones
    function updateNotifications() {
        const ajaxPath = getAjaxNotificationsPath();
        
        fetch(ajaxPath + '?ajax_notificaciones=1')
            .then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            })
            .then(data => {
                // Actualizar el contenido de notificaciones
                const notiContainer = document.querySelector('.dropdown-menu .px-2[data-simplebar]');
                if (notiContainer) {
                    let notiList = '';
                    if (data.length === 0) {
                        notiList = '<div class="text-center text-muted py-3">No tienes notificaciones recientes.</div>';
                    } else {
                        data.forEach(function(noti) {
                            const fecha = new Date(noti.fecha).toLocaleDateString('es-ES', {
                                day: '2-digit',
                                month: '2-digit', 
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                            
                            notiList += `<a href="notificaciones.php" class="dropdown-item p-0 notify-item card ${noti.leido ? 'read-noti' : 'unread-noti'} shadow-none mb-2">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 text-truncate ms-2">
                                            <h5 class="noti-item-title fw-semibold font-14 mb-1">
                                                ${noti.tipo.charAt(0).toUpperCase() + noti.tipo.slice(1)}
                                                <small class="fw-normal text-muted ms-1">${fecha}</small>
                                            </h5>
                                            <small class="noti-item-subtitle text-muted">${noti.mensaje}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>`;
                        });
                    }
                    notiContainer.innerHTML = notiList;
                }

                // Actualizar el badge de notificaciones
                const notiBadge = document.querySelector('.noti-icon-badge');
                const notiButton = document.querySelector('.notification-list .nav-link');
                
                if (data.length > 0) {
                    // Hay notificaciones - mostrar badge si no existe
                    if (!notiBadge && notiButton) {
                        const badge = document.createElement('span');
                        badge.className = 'noti-icon-badge';
                        notiButton.appendChild(badge);
                    }
                } else {
                    // No hay notificaciones - remover badge si existe
                    if (notiBadge) {
                        notiBadge.remove();
                    }
                }
            })
            .catch(error => {
                console.error('Error al cargar notificaciones:', error);
                // Intentar con rutas alternativas si falla
                const alternativePaths = [
                    'includes/ajax_notifications.php',
                    '../includes/ajax_notifications.php',
                    'Admin/includes/ajax_notifications.php',
                    '../Admin/includes/ajax_notifications.php'
                ];
                
                tryAlternativePaths(alternativePaths, 0);
            });
    }
    
    // Función para probar rutas alternativas
    function tryAlternativePaths(paths, index) {
        if (index >= paths.length) {
            console.error('No se pudo cargar las notificaciones desde ninguna ruta');
            return;
        }
        
        const path = paths[index];
        fetch(path + '?ajax_notificaciones=1')
            .then(res => {
                if (!res.ok) {
                    throw new Error('Path not found: ' + path);
                }
                return res.json();
            })
            .then(data => {
                updateNotificationDisplay(data);
            })
            .catch(error => {
                console.log('Intentando ruta alternativa:', paths[index + 1]);
                tryAlternativePaths(paths, index + 1);
            });
    }
    
    // Función separada para actualizar la visualización de notificaciones
    function updateNotificationDisplay(data) {
        // Actualizar el contenido de notificaciones
        const notiContainer = document.querySelector('.dropdown-menu .px-2[data-simplebar]');
        if (notiContainer) {
            let notiList = '';
            if (data.length === 0) {
                notiList = '<div class="text-center text-muted py-3">No tienes notificaciones recientes.</div>';
            } else {
                data.forEach(function(noti) {
                    const fecha = new Date(noti.fecha).toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit', 
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    notiList += `<a href="notificaciones.php" class="dropdown-item p-0 notify-item card ${noti.leido ? 'read-noti' : 'unread-noti'} shadow-none mb-2">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 text-truncate ms-2">
                                    <h5 class="noti-item-title fw-semibold font-14 mb-1">
                                        ${noti.tipo.charAt(0).toUpperCase() + noti.tipo.slice(1)}
                                        <small class="fw-normal text-muted ms-1">${fecha}</small>
                                    </h5>
                                    <small class="noti-item-subtitle text-muted">${noti.mensaje}</small>
                                </div>
                            </div>
                        </div>
                    </a>`;
                });
            }
            notiContainer.innerHTML = notiList;
        }

        // Actualizar el badge de notificaciones
        const notiBadge = document.querySelector('.noti-icon-badge');
        const notiButton = document.querySelector('.notification-list .nav-link');
        
        if (data.length > 0) {
            // Hay notificaciones - mostrar badge si no existe
            if (!notiBadge && notiButton) {
                const badge = document.createElement('span');
                badge.className = 'noti-icon-badge';
                notiButton.appendChild(badge);
            }
        } else {
            // No hay notificaciones - remover badge si existe
            if (notiBadge) {
                notiBadge.remove();
            }
        }
    }

    // Actualizar notificaciones al cargar la página
    updateNotifications();
    
    // Refrescar notificaciones cada 30 segundos
    setInterval(updateNotifications, 30000);
    
    // También actualizar cuando se hace clic en la campanita
    const notiButton = document.querySelector('.notification-list .nav-link');
    if (notiButton) {
        notiButton.addEventListener('click', function() {
            updateNotifications();
        });
    }
});