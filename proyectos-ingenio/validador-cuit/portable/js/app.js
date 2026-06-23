/* ══════════════════════════════════════════════════════════════
   Validador de CUITs — App principal
   Ingenio La Corona — Bioenergia La Corona S.A.
   ══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  // ── Config ──
  const CONFIG = {
    backendUrl: '/validador-cuit/backend/validador_cuit.php',
    timeout: 15000,
    cuitRegex: /\b(\d{2})(\d{8})(\d)\b/g,
    cuitFormattedRegex: /^\d{2}-\d{8}-\d$/,
    checkDigitWeights: [5, 4, 3, 2, 7, 6, 5, 4, 3, 2]
  };

  // ── DOM refs ──
  const $ = function (id) { return document.getElementById(id); };
  const dom = {};

  function cacheDomRefs () {
    dom.inputTexto        = $('input-texto');
    dom.inputLabel        = $('input-label');
    dom.inputHint         = $('input-hint');
    dom.modoDirecto       = $('modo-directo');
    dom.modoIa            = $('modo-ia');
    dom.btnValidar        = $('btn-validar');
    dom.btnLimpiar        = $('btn-limpiar');
    dom.btnText           = $('btn-text');
    dom.btnSpinner        = $('btn-spinner');
    dom.btnIcon           = $('btn-icon');
    dom.cuitsPreview      = $('cuits-preview');
    dom.cuitsCount        = $('cuits-count');
    dom.cuitsList         = $('cuits-list');
    dom.statsBar          = $('stats-bar');
    dom.statTotal         = $('stat-total');
    dom.statValidos       = $('stat-validos');
    dom.statInvalidos     = $('stat-invalidos');
    dom.statRegistrados   = $('stat-registrados');
    dom.resultsPanel      = $('results-panel');
    dom.resultsBody       = $('results-body');
    dom.resultsCount      = $('results-count');
    dom.errorContainer    = $('error-container');
    dom.loadingOverlay    = $('loading-overlay');
    dom.loadingText       = $('loading-text');
    dom.backendStatus     = $('backend-status');
    dom.toastContainer    = $('toast-container');
    dom.footerTime        = $('footer-time');
  }

  // ── CUIT Utilities ──

  /** Extrae CUITs del texto: busca digitos, los formatea y devuelve array unico */
  function extraerCuits (texto) {
    var raw = [];
    var match;
    var re = new RegExp(CONFIG.cuitRegex);
    while ((match = re.exec(texto)) !== null) {
      raw.push(match[1] + '-' + match[2] + '-' + match[3]);
    }
    // Deduplicar manteniendo orden
    return raw.filter(function (c, i) { return raw.indexOf(c) === i; });
  }

  /** Valida el digito verificador de un CUIT (modulo 11 AFIP) */
  function validarDigitoVerificador (cuit) {
    var digits = cuit.replace(/-/g, '');
    if (digits.length !== 11) return false;
    var suma = 0;
    for (var i = 0; i < 10; i++) {
      suma += parseInt(digits.charAt(i), 10) * CONFIG.checkDigitWeights[i];
    }
    var resto = 11 - (suma % 11);
    if (resto === 11) resto = 0;
    if (resto === 10) resto = 9;
    return resto === parseInt(digits.charAt(10), 10);
  }

  /** Valida formato XX-XXXXXXXX-X */
  function validarFormato (cuit) {
    return CONFIG.cuitFormattedRegex.test(cuit);
  }

  /** Formatea un CUIT de 11 digitos a XX-XXXXXXXX-X */
  function formatearCuit (digitos) {
    var d = digitos.replace(/-/g, '');
    if (d.length !== 11) return digitos;
    return d.substr(0, 2) + '-' + d.substr(2, 8) + '-' + d.substr(10, 1);
  }

  // ── UI State ──

  var estado = {
    procesando: false,
    modo: 'directo',
    resultados: []
  };

  // ── Backend health check ──

  function verificarBackend () {
    dom.backendStatus.textContent = 'Verificando backend...';
    dom.backendStatus.className = 'badge-status checking';

    fetch(CONFIG.backendUrl + '?action=health', {
      method: 'GET',
      signal: AbortSignal.timeout(5000)
    })
      .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
      })
      .then(function (data) {
        if (data && data.success) {
          dom.backendStatus.textContent = 'Backend online';
          dom.backendStatus.className = 'badge-status online';
        } else {
          dom.backendStatus.textContent = 'Backend respondio pero con error';
          dom.backendStatus.className = 'badge-status offline';
        }
      })
      .catch(function () {
        // Backend no disponible — el validador funciona offline (solo validacion local)
        dom.backendStatus.textContent = 'Solo validacion local';
        dom.backendStatus.className = 'badge-status offline';
      });
  }

  // ── Toast notifications ──

  function mostrarToast (mensaje, tipo) {
    tipo = tipo || 'success';
    var iconos = {
      success: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1D9E75" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>',
      error:   '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A32D2D" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      warning: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#BA7517" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>'
    };
    var toast = document.createElement('div');
    toast.className = 'toast-corona ' + tipo;
    toast.innerHTML = iconos[tipo] || iconos.success;
    toast.appendChild(document.createTextNode(' ' + mensaje));
    toast.addEventListener('click', function () {
      toast.remove();
    });
    dom.toastContainer.appendChild(toast);
    setTimeout(function () {
      if (toast.parentNode) toast.remove();
    }, 4000);
  }

  // ── Error display ──

  function mostrarError (mensaje) {
    dom.errorContainer.innerHTML =
      '<div class="error-box" role="alert">' +
        '<svg class="error-box-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">' +
          '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>' +
        '</svg>' +
        '<span>' + escaparHtml(mensaje) + '</span>' +
      '</div>';
    dom.errorContainer.classList.remove('d-none');
  }

  function limpiarError () {
    dom.errorContainer.classList.add('d-none');
    dom.errorContainer.innerHTML = '';
  }

  // ── Helper: escapar HTML ──

  function escaparHtml (str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
  }

  // ── Modo selector ──

  function actualizarModo () {
    estado.modo = dom.modoIa.checked ? 'ia' : 'directo';
    if (estado.modo === 'directo') {
      dom.inputHint.textContent = 'Ingrese CUITs o pegue texto de factura. Los CUITs se detectaran automaticamente por su formato numerico (11 digitos).';
      dom.cuitsPreview.classList.add('d-none');
    } else {
      dom.inputHint.textContent = 'Pegue el texto completo de una factura. La IA analizara el contenido y extraera los CUITs de proveedores.';
      dom.cuitsPreview.classList.remove('d-none');
      actualizarPreviewCuits();
    }
  }

  // ── Preview de CUITs detectados ──

  function actualizarPreviewCuits () {
    var texto = dom.inputTexto.value;
    var cuits = extraerCuits(texto);
    dom.cuitsCount.textContent = cuits.length;
    if (cuits.length > 0) {
      dom.cuitsList.textContent = cuits.join(', ');
      dom.cuitsPreview.classList.remove('d-none');
    } else {
      dom.cuitsList.textContent = '';
      if (estado.modo === 'ia') {
        dom.cuitsPreview.classList.remove('d-none');
      } else {
        dom.cuitsPreview.classList.add('d-none');
      }
    }
  }

  // ── Formateo en tiempo real ──

  function formatearCuitsEnTextarea () {
    var ta = dom.inputTexto;
    var pos = ta.selectionStart;
    var oldLen = ta.value.length;

    // Reemplazar secuencias de 11 digitos sin formato
    var newVal = ta.value.replace(CONFIG.cuitRegex, '$1-$2-$3');

    if (newVal !== ta.value) {
      var charDiff = newVal.length - oldLen;
      ta.value = newVal;
      // Ajustar cursor si estaba al final o cerca del reemplazo
      if (pos >= oldLen - 2) {
        ta.selectionStart = ta.selectionEnd = newVal.length;
      }
    }

    // Actualizar preview
    actualizarPreviewCuits();
  }

  // ── Loading state ──

  function mostrarCarga (texto) {
    estado.procesando = true;
    dom.btnValidar.disabled = true;
    dom.btnSpinner.classList.remove('d-none');
    dom.btnText.textContent = texto || 'Procesando...';
    dom.loadingOverlay.classList.remove('d-none');
    dom.loadingText.textContent = texto || 'Procesando CUITs...';
  }

  function ocultarCarga () {
    estado.procesando = false;
    dom.btnValidar.disabled = false;
    dom.btnSpinner.classList.add('d-none');
    dom.btnText.textContent = 'Validar CUITs';
    dom.loadingOverlay.classList.add('d-none');
  }

  // ── API calls ──

  function llamarApi (params) {
    var url = CONFIG.backendUrl + '?' + params;
    return fetch(url, {
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      signal: AbortSignal.timeout(CONFIG.timeout)
    }).then(function (res) {
      if (!res.ok) throw new Error('Error HTTP ' + res.status + ' al contactar el servidor');
      return res.json();
    }).then(function (data) {
      if (!data || data.error) {
        throw new Error((data && data.error) || 'Respuesta invalida del servidor');
      }
      return data;
    });
  }

  function validarCuits (cuitsArray) {
    var cuitsParam = cuitsArray.map(function (c) {
      return encodeURIComponent(c.replace(/-/g, ''));
    }).join(',');
    return llamarApi('action=validar&cuits=' + cuitsParam);
  }

  function extraerCuitsConIa (texto) {
    return llamarApi('action=extraer&texto=' + encodeURIComponent(texto));
  }

  // ── Procesar validacion ──

  function manejarValidacion () {
    limpiarError();

    var texto = dom.inputTexto.value.trim();
    if (!texto) {
      mostrarError('Ingrese texto con CUITs antes de validar.');
      dom.inputTexto.focus();
      return;
    }

    if (estado.modo === 'ia') {
      // Modo IA: primero extraer, luego validar
      mostrarCarga('Extrayendo CUITs con IA...');
      extraerCuitsConIa(texto)
        .then(function (data) {
          var cuitsExtraidos = [];
          if (data && data.cuits_extraidos && Array.isArray(data.cuits_extraidos)) {
            cuitsExtraidos = data.cuits_extraidos;
          }
          if (cuitsExtraidos.length === 0) {
            ocultarCarga();
            mostrarError('La IA no pudo extraer CUITs del texto proporcionado. Verifique el contenido o use modo "Validacion directa".');
            return;
          }
          mostrarCarga('Validando ' + cuitsExtraidos.length + ' CUITs...');
          return validarCuits(cuitsExtraidos);
        })
        .then(function (resultados) {
          ocultarCarga();
          if (resultados) renderizarResultados(resultados);
        })
        .catch(function (err) {
          ocultarCarga();
          var mensaje = err.message || 'Error al procesar la solicitud.';
          if (err.name === 'TimeoutError' || err.name === 'AbortError') {
            mensaje = 'La solicitud tardo demasiado. Verifique la conexion con el servidor.';
          }
          mostrarError(mensaje);
        });
    } else {
      // Modo directo: extraer CUITs con regex del lado cliente
      var cuits = extraerCuits(texto);

      // Si no se encontraron CUITs con el patron numerico, intentar parsear lineas como CUITs directos
      if (cuits.length === 0) {
        var lineas = texto.split(/[\n,;]+/);
        lineas.forEach(function (linea) {
          var c = linea.trim();
          if (validarFormato(c)) {
            cuits.push(c);
          } else {
            // Intentar formatear 11 digitos
            var limpio = c.replace(/[\s-]/g, '');
            if (/^\d{11}$/.test(limpio)) {
              cuits.push(formatearCuit(limpio));
            }
          }
        });
        // Deduplicar
        cuits = cuits.filter(function (c, i) { return cuits.indexOf(c) === i; });
      }

      if (cuits.length === 0) {
        mostrarError('No se encontraron CUITs en el texto ingresado. Verifique que los CUITs tengan 11 digitos.');
        return;
      }

      mostrarCarga('Validando ' + cuits.length + ' CUITs...');

      // Primero hacemos validacion local del digito verificador
      cuits.forEach(function (cuit) {
        var valido = validarFormato(cuit) && validarDigitoVerificador(cuit);
        // Agregamos un placeholder para los resultados locales
        estado.ultimosCuitsLocales = estado.ultimosCuitsLocales || [];
        estado.ultimosCuitsLocales.push({
          cuit: cuit,
          validoLocal: valido
        });
      });

      // Luego llamamos al backend
      validarCuits(cuits)
        .then(function (data) {
          ocultarCarga();
          if (data && data.results && Array.isArray(data.results)) {
            renderizarResultados(data);
          } else {
            renderizarResultadosDesdeLocal(cuits);
          }
        })
        .catch(function (err) {
          ocultarCarga();
          // Si el backend falla, mostrar validacion local
          var mensaje = err.message || 'Error de conexion con el servidor.';
          mostrarToast('Backend no disponible. Mostrando validacion local.', 'warning');
          renderizarResultadosDesdeLocal(cuits);
        });
    }
  }

  // ── Renderizado de resultados ──

  function renderizarResultados (data) {
    var resultados = data.results || [];
    var resumen = data.resumen || {};

    if (resultados.length === 0) {
      mostrarError('No se recibieron resultados del servidor.');
      return;
    }

    estado.resultados = resultados;

    // Actualizar stats
    dom.statTotal.textContent       = resumen.total       || resultados.length;
    dom.statValidos.textContent     = resumen.validos     || 0;
    dom.statInvalidos.textContent   = resumen.invalidos   || 0;
    dom.statRegistrados.textContent = resumen.registrados || 0;
    dom.statsBar.classList.remove('d-none');

    // Actualizar tabla
    var tbody = dom.resultsBody;
    tbody.innerHTML = '';

    resultados.forEach(function (r) {
      var tr = document.createElement('tr');

      // Determinar estado para coloreado de fila
      var valido = r.valido === true || r.valido === '1' || r.valido === 1;
      var registrado = r.registrado === true || r.registrado === '1' || r.registrado === 1;

      if (!valido) {
        tr.className = 'tr-invalid';
      } else if (!registrado) {
        tr.className = 'tr-unregistered';
      } else {
        tr.className = 'tr-valid';
      }

      // CUIT
      var tdCuit = document.createElement('td');
      tdCuit.className = 'mono fw-medium';
      tdCuit.textContent = r.cuit || '—';
      tr.appendChild(tdCuit);

      // Formateado
      var tdForm = document.createElement('td');
      tdForm.className = 'mono';
      tdForm.textContent = r.formateado || formatearCuit(r.cuit || '') || '—';
      tr.appendChild(tdForm);

      // Valido
      var tdVal = document.createElement('td');
      if (valido) {
        tdVal.innerHTML = '<span class="badge-cuit badge-cuit-ok">' +
          '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>' +
          ' SI</span>';
      } else {
        tdVal.innerHTML = '<span class="badge-cuit badge-cuit-err">' +
          '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
          ' NO</span>';
      }
      tr.appendChild(tdVal);

      // Registrado en Calipso
      var tdReg = document.createElement('td');
      if (registrado) {
        tdReg.innerHTML = '<span class="badge-cuit badge-cuit-ok">' +
          '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>' +
          ' SI</span>';
      } else if (valido && !registrado) {
        tdReg.innerHTML = '<span class="badge-cuit badge-cuit-warn">' +
          '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' +
          ' NO</span>';
      } else {
        tdReg.innerHTML = '<span class="badge-cuit badge-cuit-na">—</span>';
      }
      tr.appendChild(tdReg);

      // Detalle
      var tdDet = document.createElement('td');
      tdDet.className = 'text-secondary';
      tdDet.textContent = r.detalle || (valido ? 'CUIT valido' : 'CUIT invalido');
      tr.appendChild(tdDet);

      tbody.appendChild(tr);
    });

    dom.resultsCount.textContent = resultados.length + ' CUIT(s) procesado(s)';
    dom.resultsPanel.classList.remove('d-none');

    // Scroll a la tabla
    dom.resultsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function renderizarResultadosDesdeLocal (cuits) {
    // Cuando el backend no esta disponible, mostramos validacion local
    var resultados = cuits.map(function (cuit) {
      var formateado = cuit.indexOf('-') > -1 ? cuit : formatearCuit(cuit);
      var formatoOk = validarFormato(formateado);
      var digitoOk = formatoOk && validarDigitoVerificador(formateado);
      var valido = formatoOk && digitoOk;
      return {
        cuit: formateado,
        formateado: formateado,
        valido: valido,
        registrado: false,
        detalle: valido
          ? 'CUIT valido (validacion local — backend no disponible)'
          : (formatoOk ? 'Digito verificador invalido' : 'Formato invalido')
      };
    });

    renderizarResultados({
      results: resultados,
      resumen: {
        total: resultados.length,
        validos: resultados.filter(function (r) { return r.valido; }).length,
        invalidos: resultados.filter(function (r) { return !r.valido; }).length,
        registrados: 0
      }
    });
  }

  // ── Limpiar ──

  function limpiarTodo () {
    dom.inputTexto.value = '';
    dom.statsBar.classList.add('d-none');
    dom.resultsPanel.classList.add('d-none');
    dom.resultsBody.innerHTML =
      '<tr class="empty-row"><td colspan="5">' +
        '<div class="empty-state"><p>No hay resultados para mostrar.</p></div>' +
      '</td></tr>';
    dom.errorContainer.classList.add('d-none');
    dom.errorContainer.innerHTML = '';
    dom.cuitsPreview.classList.add('d-none');
    dom.inputTexto.focus();
  }

  // ── Footer clock ──

  function actualizarReloj () {
    var ahora = new Date();
    var opciones = {
      year: 'numeric', month: '2-digit', day: '2-digit',
      hour: '2-digit', minute: '2-digit'
    };
    dom.footerTime.textContent = ahora.toLocaleString('es-AR', opciones);
  }

  // ── Event listeners ──

  function registrarEventos () {
    dom.btnValidar.addEventListener('click', manejarValidacion);
    dom.btnLimpiar.addEventListener('click', limpiarTodo);

    dom.modoDirecto.addEventListener('change', actualizarModo);
    dom.modoIa.addEventListener('change', actualizarModo);

    // Formateo en tiempo real con debounce
    var timeoutId = null;
    dom.inputTexto.addEventListener('input', function () {
      if (timeoutId) clearTimeout(timeoutId);
      timeoutId = setTimeout(formatearCuitsEnTextarea, 300);
    });

    // Enter en textarea con Ctrl+Enter o Meta+Enter para validar
    dom.inputTexto.addEventListener('keydown', function (e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        manejarValidacion();
      }
    });
  }

  // ── Init ──

  function init () {
    cacheDomRefs();
    actualizarModo();
    registrarEventos();
    verificarBackend();
    actualizarReloj();
    setInterval(actualizarReloj, 60000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
