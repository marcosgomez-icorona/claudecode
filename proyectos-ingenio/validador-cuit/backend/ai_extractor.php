<?php
/**
 * Extractor de CUITs con IA — Ingenio La Corona
 *
 * Utiliza la API de DeepSeek (vía PHP cURL) para extraer CUITs de textos
 * no estructurados (OCR de facturas, PDFs, correos, mensajes).
 *
 * La IA se usa como complemento cuando la extracción por regex no es suficiente
 * (texto ruidoso, formatos no estándar, CUITs parciales, documentos escaneados).
 *
 * @package     ValidadorCUIT
 * @author      Ingenio La Corona - Sistemas
 * @version     1.0.0
 * @see         backend-ai-integration skill — Patrones de integración IA
 */

// ---------------------------------------------------------------------------
//  CONSTANTES DE CONFIGURACIÓN
// ---------------------------------------------------------------------------
define('AI_DEEPSEEK_URL',       getenv('AI_API_BASE_URL') ?: 'https://api.deepseek.com/v1');
define('AI_DEEPSEEK_MODEL',     'deepseek-chat');
define('AI_TIMEOUT_SEC',        30);
define('AI_MAX_TOKENS',         1024);

// ---------------------------------------------------------------------------
//  FUNCIÓN PRINCIPAL
// ---------------------------------------------------------------------------

/**
 * Extrae CUITs de un texto usando un modelo de lenguaje (DeepSeek).
 *
 * Envía el texto a la API de DeepSeek con un prompt estructurado que solicita
 * la extracción de todos los CUITs encontrados en formato JSON array.
 *
 * REQUISITOS:
 *   - Extensión PHP cURL habilitada.
 *   - Variable de entorno DEEPSEEK_API_KEY definida.
 *   - (Opcional) AI_API_BASE_URL para URL base personalizada.
 *
 * SEGURIDAD:
 *   - La API key se lee de getenv(), nunca se hardcodea.
 *   - Timeout máximo de 30 segundos.
 *   - Los CUITs extraídos se validan aritméticamente antes de devolverlos.
 *   - El uso se registra en corona_aux.ai_usage_log vía MySQL.
 *   - NO enviar datos personales sensibles anexos al texto si no es necesario.
 *
 * @param  string $texto     Texto del cual extraer CUITs.
 * @param  bool   $anonymize Si true, ofusca los CUITs en el texto antes de enviarlo a la API.
 * @return array  Con los keys: 'success', 'cuits' (array), 'error' (opcional), 'usage' (opcional).
 */
function extraerCuitsConIA($texto, $anonymize = false)
{
    // -----------------------------------------------------------------------
    //  Validaciones de entrada
    // -----------------------------------------------------------------------
    if ($texto === null || $texto === '' || !is_string($texto)) {
        return [
            'success' => false,
            'error'   => 'El texto está vacío o no es una cadena válida.',
            'cuits'   => [],
        ];
    }

    if (strlen($texto) > 50000) {
        return [
            'success' => false,
            'error'   => 'El texto excede el límite de 50.000 caracteres.',
            'cuits'   => [],
        ];
    }

    if (!extension_loaded('curl')) {
        error_log('[ValidadorCUIT-AI] La extensión PHP cURL no está disponible.');
        return [
            'success' => false,
            'error'   => 'La extensión cURL no está habilitada en el servidor.',
            'cuits'   => [],
        ];
    }

    // -----------------------------------------------------------------------
    //  Obtener API key del entorno (NUNCA hardcodeada)
    // -----------------------------------------------------------------------
    $apiKey = getenv('DEEPSEEK_API_KEY');
    if (!$apiKey) {
        error_log('[ValidadorCUIT-AI] DEEPSEEK_API_KEY no está definida en el entorno.');
        return [
            'success' => false,
            'error'   => 'La clave de API no está configurada (DEEPSEEK_API_KEY).',
            'cuits'   => [],
        ];
    }

    // -----------------------------------------------------------------------
    //  Anonimización opcional: reemplazar CUITs por marcadores
    // -----------------------------------------------------------------------
    $textoEnviado = $texto;
    $mascaras     = [];

    if ($anonymize) {
        // Extraer CUITs con regex y reemplazarlos por marcadores
        $textoEnviado = preg_replace_callback(
            '/(?<!\d)(\d{2})[\.\-\_]?(\d{8})[\.\-\_]?(\d)(?!\d)/',
            function ($match) use (&$mascaras) {
                $cuit       = $match[0];
                $mascarado  = 'CUIT_' . (count($mascaras) + 1);
                $mascaras[$mascarado] = $cuit;
                return $mascarado;
            },
            $texto
        );
    }

    // -----------------------------------------------------------------------
    //  Construir prompt para el LLM
    // -----------------------------------------------------------------------
    $systemPrompt = 'Sos un extractor de datos fiscales argentinos. '
        . 'Tu tarea es encontrar todos los CUITs (Código Único de Identificación Tributaria) '
        . 'presentes en el texto que se te proporciona. '
        . 'Un CUIT argentino tiene 11 dígitos y puede aparecer en formatos como '
        . 'XX-XXXXXXXX-X, XX.XXXXXXXX.X, XXXXXXXXXXX. '
        . 'Respondé SOLAMENTE con un array JSON válido de strings, '
        . 'sin formato adicional, sin markdown, sin explicaciones. '
        . 'Ejemplo: ["20-12345678-9", "30-98765432-1"]. '
        . 'Si no encontrás ningún CUIT, respondé [].';

    $userPrompt = 'Extraé todos los CUITs del siguiente texto:\n\n' . $textoEnviado;

    // -----------------------------------------------------------------------
    //  Llamada a la API
    // -----------------------------------------------------------------------
    $startTime    = microtime(true);
    $result       = _callDeepSeekChat($apiKey, $systemPrompt, $userPrompt);
    $durationMs   = (int) ((microtime(true) - $startTime) * 1000);

    // -----------------------------------------------------------------------
    //  Procesar respuesta
    // -----------------------------------------------------------------------
    if (!$result['success']) {
        _logAiUsage('extraerCuitsConIA', AI_DEEPSEEK_MODEL, 0, 0, $durationMs, false, $result['error']);
        return [
            'success' => false,
            'error'   => $result['error'],
            'cuits'   => [],
        ];
    }

    // Parsear JSON de la respuesta
    $cuitsExtraidos = _parsearRespuestaJson($result['content']);

    if ($cuitsExtraidos === null) {
        _logAiUsage('extraerCuitsConIA', AI_DEEPSEEK_MODEL, 0, 0, $durationMs, false, 'Respuesta JSON inválida del LLM');
        return [
            'success' => false,
            'error'   => 'La respuesta de la IA no pudo interpretarse como JSON.',
            'cuits'   => [],
            'raw'     => $result['content'],
        ];
    }

    // -----------------------------------------------------------------------
    //  Validar cada CUIT extraído con el algoritmo de AFIP
    // -----------------------------------------------------------------------
    // Incluir validador si no está ya cargado
    if (!function_exists('validarCuit')) {
        $validadorPath = __DIR__ . '/validador_cuit.php';
        if (file_exists($validadorPath)) {
            require_once $validadorPath;
        }
    }

    $cuitsValidos = [];
    foreach ($cuitsExtraidos as $cuit) {
        if (is_string($cuit) && function_exists('validarCuit') && validarCuit($cuit)) {
            $cuitsValidos[] = $cuit;
        }
    }

    // -----------------------------------------------------------------------
    //  Re-aplicar máscaras si se anonimizó
    // -----------------------------------------------------------------------
    if ($anonymize && !empty($mascaras)) {
        foreach ($cuitsValidos as $i => $cuit) {
            // Si el CUIT devuelto es un marcador, restaurar el original
            if (isset($mascaras[$cuit])) {
                $cuitsValidos[$i] = $mascaras[$cuit];
            }
        }
    }

    // -----------------------------------------------------------------------
    //  Log de uso exitoso
    // -----------------------------------------------------------------------
    $usage = $result['usage'] ?? [];
    _logAiUsage(
        'extraerCuitsConIA',
        AI_DEEPSEEK_MODEL,
        $usage['prompt_tokens'] ?? 0,
        $usage['completion_tokens'] ?? 0,
        $durationMs,
        true
    );

    return [
        'success' => true,
        'cuits'   => $cuitsValidos,
        'usage'   => [
            'prompt_tokens'     => $usage['prompt_tokens'] ?? 0,
            'completion_tokens' => $usage['completion_tokens'] ?? 0,
            'duration_ms'       => $durationMs,
        ],
    ];
}

// ---------------------------------------------------------------------------
//  FUNCIONES INTERNAS
// ---------------------------------------------------------------------------

/**
 * Llama a la API de DeepSeek Chat (OpenAI-compatible).
 *
 * @param  string $apiKey       API key de DeepSeek.
 * @param  string $systemPrompt Prompt de sistema.
 * @param  string $userPrompt   Prompt de usuario.
 * @return array  Con keys: 'success', 'content', 'error' (opcional), 'usage' (opcional).
 */
function _callDeepSeekChat($apiKey, $systemPrompt, $userPrompt)
{
    $url = rtrim(AI_DEEPSEEK_URL, '/') . '/chat/completions';

    $payload = json_encode([
        'model'       => AI_DEEPSEEK_MODEL,
        'messages'    => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ],
        'max_tokens'  => AI_MAX_TOKENS,
        'temperature' => 0.1,  // Baja temperatura para extracción precisa
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => AI_TIMEOUT_SEC,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS     => $payload,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error    = curl_error($ch);
    curl_close($ch);

    // Error de conexión
    if ($error) {
        error_log('[ValidadorCUIT-AI] Error cURL: ' . $error);
        return [
            'success' => false,
            'error'   => 'Error de conexión con la API de IA: ' . $error,
        ];
    }

    // Error HTTP
    if ($httpCode !== 200) {
        $mensaje = "HTTP $httpCode";
        $body    = json_decode($response, true);
        if ($body && isset($body['error']['message'])) {
            $mensaje .= ': ' . $body['error']['message'];
        }
        error_log('[ValidadorCUIT-AI] ' . $mensaje);
        return [
            'success' => false,
            'error'   => $mensaje,
        ];
    }

    // Parsear respuesta exitosa
    $data = json_decode($response, true);
    if (!$data || !isset($data['choices'][0]['message']['content'])) {
        error_log('[ValidadorCUIT-AI] Respuesta inesperada de la API: ' . substr($response, 0, 200));
        return [
            'success' => false,
            'error'   => 'Respuesta inesperada de la API de IA.',
        ];
    }

    return [
        'success' => true,
        'content' => $data['choices'][0]['message']['content'],
        'usage'   => $data['usage'] ?? null,
    ];
}

/**
 * Parsea la respuesta JSON del LLM extrayendo un array de strings.
 *
 * Maneja casos donde el modelo devuelve markdown, textos adicionales,
 * o formatos ligeramente irregulares.
 *
 * @param  string|null $respuesta Texto de respuesta del LLM.
 * @return array|null  Array de strings, o null si no se pudo parsear.
 */
function _parsearRespuestaJson($respuesta)
{
    if ($respuesta === null || $respuesta === '') {
        return null;
    }

    $contenido = trim($respuesta);

    // Intentar parsear directamente
    $datos = json_decode($contenido, true);
    if (is_array($datos)) {
        return $datos;
    }

    // Intentar extraer JSON con regex (si el modelo agregó markdown)
    if (preg_match('/\[[\s\S]*\]/', $contenido, $match)) {
        $datos = json_decode($match[0], true);
        if (is_array($datos)) {
            return $datos;
        }
    }

    // Intentar extraer CUITs individuales con regex como fallback
    $cuits = [];
    if (preg_match_all('/(?<!\d)(\d{2})[\.\-\_]?(\d{8})[\.\-\_]?(\d)(?!\d)/', $contenido, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $cuits[] = $m[1] . $m[2] . $m[3];
        }
    }

    return !empty($cuits) ? $cuits : null;
}

/**
 * Registra el uso de la API de IA en la tabla MySQL corona_aux.ai_usage_log.
 *
 * La tabla debe existir. Para crearla, ejecutar:
 *   proyectos-ingenio/validador-cuit/sql/create_ai_usage_log.sql
 *
 * @param  string $endpoint        Nombre del endpoint o función llamada.
 * @param  string $model           Modelo utilizado.
 * @param  int    $promptTokens    Tokens de entrada.
 * @param  int    $completionTokens Tokens de salida.
 * @param  int    $durationMs      Duración en milisegundos.
 * @param  bool   $success         Si la llamada fue exitosa.
 * @param  string|null $errorMsg   Mensaje de error (si corresponde).
 * @return void
 */
function _logAiUsage($endpoint, $model, $promptTokens, $completionTokens, $durationMs, $success, $errorMsg = null)
{
    // Intentar conectar a MySQL si está disponible.
    // Se usa la convención del proyecto: variables de entorno para la conexión.
    $host = getenv('MYSQL_HOST') ?: '127.0.0.1';
    $port = getenv('MYSQL_PORT') ?: '3306';
    $db   = getenv('MYSQL_DATABASE') ?: 'corona_aux';
    $user = getenv('MYSQL_USER') ?: 'root';
    $pass = getenv('MYSQL_PASSWORD') ?: '';

    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 3,
        ]);

        // Costo estimado: DeepSeek ~$0.14/M tokens input, ~$0.28/M tokens output (deepseek-chat)
        $estimatedCost = ($promptTokens * 0.14 / 1000000) + ($completionTokens * 0.28 / 1000000);

        $stmt = $pdo->prepare("
            INSERT INTO corona_aux.ai_usage_log
                (endpoint, model, prompt_tokens, completion_tokens, duration_ms, estimated_cost_usd, success, error_message, module)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, 'validador-cuit')
        ");
        $stmt->execute([
            $endpoint,
            $model,
            $promptTokens,
            $completionTokens,
            $durationMs,
            round($estimatedCost, 6),
            $success ? 1 : 0,
            $errorMsg,
        ]);

    } catch (PDOException $e) {
        // Falla silenciosa: el log no debe interrumpir el flujo principal.
        error_log('[ValidadorCUIT-AI] No se pudo registrar uso en ai_usage_log: ' . $e->getMessage());
    }
}
