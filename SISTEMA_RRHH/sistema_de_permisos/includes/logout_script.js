/**
 * Script para manejar el cierre de sesión cuando se cierra el navegador
 * Este archivo se incluye en las páginas que requieren sesión
 */

// Detectar cuando el usuario cierra la pestaña o el navegador
window.addEventListener('beforeunload', function(e) {
    // Intentar cerrar la sesión (pero esto no siempre funciona)
    // La mejor solución es usar expiración de sesión en el servidor
    // que ya está implementada en session.php
});

// Alternativa: Usar el evento 'visibilitychange' para detectar cuando el usuario cambia de pestaña
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        // El usuario cambió de pestaña o minimizó la ventana
        // No hacemos nada, la sesión expirará automáticamente después de 2 horas
    }
});


