<?php
/**
 * Tests unitarios — Validador de CUITs Ingenio La Corona
 *
 * Ejecutar desde línea de comandos:
 *   php proyectos-ingenio/validador-cuit/backend/test_validador.php
 *
 * Compatible con PHP 7.1+.
 * No requiere PHPUnit. Implementa aserciones inline con reporte en consola.
 *
 * @package     ValidadorCUIT
 * @author      Ingenio La Corona - Sistemas
 * @version     1.0.0
 */

// ---------------------------------------------------------------------------
//  Cargar módulos
// ---------------------------------------------------------------------------
$baseDir = __DIR__;
require_once $baseDir . '/validador_cuit.php';

// Opcional: cargar ai_extractor si existe (tests básicos de IA no necesitan API)
$aiExtractorPath = $baseDir . '/ai_extractor.php';
$aiDisponible    = file_exists($aiExtractorPath);

// ---------------------------------------------------------------------------
//  Estadísticas de la corrida
// ---------------------------------------------------------------------------
$totalPruebas  = 0;
$pasaron       = 0;
$fallaron      = 0;
$errores       = [];

// ---------------------------------------------------------------------------
//  Helpers de test
// ---------------------------------------------------------------------------

/**
 * Ejecuta una aserción y registra el resultado.
 */
function assertTrue($condicion, $nombre, $detalle = '')
{
    global $totalPruebas, $pasaron, $fallaron, $errores;
    $totalPruebas++;
    if ($condicion) {
        $pasaron++;
        echo "  [OK] $nombre\n";
    } else {
        $fallaron++;
        $msg = "  [FAIL] $nombre" . ($detalle ? " -- $detalle" : '');
        $errores[] = $msg;
        echo "$msg\n";
    }
}

/**
 * Ejecuta una aserción de igualdad.
 */
function assertEqual($esperado, $real, $nombre, $detalle = '')
{
    $condicion = ($esperado === $real);
    $detExtra  = $detalle ?: "Esperado: " . var_export($esperado, true)
                         . ", Real: " . var_export($real, true);
    assertTrue($condicion, $nombre, $detExtra);
}

/**
 * Ejecuta una aserción negativa.
 */
function assertFalse($condicion, $nombre, $detalle = '')
{
    assertTrue(!$condicion, $nombre, $detalle ?: 'Se esperaba false, se obtuvo true');
}

// ===========================================================================
//  GENERACIÓN DE CUITS MATEMÁTICAMENTE CORRECTOS
// ===========================================================================
//
// Verificación manual del algoritmo AFIP:
//
//   20-12345678-6:
//     2*5 + 0*4 + 1*3 + 2*2 + 3*7 + 4*6 + 5*5 + 6*4 + 7*3 + 8*2 = 148
//     148 % 11 = 5 → 11-5 = 6 ✓
//
//   23-00000000-0:
//     2*5 + 3*4 + 0*3 + 0*2 + 0*7 + 0*6 + 0*5 + 0*4 + 0*3 + 0*2 = 22
//     22 % 11 = 0 → 11-0 = 11 → 0 ✓ (check digit = 0)
//
//   27-12345678-0:
//     2*5 + 7*4 + 1*3 + 2*2 + 3*7 + 4*6 + 5*5 + 6*4 + 7*3 + 8*2 = 176
//     176 % 11 = 0 → 11-0 = 11 → 0 ✓ (check digit = 0)
//
//   30-12345678-1:
//     3*5 + 0*4 + 1*3 + 2*2 + 3*7 + 4*6 + 5*5 + 6*4 + 7*3 + 8*2 = 153
//     153 % 11 = 10 → 11-10 = 1 ✓
//
//   33-00000000-6:
//     3*5 + 3*4 + 0*3 + 0*2 + 0*7 + 0*6 + 0*5 + 0*4 + 0*3 + 0*2 = 27
//     27 % 11 = 5 → 11-5 = 6 ✓
//
//   34-00000000-2:
//     3*5 + 4*4 + 0*3 + 0*2 + 0*7 + 0*6 + 0*5 + 0*4 + 0*3 + 0*2 = 31
//     31 % 11 = 9 → 11-9 = 2 ✓

$CUIT_VALIDO_1       = '20-12345678-6';
$CUIT_VALIDO_2       = '20123456786';       // sin formato
$CUIT_VALIDO_3       = '20.12345678.6';     // formato con puntos
$CUIT_VALIDO_4       = '30-12345678-1';     // empresa
$CUIT_VALIDO_5       = '27-12345678-0';     // autónomo, dígito 0
$CUIT_VALIDO_6       = '23-00000000-0';     // dígito verificador = 0
$CUIT_VALIDO_7       = '33-00000000-6';     // entidad
$CUIT_VALIDO_8       = '34-00000000-2';     // entidad extranjera

// ===========================================================================
//  TESTS: validarCuit()
// ===========================================================================

echo "\n=== TEST: validarCuit() — Válidos ===\n";

assertTrue(validarCuit($CUIT_VALIDO_1), "CUIT válido con guiones: $CUIT_VALIDO_1");
assertTrue(validarCuit($CUIT_VALIDO_2), "CUIT válido sin formato: $CUIT_VALIDO_2");
assertTrue(validarCuit($CUIT_VALIDO_3), "CUIT válido con puntos: $CUIT_VALIDO_3");
assertTrue(validarCuit($CUIT_VALIDO_4), "CUIT empresa: $CUIT_VALIDO_4");
assertTrue(validarCuit($CUIT_VALIDO_5), "CUIT autónomo (check digit 0): $CUIT_VALIDO_5");
assertTrue(validarCuit($CUIT_VALIDO_6), "CUIT tipo 23 (check digit 0): $CUIT_VALIDO_6");
assertTrue(validarCuit($CUIT_VALIDO_7), "CUIT tipo 33: $CUIT_VALIDO_7");
assertTrue(validarCuit($CUIT_VALIDO_8), "CUIT tipo 34: $CUIT_VALIDO_8");

echo "\n=== TEST: validarCuit() — Inválidos ===\n";

// Dígito verificador incorrecto (mutar el último dígito de un CUIT válido)
assertFalse(validarCuit('20-12345678-7'), 'Dígito verificador incorrecto');
assertFalse(validarCuit('20-12345678-5'), 'Dígito verificador incorrecto (otro)');
assertFalse(validarCuit('30-12345678-9'), 'Empresa con dígito incorrecto');

// Tipo inválido
assertFalse(validarCuit('00-12345678-9'), 'Tipo 00 inválido');
assertFalse(validarCuit('11-12345678-3'), 'Tipo 11 inválido');
assertFalse(validarCuit('99-12345678-9'), 'Tipo 99 inválido');

// Formato inválido
assertFalse(validarCuit('not-a-cuit'), 'Texto sin dígitos');
assertFalse(validarCuit('20/12345678/6'), 'Separador / no soportado');

echo "\n=== TEST: validarCuit() — Casos borde ===\n";

assertFalse(validarCuit(null), 'Null');
assertFalse(validarCuit(''), 'Cadena vacía');
assertFalse(validarCuit('   '), 'Solo espacios');
assertFalse(validarCuit('201234567'), 'Muy corto (9 dígitos)');
assertFalse(validarCuit('201234567890'), 'Muy largo (12 dígitos)');
assertFalse(validarCuit('20-123456-78-6'), 'Formato con separadores extra');

// Verificar que un CUIT de 11 dígitos con letras no pasa
assertFalse(validarCuit('20-ABCDEFGH-6'), 'Contiene letras');

// Array en lugar de string
assertFalse(validarCuit(['20-12345678-6']), 'Array en lugar de string');
assertFalse(validarCuit(20123456786), 'Entero en lugar de string');

// ===========================================================================
//  TESTS: formatearCuit()
// ===========================================================================

echo "\n=== TEST: formatearCuit() ===\n";

assertEqual('20-12345678-6', formatearCuit('20123456786'), 'Formato desde dígitos limpios');
assertEqual('20-12345678-6', formatearCuit('20-12345678-6'), 'Formato ya formateado');
assertEqual('20-12345678-6', formatearCuit('20.12345678.6'), 'Formato desde puntos');
assertEqual('27-12345678-0', formatearCuit('27123456780'), 'Formato CUIT con check digit 0');
assertEqual('', formatearCuit(''), 'Cadena vacía devuelve vacío');
assertEqual('', formatearCuit('abc'), 'Texto inválido devuelve vacío');
assertEqual('', formatearCuit('201234567'), 'CUIT corto devuelve vacío');

// ===========================================================================
//  TESTS: extraerCuits()
// ===========================================================================

echo "\n=== TEST: extraerCuits() ===\n";

// Texto con CUITs válidos
$texto1 = 'El proveedor 20-12345678-6 emitió una factura.';
$result1 = extraerCuits($texto1);
assertEqual(1, count($result1), 'Encontró 1 CUIT en texto simple');
assertTrue(in_array('20123456786', $result1), 'El CUIT encontrado es correcto');

// Texto con múltiples CUITs
$texto2 = 'Proveedores: 20-12345678-6 y 30-12345678-1 y 27-12345678-0.';
$result2 = extraerCuits($texto2);
assertEqual(3, count($result2), 'Encontró 3 CUITs en texto múltiple');

// Texto sin CUITs
assertEqual([], extraerCuits('No hay CUITs acá.'), 'Texto sin CUITs');
assertEqual([], extraerCuits(''), 'Cadena vacía');
assertEqual([], extraerCuits(null), 'Null');

// CUITs con diferentes formatos en el mismo texto
$texto3 = 'Formatos: 20-12345678-6, 30.12345678.1, 27123456780';
$result3 = extraerCuits($texto3);
assertEqual(3, count($result3), 'Encontró CUITs en 3 formatos distintos');

// CUITs duplicados (debe devolver uno solo)
$texto4 = 'Mismo: 20-12345678-6 y 20-12345678-6 otra vez.';
$result4 = extraerCuits($texto4);
assertEqual(1, count($result4), 'CUITs duplicados se deduplican');

// Número de 12 dígitos no debe coincidir
$texto5 = '123456789012';  // 12 dígitos
assertEqual([], extraerCuits($texto5), 'Número de 12 dígitos no se confunde con CUIT');

// ===========================================================================
//  TESTS: validarLote()
// ===========================================================================

echo "\n=== TEST: validarLote() ===\n";

$lote1 = ['20-12345678-6', '30-12345678-1', '20-12345678-7'];
$resultadoLote = validarLote($lote1);

assertTrue($resultadoLote['success'], 'validarLote devuelve success');
assertEqual(3, $resultadoLote['resumen']['total'], 'Lote de 3 CUITs');
assertEqual(2, $resultadoLote['resumen']['validos'], '2 válidos en el lote');
assertEqual(1, $resultadoLote['resumen']['invalidos'], '1 inválido en el lote');
assertEqual(66.67, $resultadoLote['resumen']['tasa_validez'], 'Tasa de validez 66.67%');
assertEqual(3, count($resultadoLote['results']), '3 resultados individuales');

// Primer resultado: válido, con formateo correcto
assertTrue($resultadoLote['results'][0]['valido'], 'Primer CUIT del lote es válido');
assertEqual('20-12345678-6', $resultadoLote['results'][0]['formateado'], 'Formateo correcto del primer CUIT');

// Tercer resultado: inválido, debe tener diagnóstico
assertFalse($resultadoLote['results'][2]['valido'], 'Tercer CUIT del lote es inválido');
assertTrue(isset($resultadoLote['results'][2]['diagnostico']), 'CUIT inválido tiene diagnóstico');

// Lote vacío
$loteVacio = validarLote([]);
assertFalse($loteVacio['success'], 'Lote vacío devuelve success false');

// Lote con entrada no array
$loteNoArray = validarLote('no-soy-array');
assertFalse($loteNoArray['success'], 'Entrada no array devuelve success false');

// ===========================================================================
//  TESTS: cruzarConCalipso() (sin conexión)
// ===========================================================================

echo "\n=== TEST: cruzarConCalipso() ===\n";

$resultadoSinConexion = cruzarConCalipso('20-12345678-6');
assertFalse($resultadoSinConexion['success'], 'Sin PDO devuelve success false');
assertEqual('20-12345678-6', $resultadoSinConexion['cuit'], 'CUIT informado correctamente');
assertTrue(isset($resultadoSinConexion['sql_ejemplo']), 'Sin PDO incluye sql_ejemplo');
assertTrue(isset($resultadoSinConexion['sql_ejemplo']['sql_generado']), 'sql_ejemplo contiene SQL generado');

// CUIT inválido sin conexión
$resultadoInvalido = cruzarConCalipso('00-12345678-9');
assertFalse($resultadoInvalido['success'], 'CUIT inválido devuelve success false');
assertTrue(strpos($resultadoInvalido['error'], 'aritméticamente') !== false, 'Mensaje de error por CUIT inválido');

// ===========================================================================
//  TESTS: Casos realistas (integración ficticia)
// ===========================================================================

echo "\n=== TEST: Escenarios realistas ===\n";

// Simular cuerpo de email con factura
$emailBody = "
    Factura Nro: 0001-00001234
    Fecha: 15/06/2026
    Proveedor: 30-12345678-1
    Importe: \$ 1.234,56
    CUIT Cliente: 20-12345678-6
    Condición IVA: Responsable Inscripto
";

$cuitsEncontrados = extraerCuits($emailBody);
assertEqual(2, count($cuitsEncontrados), 'Email simulado: encuentra 2 CUITs');

// Simular texto OCR ruidoso
$ocrNoisy = "Proveedor: 3O-12345678-l | CUIT: 33.00000000.6";  // O y l en lugar de 0 y 1
$cuitsOCR = extraerCuits($ocrNoisy);
assertEqual(1, count($cuitsOCR), 'OCR ruidoso: encuentra solo el CUIT con puntos');
assertEqual('33000000006', $cuitsOCR[0], 'OCR ruidoso: CUIT correcto es 33-00000000-6');

// ===========================================================================
//  RESULTADOS
// ===========================================================================

echo "\n========================================\n";
echo "  RESUMEN DE TESTS\n";
echo "========================================\n";
echo "  Total:   $totalPruebas\n";
echo "  Pasaron: $pasaron\n";
echo "  Fallaron: $fallaron\n";
echo "========================================\n";

if ($fallaron > 0) {
    echo "\n  ERRORES:\n";
    foreach ($errores as $err) {
        echo "    $err\n";
    }
    echo "\n";
    exit(1);
}

echo "\n  Todos los tests pasaron correctamente.\n\n";
exit(0);
