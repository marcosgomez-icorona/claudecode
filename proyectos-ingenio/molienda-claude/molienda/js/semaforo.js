(function () {

    var FONDOS = {
        verde:    'rgba(40, 167, 69, 0.13)',
        amarillo: 'rgba(255, 193, 7, 0.20)',
        rojo:     'rgba(220, 53, 69, 0.13)',
        gris:     'rgba(108, 117, 125, 0.08)'
    };

    function parseNum(s) {
        if (!s || s === '--') return NaN;
        return parseFloat(String(s).replace(',', '.').trim());
    }

    // Retorna: 'verde' | 'amarillo' | 'rojo' | 'gris'
    // direction: 'target' (simétrico ±5%/±10%) | 'less' (cuanto menos, mejor)
    // objetivo: '' → gris | '0' → cero-especial | 'N a M' → rango | 'N' → punto
    function calcSemaforo(valorStr, objetivoStr, direction) {
        var obj = String(objetivoStr || '').trim();
        if (!obj || obj === '--') return 'gris';

        var v = parseNum(valorStr);
        if (isNaN(v)) return 'gris';

        // Rango: "19 a 21" o "1.5-2.5"
        var rango = obj.match(/^([\d.,]+)\s*(?:a|-)\s*([\d.,]+)$/i);
        if (rango) {
            var min = parseNum(rango[1]), max = parseNum(rango[2]);
            if (isNaN(min) || isNaN(max)) return 'gris';
            if (v >= min && v <= max) return 'verde';
            var margen = ((min + max) / 2) * 0.10;
            if (v >= min - margen && v <= max + margen) return 'amarillo';
            return 'rojo';
        }

        var o = parseNum(obj);
        if (isNaN(o)) return 'gris';

        // Objetivo = 0: valor 0 → verde, cualquier otro → rojo
        if (o === 0) return (v === 0) ? 'verde' : 'rojo';

        // Cuanto menos, mejor: por debajo del objetivo → verde
        if (direction === 'less') {
            if (v <= o) return 'verde';
            var ex = (v - o) / o;
            if (ex <= 0.10) return 'amarillo';
            return 'rojo';
        }

        // Simétrico ±5% verde / ±10% amarillo / fuera rojo
        var dev = Math.abs(v - o) / o;
        if (dev <= 0.05) return 'verde';
        if (dev <= 0.10) return 'amarillo';
        return 'rojo';
    }

    function aplicarSemaforos() {
        document.querySelectorAll('.semaforo-card').forEach(function (card) {
            var estado = calcSemaforo(
                card.dataset.valor     || '--',
                card.dataset.objetivo  || '',
                card.dataset.direction || 'target'
            );
            card.style.backgroundColor = FONDOS[estado];
            card.querySelectorAll('img[data-semaforo]').forEach(function (img) {
                img.style.display = (img.dataset.semaforo === estado) ? 'inline' : 'none';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        aplicarSemaforos();
        // Auto-refresh cada 10 minutos
        setTimeout(function () { location.reload(); }, 600000);
    });

})();
