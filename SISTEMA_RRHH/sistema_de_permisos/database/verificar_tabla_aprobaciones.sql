-- =====================================================
-- VERIFICAR ESTRUCTURA DE TABLA aprobaciones
-- =====================================================

USE recursos_humanos;

-- Ver estructura de la tabla
DESCRIBE aprobaciones;

-- Ver tokens actuales (problema)
SELECT id, tipo_formulario, formulario_id, token_aprobacion, LENGTH(token_aprobacion) as longitud_token, fecha_expiracion_token 
FROM aprobaciones 
ORDER BY id DESC 
LIMIT 5;


