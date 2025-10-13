<?php

return [    // Configuración SMTP
    'smtp' => [
        'host' => 'smtp.gmail.com',          // Servidor SMTP
        'port' => 587,                       // Puerto SMTP
        'encryption' => 'tls',               // Tipo de encriptación (tls/ssl)
        'auth' => true,                      // Usar autenticación
        'username' => 'crazycoasterptc25@gmail.com',  // Email configurado
        'password' => 'D0nBosC0d4l3ch3$',     // Contraseña configurada
    ],
    
    // Configuración del remitente
    'from' => [
        'email' => 'crazycoasterptc25@gmail.com',     // Email del remitente
        'name' => 'Sistema Biblioteca',      // Nombre del remitente
    ],
      // Configuración de la aplicación
    'app' => [
        'name' => 'Sistema Biblioteca',
        'url' => 'http://localhost/ProyectoGrad-Logica-ProyectoGrad', // URL de tu aplicación
        'support_email' => 'crazycoasterptc25@gmail.com', // Email de soporte
    ],
    
    // Configuración de tokens
    'password_reset' => [
        'token_length' => 64,                // Longitud del token
        'expiry_hours' => 1,                 // Horas antes de que expire el token
    ],
];

/*
INSTRUCCIONES DE CONFIGURACIÓN:

1. Para Gmail:
   - Habilita la verificación en 2 pasos en tu cuenta de Google
   - Genera una "Contraseña de aplicación" específica para este sistema
   - Usa esa contraseña en lugar de tu contraseña normal

2. Para otros proveedores:
   - Consulta la documentación de tu proveedor de email
   - Ajusta host, puerto y tipo de encriptación según corresponda

3. Configuración de URLs:
   - Cambia la URL base según tu entorno (desarrollo/producción)
   - Asegúrate de que las rutas sean accesibles desde el navegador

4. Seguridad:
   - Nunca subas este archivo a repositorios públicos
   - Considera usar variables de entorno en producción
   - Mantén las credenciales seguras
*/
?>
