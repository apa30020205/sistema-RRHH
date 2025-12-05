<?php
/**
 * SISTEMA DE ENVÍO DE EMAILS
 * 
 * Maneja el envío de emails para notificaciones de aprobación
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Obtener configuración de email desde la base de datos
 */
function obtenerConfiguracionEmail($conn) {
    $stmt = $conn->prepare("SELECT * FROM configuracion_emails WHERE activo = 1 LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Enviar email usando PHPMailer (requiere composer install)
 * Si no está disponible, usar función mail() nativa
 */
function enviarEmail($destinatario, $asunto, $mensaje_html, $mensaje_texto = '') {
    $conn = conectarDB();
    $config = obtenerConfiguracionEmail($conn);
    cerrarDB($conn);
    
    if (!$config) {
        error_log("Error: No hay configuración de email activa");
        return false;
    }
    
    // Intentar cargar PHPMailer si existe el autoload
    $autoload_path = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($autoload_path) && !class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        require_once $autoload_path;
    }
    
    // Intentar usar PHPMailer si está disponible
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        return enviarEmailPHPMailer($config, $destinatario, $asunto, $mensaje_html, $mensaje_texto);
    } else {
        // Usar función mail() nativa
        return enviarEmailNativo($config, $destinatario, $asunto, $mensaje_html, $mensaje_texto);
    }
}

/**
 * Enviar email usando PHPMailer
 */
function enviarEmailPHPMailer($config, $destinatario, $asunto, $mensaje_html, $mensaje_texto) {
    try {
        // Intentar cargar PHPMailer desde diferentes ubicaciones posibles
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            // Intentar cargar manualmente si está en vendor
            $phpmailer_path = __DIR__ . '/../vendor/autoload.php';
            if (file_exists($phpmailer_path)) {
                require_once $phpmailer_path;
            } else {
                error_log("PHPMailer no encontrado. Instala con: composer require phpmailer/phpmailer");
                return false;
            }
        }
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_usuario'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_seguridad'];
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = 'UTF-8';
        
        // Opciones adicionales para mejor compatibilidad
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        
        // Remitente y destinatario
        $mail->setFrom($config['email_remitente'], $config['nombre_remitente']);
        $mail->addAddress($destinatario);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje_html;
        if (!empty($mensaje_texto)) {
            $mail->AltBody = $mensaje_texto;
        }
        
        $mail->send();
        error_log("Email enviado exitosamente a: " . $destinatario);
        return true;
    } catch (Exception $e) {
        $error_msg = "Error al enviar email: " . $mail->ErrorInfo;
        error_log($error_msg);
        return false;
    }
}

/**
 * Enviar email usando función mail() nativa de PHP
 * NOTA: Esta función puede no funcionar en entornos locales sin configuración SMTP
 */
function enviarEmailNativo($config, $destinatario, $asunto, $mensaje_html, $mensaje_texto) {
    // mail() nativa no soporta SMTP directamente, necesita configuración del servidor
    // Por eso intentamos usar PHPMailer primero
    error_log("ADVERTENCIA: Intentando usar mail() nativa. Esto puede no funcionar en entornos locales.");
    error_log("Se recomienda instalar PHPMailer para mejor compatibilidad.");
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $config['nombre_remitente'] . " <" . $config['email_remitente'] . ">\r\n";
    $headers .= "Reply-To: " . $config['email_remitente'] . "\r\n";
    
    $resultado = @mail($destinatario, $asunto, $mensaje_html, $headers);
    
    if (!$resultado) {
        error_log("Error al enviar email usando mail() nativa. Verifica la configuración SMTP de PHP.");
    }
    
    return $resultado;
}

/**
 * Generar token único para aprobación por email
 */
function generarTokenAprobacion() {
    return bin2hex(random_bytes(32));
}

/**
 * Crear plantilla de email para solicitud pendiente de aprobación
 */
function crearEmailSolicitudPendiente($tipo_formulario, $funcionario_nombre, $formulario_id, $token, $rol_aprobador, $nivel_aprobacion = 1) {
    $base_url = obtenerBaseURL();
    $link_aprobacion = $base_url . "/aprobaciones/revisar.php?token=" . $token;
    
    $tipo_nombre = [
        'permiso' => 'Solicitud de Permiso',
        'vacaciones' => 'Solicitud de Vacaciones',
        'mision_oficial' => 'Misión Oficial',
        'jornada_extraordinaria' => 'Jornada Extraordinaria',
        'tiempo_compensatorio' => 'Tiempo Compensatorio',
        'reincorporacion' => 'Reincorporación'
    ];
    
    $nombre_tipo = $tipo_nombre[$tipo_formulario] ?? 'Solicitud';
    
    // Determinar el cargo según el nivel
    $cargo_aprobador = '';
    switch ($nivel_aprobacion) {
        case 1:
            $cargo_aprobador = 'Jefe inmediato';
            break;
        case 2:
            $cargo_aprobador = 'Revisado por';
            break;
        case 3:
            $cargo_aprobador = 'Jefe Institucional de Recursos Humanos';
            break;
        default:
            $cargo_aprobador = 'Aprobador';
    }
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .button { display: inline-block; padding: 12px 30px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .cargo-info { background: white; padding: 15px; border-left: 4px solid #667eea; margin: 15px 0; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Sistema de Recursos Humanos</h2>
            </div>
            <div class='content'>
                <h3>Nueva Solicitud Pendiente de Aprobación</h3>
                <p>Estimado/a,</p>
                <p>Se ha recibido una <strong>{$nombre_tipo}</strong> del funcionario <strong>{$funcionario_nombre}</strong> que requiere su aprobación.</p>
                
                <div class='cargo-info'>
                    <p style='margin: 0;'><strong>Cargo:</strong> {$cargo_aprobador}</p>
                </div>
                
                <p>Por favor, revise la solicitud y tome una decisión:</p>
                <div style='text-align: center;'>
                    <a href='{$link_aprobacion}' class='button'>Revisar Solicitud</a>
                </div>
                <p>O copie y pegue este enlace en su navegador:</p>
                <p style='word-break: break-all; color: #667eea;'>{$link_aprobacion}</p>
                <p><strong>Nota:</strong> Este enlace expirará en 7 días.</p>
            </div>
            <div class='footer'>
                <p>Este es un email automático del Sistema de Recursos Humanos. Por favor no responda a este mensaje.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return $html;
}

/**
 * Crear plantilla de email para notificación de aprobación/rechazo al funcionario
 */
function crearEmailNotificacionFuncionario($tipo_formulario, $accion, $aprobador_nombre, $observaciones = '', $funcionario_nombre = '') {
    $tipo_nombre = [
        'permiso' => 'Solicitud de Permiso',
        'vacaciones' => 'Solicitud de Vacaciones',
        'mision_oficial' => 'Misión Oficial',
        'jornada_extraordinaria' => 'Jornada Extraordinaria',
        'tiempo_compensatorio' => 'Tiempo Compensatorio',
        'reincorporacion' => 'Reincorporación'
    ];
    
    $nombre_tipo = $tipo_nombre[$tipo_formulario] ?? 'Solicitud';
    $estado = ($accion == 'aprobado') ? 'APROBADA' : 'RECHAZADA';
    $color = ($accion == 'aprobado') ? '#10b981' : '#ef4444';
    $icono = ($accion == 'aprobado') ? '✓' : '✗';
    
    // Saludo personalizado con nombre del funcionario
    $saludo = !empty($funcionario_nombre) ? "Estimado/a <strong>{$funcionario_nombre}</strong>," : "Estimado/a funcionario,";
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: {$color}; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>{$icono} Su Solicitud ha sido {$estado}</h2>
            </div>
            <div class='content'>
                <p>{$saludo}</p>
                <p>Su <strong>{$nombre_tipo}</strong> ha sido <strong>{$estado}</strong> por <strong>{$aprobador_nombre}</strong>.</p>";
    
    if (!empty($observaciones)) {
        $html .= "<p><strong>Observaciones:</strong></p><p style='background: white; padding: 15px; border-left: 4px solid {$color};'>{$observaciones}</p>";
    }
    
    $html .= "
            </div>
            <div class='footer'>
                <p>Este es un email automático del Sistema de Recursos Humanos. Por favor no responda a este mensaje.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return $html;
}

/**
 * Obtener URL base del sistema
 */
function obtenerBaseURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Obtener la ruta del script actual
    $script_path = $_SERVER['SCRIPT_NAME'];
    
    // Buscar la posición de "SISTEMA_RRHH" en la ruta
    $pos = strpos($script_path, 'SISTEMA_RRHH');
    
    if ($pos !== false) {
        // Extraer la ruta hasta SISTEMA_RRHH
        $base_path = substr($script_path, 0, $pos + strlen('SISTEMA_RRHH'));
    } else {
        // Si no encontramos SISTEMA_RRHH, usar dirname dos veces (desde includes/)
        $base_path = dirname(dirname($script_path));
    }
    
    // Normalizar la ruta (eliminar barras dobles)
    $base_path = str_replace('//', '/', $base_path);
    
    // Construir la URL base del sistema
    $base_url = $protocol . '://' . $host . $base_path;
    
    return $base_url;
}


