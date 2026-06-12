USE CORONA
GO

-- Ejemplo de registro de factura aprobada en staging
-- Reemplazar valores de ejemplo por los datos reales de la factura validada.

DECLARE @res INT,
        @msg VARCHAR(500);

EXEC UD_EZI_SP_STAGING_INSERTAR
    @UUID_OPERACION      = NEWID(),
    @TIPO_OPERACION      = 'FACTURA_COMPRA',
    @ORIGEN              = 'PDF_EMAIL',
    @USUARIO_CARGA       = 'middleware',
    @TIPOTRANSACCION_ID  = '50829758-5905-11D5-86C4-0080AD403F5F',
    @NUMERODOCUMENTO     = '0001-00001234',
    @LETRA               = 'A',
    @FECHA_EMISION       = '20260501',
    @FECHA_VENCIMIENTO   = NULL,
    @REFERENCIA          = 'OC1234',
    @PROVEEDOR_CUIT      = '30712345678',
    @NETO                = 10000,
    @IVA_21              = 2100,
    @IVA_105             = 0,
    @PERCEPCIONES        = 0,
    @OTROS_IMPUESTOS     = 0,
    @TOTAL               = 12100,
    @COTIZACION          = 1,
    @CAE                 = '12345678901234',
    @FECHA_VTO_CAE       = '20260515',
    @CENTROCOSTOS_CODIGO = NULL,
    @PDF_FILENAME        = 'factura.pdf',
    @PDF_HASH            = 'ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890',
    @EMAIL_ORIGEN        = 'origen@example.com',
    @EMAIL_ASUNTO        = 'Factura aprobada',
    @RESULTADO           = @res OUTPUT,
    @MENSAJE             = @msg OUTPUT;

SELECT @res AS resultado,
       @msg AS mensaje;
GO

-- Ver facturas aprobadas listas para cargar en Calipso
EXEC UD_EZI_SP_STAGING_PENDIENTES @ESTADO = 'APROBADO';
GO

-- Nota: el siguiente bloque es un ejemplo de llamada manual.
-- Mantener comentado en este archivo de ejemplo y ejecutar solo cuando el TR
-- ya haya sido generado en Calipso y se conozcan los UUID reales.
-- EXEC UD_EZI_SP_STAGING_MARCAR_PROCESADO
--     @ID = 'UUID-STAGING',
--     @TR_GENERADO_ID = 'UUID-TR-CALIPSO',
--     @OPERADOR = 'pdietrich';
GO
