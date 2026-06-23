<?php
/**
 * Validador de CUITs — Ingenio La Corona
 *
 * Valida CUITs argentinos usando el algoritmo oficial de AFIP.
 * Formato: XX-XXXXXXXX-X (11 dígitos)
 *
 * Compatible con:
 *   - PHP 7.1+ (XAMPP del ingenio)
 *   - SQL Server 2008 R2 (Calipso ERP, función cruzarConCalipso)
 *
 * @package     ValidadorCUIT
 * @author      Ingenio La Corona - Sistemas
 * @version     1.0.0
 * @link        https://www.afip.gob.ar/generico/validarCuit.asp
 */

// ---------------------------------------------------------------------------
//  TIPOS DE CUIT VÁLIDOS SEGÚN AFIP
// ---------------------------------------------------------------------------
define('CUIT_TIPOS_VALIDOS', ['20', '23', '24', '27', '30', '33', '34']);
define('CUIT_PESOS_AFIP',    [5, 4, 3, 2, 7, 6, 5, 4, 3, 2]);
define('CUIT_LARGO',         11);

// ---------------------------------------------------------------------------
//  FUNCIONES PÚBLICAS
// ---------------------------------------------------------------------------

/**
 * Valida un CUIT argentino según el algoritmo oficial de AFIP.
 *
 * PASOS:
 *   1. Normaliza el CUIT (elimina guiones, puntos, espacios).
 *   2. Verifica que tenga exactamente 11 dígitos numéricos.
 *   3. Verifica que el prefijo (tipo) sea uno de los códigos válidos.
 *   4. Aplica el cálculo de dígito verificador con pesos fijos.
 *   5. Compara el dígito calculado contra el último dígito del CUIT.
 *
 * @param  string|null $cuit CUIT a validar (con o sin separadores).
 * @return bool True si el CUIT es aritméticamente válido.
 */
function validarCuit($cuit)
{
    // Rechazar null, vacío, o no-string
    if ($cuit === null || $cuit === '' || !is_string($cuit)) {
        return false;
    }

    // Normalizar: quitar separadores comunes
    $cuit = _normalizarCuit($cuit);

    // Debe ser exactamente 11 dígitos numéricos
    if (strlen($cuit) !== CUIT_LARGO || !ctype_digit($cuit)) {
        return false;
    }

    // El prefijo (2 dígitos) debe ser un tipo de CUIT válido
    $tipo = substr($cuit, 0, 2);
    if (!in_array($tipo, CUIT_TIPOS_VALIDOS, true)) {
        return false;
    }

    // Calcular dígito verificador
    $digitoEsperado = _calcularDigitoVerificador($cuit);

    // Si el cálculo devuelve 10, el CUIT es inválido por definición de AFIP
    if ($digitoEsperado === 10) {
        return false;
    }

    // Comparar contra el último dígito real
    $ultimoDigito = (int) substr($cuit, 10, 1);
    return $digitoEsperado === $ultimoDigito;
}

/**
 * Formatea un CUIT al formato estándar XX-XXXXXXXX-X.
 *
 * @param  string $cuit CUIT sin formatear.
 * @return string CUIT formateado, o cadena vacía si no se puede formatear.
 */
function formatearCuit($cuit)
{
    $cuit = _normalizarCuit($cuit);

    if (strlen($cuit) !== CUIT_LARGO || !ctype_digit($cuit)) {
        return '';
    }

    return substr($cuit, 0, 2)
         . '-' . substr($cuit, 2, 8)
         . '-' . substr($cuit, 10, 1);
}

/**
 * Extrae todos los CUITs válidos encontrados en un texto usando expresiones
 * regulares. Cada CUIT encontrado se valida aritméticamente antes de incluirlo
 * en el resultado (evita falsos positivos de números con formato similar).
 *
 * Formatos reconocidos:
 *   - XX-XXXXXXXX-X (con guiones)
 *   - XX.XXXXXXXX.X (con puntos)
 *   - XXXXXXXXXXX   (11 dígitos sin separadores)
 *   - XX XXXXXXXXX X (con espacios)
 *
 * @param  string $texto Texto en el cual buscar CUITs.
 * @return array  Arreglo de CUITs válidos encontrados (solo dígitos, 11 caracteres).
 */
function extraerCuits($texto)
{
    if ($texto === null || $texto === '' || !is_string($texto)) {
        return [];
    }

    $cuits = [];
    $encontrados = [];

    // Patrón: 2 dígitos + separador opcional + 8 dígitos + separador opcional + 1 dígito
    // Usa lookbehind/lookahead negativos para no capturar dentro de números más largos.
    $patron = '/(?<!\d)(\d{2})[\.\-\_]?(\d{8})[\.\-\_]?(\d)(?!\d)/';

    if (preg_match_all($patron, $texto, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $cuit = $match[1] . $match[2] . $match[3];

            // Validación aritmética para filtrar falsos positivos
            if (validarCuit($cuit)) {
                $encontrados[] = $cuit;
            }
        }
    }

    // Eliminar duplicados preservando orden de aparición
    foreach ($encontrados as $cuit) {
        if (!in_array($cuit, $cuits, true)) {
            $cuits[] = $cuit;
        }
    }

    return $cuits;
}

/**
 * Valida un lote de CUITs y devuelve un reporte estructurado con resultados
 * individuales y un resumen estadístico.
 *
 * @param  array $cuitsArray Arreglo de strings con CUITs a validar.
 * @return array Con los keys: 'success', 'error' (opcional), 'results', 'resumen'.
 */
function validarLote($cuitsArray)
{
    if (!is_array($cuitsArray) || empty($cuitsArray)) {
        return [
            'success' => false,
            'error'   => 'El arreglo de CUITs está vacío o no es válido.',
            'results' => [],
            'resumen' => [
                'total'        => 0,
                'validos'      => 0,
                'invalidos'    => 0,
                'tasa_validez' => 0,
            ],
        ];
    }

    $results   = [];
    $validos   = 0;
    $invalidos = 0;
    $total     = count($cuitsArray);

    foreach ($cuitsArray as $index => $cuit) {
        $esValido   = validarCuit($cuit);
        $formateado = formatearCuit($cuit);

        $item = [
            'index'      => $index,
            'original'   => (string) $cuit,
            'formateado' => $formateado,
            'valido'     => $esValido,
        ];

        if (!$esValido) {
            $item['diagnostico'] = _diagnosticarCuit($cuit);
            $invalidos++;
        } else {
            $validos++;
        }

        $results[] = $item;
    }

    return [
        'success' => true,
        'results' => $results,
        'resumen' => [
            'total'        => $total,
            'validos'      => $validos,
            'invalidos'    => $invalidos,
            'tasa_validez' => $total > 0
                ? round(($validos / $total) * 100, 2)
                : 0,
        ],
    ];
}

/**
 * Busca un CUIT en las tablas de proveedores de Calipso (SQL Server 2008 R2).
 *
 * NOTA DE SEGURIDAD:
 *   Esta función NO debe ejecutarse directamente contra la base de producción
 *   sin pasar por el proceso de revisión del agente sql-server-calipso-reviewer.
 *   Las tablas y columnas usadas son representativas; verificar con:
 *     SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
 *     WHERE TABLE_NAME LIKE '%PROV%' OR TABLE_NAME LIKE '%PROVEEDOR%';
 *
 * @param  string   $cuit CUIT a buscar (con o sin formato).
 * @param  PDO|null $pdo  Conexión PDO a SQL Server. Si es null, devuelve SQL de ejemplo.
 * @return array    Resultado de la búsqueda.
 */
function cruzarConCalipso($cuit, $pdo = null)
{
    // Validar el CUIT primero
    if (!validarCuit($cuit)) {
        $formateado = formatearCuit($cuit);
        return [
            'success'    => false,
            'error'      => 'El CUIT no es aritméticamente válido.',
            'cuit'       => $formateado ?: $cuit,
            'encontrado' => null,
        ];
    }

    $cuitFormateado = formatearCuit($cuit);
    $cuitLimpio     = _normalizarCuit($cuit);

    // Si no hay conexión, devuelve SQL de ejemplo para depuración
    if ($pdo === null) {
        return [
            'success'    => false,
            'error'      => 'No se proporcionó conexión PDO a SQL Server.',
            'cuit'       => $cuitFormateado,
            'encontrado' => null,
            'sql_ejemplo' => _generarSqlBusqueda($cuitLimpio, $cuitFormateado),
        ];
    }

    try {
        /*
         * SQL compatible con SQL Server 2008 R2.
         * Busca el CUIT normalizado en las tablas de proveedores de Calipso.
         *
         * NOTA: Se usa REPLACE anidado para normalizar el CUIT almacenado
         * (eliminar guiones, puntos, espacios) antes de comparar.
         *
         * Se consultan dos fuentes (tabla directa + vista EZI) por UNION
         * para cubrir distintas instalaciones de Calipso.
         */
        $sql = "
            SELECT
                p.COD_PROV,
                p.RAZON_SOCIAL,
                p.CUIT,
                p.ESTADO
            FROM dbo.PROVEEDORES p
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(p.CUIT, '-', ''), '.', ''), ' ', ''), '_', '') = ?

            UNION

            SELECT
                v.COD_PROV,
                v.RAZON_SOCIAL,
                v.CUIT,
                v.ESTADO
            FROM dbo.V_EZI_PROVEEDORES v
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(v.CUIT, '-', ''), '.', ''), ' ', ''), '_', '') = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$cuitLimpio, $cuitLimpio]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return [
                'success'    => true,
                'cuit'       => $cuitFormateado,
                'encontrado' => true,
                'proveedor'  => [
                    'codigo'       => $row['COD_PROV'],
                    'razon_social' => $row['RAZON_SOCIAL'],
                    'cuit'         => $row['CUIT'],
                    'estado'       => $row['ESTADO'] ?? null,
                ],
            ];
        }

        return [
            'success'    => true,
            'cuit'       => $cuitFormateado,
            'encontrado' => false,
            'proveedor'  => null,
        ];

    } catch (PDOException $e) {
        error_log('[ValidadorCUIT] Error al consultar Calipso: ' . $e->getMessage());
        return [
            'success'    => false,
            'error'      => 'Error al consultar la base de datos.',
            'cuit'       => $cuitFormateado,
            'encontrado' => null,
        ];
    }
}

// ---------------------------------------------------------------------------
//  FUNCIONES INTERNAS (helpers)
// ---------------------------------------------------------------------------

/**
 * Normaliza un CUIT eliminando separadores comunes.
 *
 * @param  string $cuit CUIT con posible formato.
 * @return string CUIT con solo dígitos, o cadena vacía si la entrada no es string.
 */
function _normalizarCuit($cuit)
{
    if (!is_string($cuit)) {
        return '';
    }

    return str_replace(['-', '.', ' ', '_', "\t", "\n", "\r"], '', trim($cuit));
}

/**
 * Calcula el dígito verificador de un CUIT usando el algoritmo de AFIP.
 *
 * Pesos: [5, 4, 3, 2, 7, 6, 5, 4, 3, 2]
 *
 * @param  string $cuit CUIT normalizado de 11 dígitos.
 * @return int    Dígito verificador esperado (0-11). 10 indica CUIT inválido.
 */
function _calcularDigitoVerificador($cuit)
{
    $digitos = str_split(substr($cuit, 0, 10));
    $pesos   = CUIT_PESOS_AFIP;
    $suma    = 0;

    foreach ($digitos as $i => $digito) {
        $suma += (int) $digito * $pesos[$i];
    }

    $resto        = $suma % 11;
    $digitoCalculado = 11 - $resto;

    // Caso especial: 11 → 0
    if ($digitoCalculado === 11) {
        return 0;
    }

    // Caso especial: 10 → CUIT inválido (no aplica para ningún tipo válido)
    return $digitoCalculado;
}

/**
 * Diagnostica por qué un CUIT es inválido (helper para validarLote).
 *
 * @param  string $cuit CUIT a diagnosticar.
 * @return string Mensaje descriptivo del error.
 */
function _diagnosticarCuit($cuit)
{
    if ($cuit === null || $cuit === '' || !is_string($cuit)) {
        return 'El CUIT está vacío o no es una cadena de texto.';
    }

    $original = $cuit;
    $cuit     = _normalizarCuit($cuit);

    if ($cuit === '') {
        return "El CUIT '$original' solo contiene separadores o caracteres no válidos.";
    }

    if (!ctype_digit($cuit)) {
        return "El CUIT '$original' contiene caracteres no numéricos (letras, símbolos).";
    }

    if (strlen($cuit) !== CUIT_LARGO) {
        return "El CUIT '$original' debe tener exactamente 11 dígitos (se encontraron " . strlen($cuit) . ").";
    }

    $tipo = substr($cuit, 0, 2);
    if (!in_array($tipo, CUIT_TIPOS_VALIDOS, true)) {
        return "El prefijo '$tipo' no es un tipo de CUIT válido. "
             . 'Tipos válidos: ' . implode(', ', CUIT_TIPOS_VALIDOS) . '.';
    }

    // Error en el dígito verificador
    $digitoEsperado = _calcularDigitoVerificador($cuit);

    if ($digitoEsperado === 10) {
        return 'El dígito verificador calculado es 10 (CUIT inválido según AFIP).';
    }

    $ultimoDigito = (int) substr($cuit, 10, 1);
    return "El dígito verificador esperado es $digitoEsperado, pero se encontró $ultimoDigito.";
}

/**
 * Genera un string con el SQL de búsqueda para depuración.
 *
 * @param  string $cuitLimpio     CUIT normalizado (solo dígitos).
 * @param  string $cuitFormateado CUIT formateado.
 * @return array  SQL generado + metadatos.
 */
function _generarSqlBusqueda($cuitLimpio, $cuitFormateado)
{
    return [
        'cuit_original'   => $cuitFormateado,
        'cuit_para_buscar' => $cuitLimpio,
        'sql_generado'    => "-- Buscar CUIT $cuitFormateado en Calipso\n"
            . "SELECT p.COD_PROV, p.RAZON_SOCIAL, p.CUIT, p.ESTADO\n"
            . "FROM dbo.PROVEEDORES p\n"
            . "WHERE REPLACE(REPLACE(REPLACE(REPLACE(p.CUIT, '-', ''), '.', ''), ' ', ''), '_', '')"
            . " = '$cuitLimpio'\n"
            . "\nUNION\n\n"
            . "SELECT v.COD_PROV, v.RAZON_SOCIAL, v.CUIT, v.ESTADO\n"
            . "FROM dbo.V_EZI_PROVEEDORES v\n"
            . "WHERE REPLACE(REPLACE(REPLACE(REPLACE(v.CUIT, '-', ''), '.', ''), ' ', ''), '_', '')"
            . " = '$cuitLimpio';",
    ];
}
