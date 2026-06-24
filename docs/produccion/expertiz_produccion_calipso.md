# Módulo de Producción — ERP Calipso
## Ingenio La Corona — Análisis Completo del Schema

> **Fecha:** 2026-06-24  
> **Fuente:** SQL Server 2008 R2 `CORONA` — tablas `pr_*` y `pr_ezi_*`  
> **Filas analizadas:** ~1M registros en 11+ tablas, 186 vistas asociadas

---

## 1. Arquitectura General del Módulo

El módulo de producción de Calipso en Ingenio La Corona es una extensión custom (`pr_ezi_*`) que gestiona el ciclo completo cañero → ingenio:

```
CONTRATOS → FINCAS → ENTRADA CAÑA → PESAJE → LABORATORIO → LIQUIDACIÓN
                                                      ↓
                                              INVENTARIO FÁBRICA
                                              PROCESO INDUSTRIAL
                                              DESTILERÍA
                                              DESPACHOS (azúcar/alcohol/melaza)
```

### Tablas núcleo (11 tablas, ~988K registros)

| Tabla | Filas | Rol |
|-------|-------|-----|
| `pr_ezi_movimientos` | 464,877 | **Registro central de entrada de caña** — cada camión/carga |
| `pr_ezi_muestraLab` | 278,162 | **Análisis de laboratorio** — pol, brix, pureza, rendimiento |
| `pr_ezi_lecturapolbrix` | 201,428 | **Lecturas pol/brix** — vinculadas al cañero |
| `pr_ezi_individuales` | 14,420 | **Análisis individuales** — 50 campos dinámicos |
| `pr_ezi_remitos_finca` | 15,592 | **Remitos de finca** — detalle de cosecha/transporte |
| `pr_ezi_contratos` | 4,063 | **Contratos con cañeros** — precio, tipo, porcentaje |
| `pr_ezi_Liquidacion` | 3,847 | **Liquidaciones** — pago a proveedores de servicio |
| `pr_ezi_finca` | 1,051 | **Fincas/parcelas** — código, sector, distancia, precálculo |
| `pr_ezi_FincaCañero` | 1 | **Relación finca↔cañero** — M:N |
| `pr_ezi_peso_balanza` | 1 | **Peso bruto de balanza** — registro crudo |
| `pr_ezi_combustible` | 0 | Combustible (sin uso en esta BD) |

---

## 2. Flujo de Entrada de Caña — Paso a Paso

### 2.1 El Contrato (pr_ezi_contratos)

Antes de la zafra, se firma un contrato con cada cañero:

```
pr_ezi_contratos
├── id_contrato (PK)
├── id_caniero → PROVEEDOR (UUID)
├── id_tipo_cania → tipo de caña (convencional, orgánica, etc.)
├── id_tipo_contrato → tipo de contrato
├── porcentaje → % de participación
├── kilos → kilos comprometidos
├── pesos → precio por kilo
├── prto → prorrateo
├── fijo → precio fijo
├── flete → costo de flete
├── cosecha → costo de cosecha
├── autorizado → flag de aprobación
├── estado → activo/cancelado
├── FechaDesde / FechaHasta → vigencia
```

### 2.2 La Finca (pr_ezi_finca + pr_ezi_FincaCañero)

Cada cañero tiene una o más fincas (parcelas):

```
pr_ezi_finca
├── id_finca (PK)
├── nombre → nombre de la finca
├── codfinca → código (ej: "9501")
├── codsector → sector (ej: "01")
├── kilometros → distancia al ingenio
├── surcos → cantidad de surcos
├── precalculo → toneladas estimadas
├── empresa → empresa propietaria
└── nomsurco → nombre del surco

pr_ezi_FincaCañero (M:N)
├── Id_FincaCaniero (PK)
├── Id_Finca → pr_ezi_finca
└── Id_Caniero → PROVEEDOR
```

### 2.3 La Entrada — Movimiento (pr_ezi_movimientos) ⭐ TABLA MAESTRA

Es el registro de cada camión que entra al ingenio. **58 columnas.**

#### Datos del productor/transporte
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_movimiento` | bigint PK | Número único de movimiento |
| `id_caniero` | UUID FK | Cañero (→ PROVEEDOR) |
| `id_transportista` | UUID FK | Transportista |
| `razon_social` | nvarchar(50) | Nombre del cañero |
| `transporte` | nvarchar(50) | Nombre del transportista |
| `patente` | nvarchar(50) | Patente del camión |
| `chofer` | nvarchar(50) | Nombre del chofer |
| `camion` | nvarchar(50) | Identificación del camión |

#### Datos de pesaje
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `numero_ingreso` | bigint | Número de ingreso al ingenio |
| `numero_pesada` | bigint | **Número de pesada (clave para lab)** |
| `peso_bruto` | float | Peso bruto (camión + caña) |
| `tara` | float | Tara (camión vacío) |
| `peso_neto` | float | **Peso neto = bruto − tara** |
| `trash` | float | **% trash (materia extraña)** |
| `neto_cana` | float | **Peso neto de caña = neto × (1 − trash/100)** |
| `mlimpia` | float | Muestra limpia (para lab) |
| `msucia` | float | Muestra sucia (para lab) |
| `despunte` | float | Despunte (kg) |
| `despunteporcentaje` | float | % despunte |
| `trashReal` | float | Trash real (corregido) |
| `netoCanaReal` | float | Neto caña real (corregido) |

#### Tipo de pesada
| Campo | Valor | Significado |
|-------|-------|-------------|
| `tipo_pesada` | `C` | Caña |
| `tipopesada` | `M` | Manual |
| `tipopesada` | `A` | Automática |
| `tipotara` | `A` | Tara automática |
| `tipotara` | `M` | Tara manual |
| `prepesada` | `F` | False — no es prepesada |
| `cantpesadas` | int | Cantidad de pesadas |
| `pesada1`, `pesada2` | float | Pesadas individuales |
| `tara1`, `tara2` | float | Taras individuales |

#### Fechas y trazabilidad
| Campo | Descripción |
|-------|-------------|
| `fecha_entrada` + `hora_entrada` | Ingreso del camión al ingenio |
| `fecha_pesada` + `hora_pesada` | Momento del pesaje |
| `fecha_salida` + `hora_salida` | Salida del camión |
| `fechaindustrial` | Fecha de procesamiento industrial |
| `usuario` | Usuario que registró la entrada |
| `usuariopesada` | Usuario que realizó el pesaje |
| `usuario_salida` | Usuario que registró la salida |
| `usuariomanual` | Usuario de entrada manual |
| `usuarioautoriza` | Usuario que autorizó |

#### Datos de finca/origen
| Campo | Descripción |
|-------|-------------|
| `codfinca` | Código de finca |
| `sector` | Sector de la finca |
| `remito` | Número de remito |
| `id_tipo_cana` | Tipo de caña (UUID) |
| `id_destino` | Destino (patio, etc.) |
| `destino` | Descripción del destino |
| `mesa` | Número de mesa de muestreo |
| `nrofrente` | Número de frente de cosecha |
| `nromaquina` | Número de máquina cosechadora |

### 2.4 El Remito de Finca (pr_ezi_remitos_finca)

Documento que acompaña la carga desde la finca. **28 columnas, 15,592 registros.**

| Campo | Descripción |
|-------|-------------|
| `idmovimiento` | FK → pr_ezi_movimientos |
| `numero_pesada` | Número de pesada |
| `finca` | Código de finca (ej: "9501") |
| `sector` | Sector (ej: "01") |
| `remito` | Número de remito (ej: "15101") |
| `tipocaña` | Tipo de caña (ej: "Integral") |
| `bruto`, `tara`, `neto` | Pesos |
| `trash`, `netocaña` | Trash y neto |
| `cañero` | UUID del cañero |
| `eq_cosechadora`, `pe_cosechadora` | Equipo y persona cosechadora |
| `eq_autovolcable` | Equipo autovolcable |
| `eq_tractor`, `pe_tractor` | Tractor y tractorista |
| `eq_cargadora`, `pe_cargadora` | Cargadora y operador |
| `camion`, `acoplado`, `semi` | Vehículos |
| `servicio` | Tipo de servicio (ej: "Flete Solo Camion/Tractor") |
| `ingenio` | Nombre del ingenio ("Corona") |

### 2.5 Laboratorio — Muestra (pr_ezi_muestraLab) ⭐

Análisis de calidad de cada muestra. **23 columnas, 278,162 registros.**

| Campo | Descripción |
|-------|-------------|
| `id_MuestraLab` (PK) | ID único de muestra |
| `NumeroAnalisis` | Número de análisis (0 = automático) |
| `NumeroPesada` | **FK → pr_ezi_movimientos.numero_pesada** |
| `idmovimiento` | FK → pr_ezi_movimientos |
| `MS` | **Muestra Sucia** — peso de la muestra con impurezas (~20.3g) |
| `ML` | **Muestra Limpia** — peso después de limpiar (~18.2g) |

#### Métricas de calidad

| Métrica | Descripción | Rango típico |
|---------|-------------|-------------|
| `Pol` | Polarización (sacarosa aparente) | 50-57 |
| `Brix` | Sólidos solubles totales | 15-16 |
| `Temperatura` | Temperatura de lectura (°C) | 24-28 |
| `Polporciento` | **Pol % caña** (corregido) | 12-14% |
| `Brixporciento` | Brix % caña | 15-16% |
| `Pureza` | **Pureza = Pol/Brix × 100** | 78-86% |
| `Rendimiento` | **Rendimiento industrial %** | 7-9% |
| `RendimientoReal` | Rendimiento real (corregido) | 7-9% |

#### Kilogramos Recuperables

| Métrica | Descripción | Fórmula |
|---------|-------------|---------|
| `KRPol` | **Kilos recuperables de pol** | `neto_cana × Polporciento / 100` |
| `KRBrix` | Kilos recuperables de brix | `neto_cana × Brixporciento / 100` |

#### Métricas de Jugo Virgen (sufijo V)

| Métrica | Descripción |
|---------|-------------|
| `PolporcientoJugoV` | Pol % en jugo virgen |
| `BrixporcientoJugoV` | Brix % en jugo virgen |
| `PurezaV` | Pureza del jugo virgen |
| `RendimientoV` | Rendimiento sobre jugo virgen |

### 2.6 Lectura Pol/Brix (pr_ezi_lecturapolbrix)

Tabla de lecturas directas del laboratorio. **201,428 registros.**

| Campo | Descripción |
|-------|-------------|
| `id_caniero` | UUID del cañero |
| `marca` | Número de marca/identificación |
| `temperatura` | Temperatura de lectura |
| `brix` | Lectura de brix |
| `pol` | Lectura de pol |
| `fecha`, `hora` | Momento de la lectura |
| `idmuestralab` | FK → pr_ezi_muestraLab |

### 2.7 Análisis Individuales (pr_ezi_individuales)

Tabla de propósito general con 50 campos dinámicos + fecha/químico/hora. **14,420 registros.**

Los campos `campo1` a `campo50` son nvarchar(50) genéricos — usados para almacenar distintos tipos de datos de laboratorio según configuración.

### 2.8 Configuración (pr_ezi_configuracion)

Parámetros de la zafra:

| Campo | Descripción |
|-------|-------------|
| `Periodo` | Período de zafra (ej: "2024") |
| `CoefCorrecTemperatura` | Coeficiente de corrección por temperatura |
| `Java` | Tipo de Java (método de cálculo) |
| `C11` a `C15` | Coeficientes de corrección para pol |

---

## 3. Liquidación y Pagos

### 3.1 Liquidación de Servicios (pr_ezi_Liquidacion)

| Campo | Descripción |
|-------|-------------|
| `NroLiquidacion` (PK) | Número de liquidación |
| `id_proveedorServicio` | FK → PROVEEDORSERVICIOS |
| `Tipo` | Cosecha, Transporte, etc. |
| `Total` | Monto total |
| `fecha` | Fecha de liquidación |
| `Autorizado` | Flag de autorización |
| `Importado` | Flag de importación a Calipso |

### 3.2 Precios de Servicios (pr_ezi_precioServicios)

Tablas por año (2016, 2017, 2018) con precios de servicios (cosecha, flete, etc.).

---

## 4. Inventario de Fábrica (pr_ezi_inventario_fabrica)

Registro de inventario de productos en fábrica con 50 campos dinámicos + fecha/número/hora.

Campos genéricos `campo1`-`campo50` para distintos tipos de productos (azúcar, alcohol, melaza, etc.).

---

## 5. Proceso Industrial — Vistas Clave

### v_pr_procesoindustrial
Vista maestra que consolida el proceso industrial completo (molienda, fabricación, destilería).

### v_pr_detallado_cania
Detalle de caña por cañero/finca con acumulados.

### v_pr_acumulado_cania_qv
Acumulado de caña por quincena/vencimiento.

### v_pr_acumulado_tirada / v_pr_tirada
Tirada = molienda diaria. Acumulados por día de zafra.

### v_pr_ezi_servicios_*
Vistas consolidadas de servicios:
- `v_pr_ezi_servicios_cosecha` — cosecha
- `v_pr_ezi_servicios_cosecha_cierre` — cierre de cosecha
- `v_pr_ezi_servicios_transportes` — transporte
- `v_pr_ezi_servicios_transportes_cierre` — cierre de transporte
- `v_pr_ezi_servicios_ctsc` — costo total de servicios de cosecha
- `V_PR_SERVICIOS_LIQ_COSECHA` / `V_PR_SERVICIOS_LIQ_TRANSPORTE` — liquidaciones

### v_pr_liquidaciones_servicios
Liquidaciones de servicios consolidadas.

### v_pr_ezi_liquidacion_canieros
Liquidación a cañeros — integra contratos, pesajes, calidad.

### v_pr_control_pesadas
Control de pesadas — auditoría de pesajes.

### v_pr_canieros / v_pr_ec_canieros*
Vistas de cañeros con acumulados de entrega.

### v_pr_contratos / v_pr_contratos_datos
Contratos con datos calculados (kilos entregados vs comprometidos).

---

## 6. Balanzas e Informes

### v_pr_informe_balanza_azucar
Informe de balanza para despachos de azúcar.

### v_pr_informe_balanza_alcohol
Informe de balanza para despachos de alcohol.

### v_pr_informe_balanza_varios
Informe de balanza para otros productos.

### v_pr_remitos_*
Vistas de remitos impresos, totales, informes.

### v_pr_stock
Vista de stock de productos terminados.

---

## 7. Relación con Otras Tablas del ERP

| Tabla PR | FK | Tabla ERP | Relación |
|----------|-----|-----------|----------|
| `pr_ezi_movimientos.id_caniero` | UUID | `PROVEEDOR.ID` | Cañero = proveedor |
| `pr_ezi_movimientos.id_transportista` | UUID | `PROVEEDOR.ID` | Transportista = proveedor |
| `pr_ezi_movimientos.id_tipo_cana` | UUID | `pr_ezi_especiales`? | Tipo de caña |
| `pr_ezi_contratos.id_caniero` | UUID | `PROVEEDOR.ID` | Contrato con cañero |
| `pr_ezi_contratos.id_tipo_contrato` | UUID | `pr_ezi_especiales`? | Tipo contrato |
| `pr_ezi_FincaCañero.Id_Caniero` | UUID | `PROVEEDOR.ID` | Cañero dueño de finca |
| `pr_ezi_Liquidacion.id_proveedorServicio` | UUID | `PROVEEDORSERVICIOS.ID` | Proveedor de servicio |
| `pr_ezi_remitos_finca.cañero` | UUID | `PROVEEDOR.ID` | Cañero en remito |
| `pr_ezi_lecturapolbrix.id_caniero` | UUID | `PROVEEDOR.ID` | Cañero en lectura |

---

## 8. Fórmulas de Cálculo — Producción Cañera

### 8.1 Peso neto de caña
```
peso_neto = peso_bruto − tara
neto_cana = peso_neto × (1 − trash/100)
```
Ejemplo: bruto=52,820, tara=18,220, neto=34,600, trash=14.22%
→ netocaña = 34,600 × (1 − 0.1422) = 29,680 kg

### 8.2 Rendimiento industrial
```
Rendimiento = KRPol / neto_cana × 100
KRPol = neto_cana × Polporciento / 100
```
Ejemplo: neto=29,680, Pol%=13.90% → KRPol=4,125 kg, Rendimiento=9.02%

### 8.3 Pureza
```
Pureza = Pol / Brix × 100  (en jugo)
Pureza = Polporciento / Brixporciento × 100  (en caña)
```
Ejemplo: Pol=13.90, Brix=16.24 → Pureza=85.59%

### 8.4 Factor de corrección por temperatura
```
Pol_corregido = Pol_lectura × (1 + CoefCorrecTemperatura × (T_lectura − 20))
```

---

## 9. Tipos de Pesaje — Clasificación Completa

La tabla `pr_ezi_movimientos` es **multipropósito**: no solo caña, sino TODOS los productos que se pesan en la balanza del ingenio. El campo `tipo_pesada` clasifica y `descripcion` detalla.

### Tipos principales (464,877 registros)

| Código | Significado | Registros | % |
|--------|-------------|-----------|----|
| `C` | **Caña** (sugarcane) | 306,927 | 66.0% |
| `V` | **Varios** (multi-product) | 129,521 | 27.9% |
| `A` | **Alcohol** / Azúcar (?)* | 17,296 | 3.7% |
| `L` | (por confirmar) | 6,602 | 1.4% |
| `X` | (por confirmar) | 209 | 0.04% |
| `Y` | (por confirmar) | 98 | 0.02% |
| `Z` | (por confirmar) | 24 | 0.005% |

*\* Requiere verificación con usuarios del sistema*

### Productos registrados (tipo V = Varios)

#### Subproductos de proceso (SALIDA del ingenio)

| Producto | Registros | Destino típico |
|----------|-----------|----------------|
| **Vinaza** | 95,101 | Fincas (fertirriego) |
| **Cachaza** | 20,008 | Fincas (abono) |
| **Ceniza** | 12,035 | Fincas / disposición |
| **Bagazo** | 379 | Alimentación calderas |
| **Basura** | 823 | Disposición final |

#### Productos terminados (DESPACHOS)

| Producto | Registros |
|----------|-----------|
| Melaza (varios nombres) | 874 |
| Azúcar | 135 |
| Alcohol (buen gusto, mal gusto, anhidro) | 117 |

#### Insumos de fábrica (ENTRADA al ingenio)

| Producto | Registros |
|----------|-----------|
| Cal / Cal Hidratada | 449 |
| Gas Oil / Diesel | 438 |
| Chips de Madera | 186 |
| Azufre | 83 |
| Soda Cáustica | 77 |
| Sal Industrial | 59 |
| Ácido Sulfúrico | 98 |
| Urea / Fertilizantes | 50 |

### Flujo de Cachaza y Vinaza

```
FÁBRICA (produce) → BALANZA (pesa) → CAMIÓN (transporta) → FINCA (aplica)
                                                              │
                                    destino: Las Moras (38K), Los Gucheas (27K),
                                    Arcadia (16K), Los Trejos (14K), etc.
```

La cachaza y vinaza **salen** del ingenio hacia las fincas como:
- **Cachaza**: abono orgánico (residuo del filtrado del jugo)
- **Vinaza**: fertirriego (residuo de la destilación de alcohol)
Muchos campos tienen `NULL` en los datos históricos:
- `pesada1`, `pesada2`, `tara1`, `tara2` — doble pesada (no implementado)
- `mlimpia`, `msucia` — se registran en `pr_ezi_muestraLab`
- `despunte`, `despunteporcentaje` — no usado
- `trashReal`, `netoCanaReal` — correcciones no usadas
- `fechaindustrial` — no sincronizado
- `mesa`, `cantpesadas` — no usado

---

## 10. Ciclo Completo — De la Finca a la Liquidación

```
┌─────────────────────────────────────────────────────────┐
│ 1. CONTRATACIÓN (pre-zafra)                             │
│    pr_ezi_contratos ← cañero, tipo caña, precio, kilos  │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 2. ENTRADA DE CAÑA (durante zafra)                      │
│    Camión llega al ingenio                               │
│    → pr_ezi_movimientos: entrada, patente, chofer       │
│    → pr_ezi_remitos_finca: finca, sector, remito        │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 3. PESAJE                                               │
│    Balanza registra peso bruto                          │
│    → pr_ezi_movimientos.peso_bruto                      │
│    → pr_ezi_movimientos.tara (después de descargar)     │
│    → Calcula: neto = bruto − tara                       │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 4. MUESTREO (mesa de laboratorio)                       │
│    Toma de muestra para análisis de calidad             │
│    → MS (muestra sucia), ML (muestra limpia)            │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 5. ANÁLISIS DE LABORATORIO                              │
│    pr_ezi_muestraLab / pr_ezi_lecturapolbrix           │
│    → Pol, Brix, Temperatura                             │
│    → Cálculo: Pol%, Brix%, Pureza, Rendimiento          │
│    → KRPol = kg de azúcar recuperable                   │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 6. CONFIGURACIÓN DE ZAFRA                               │
│    pr_ezi_configuracion: C11-C15, Java, CoefTemperatura │
│    → Corrección de lecturas                             │
└─────────────────────────────────────────────────────────┘
                         ↓
┌─────────────────────────────────────────────────────────┐
│ 7. LIQUIDACIÓN (post-zafra o periódica)                 │
│    pr_ezi_Liquidacion + vistas v_pr_ezi_liquidacion_*   │
│    → Cálculo de pago por calidad y cantidad             │
│    → Integración con PROVEEDOR / CxP                    │
└─────────────────────────────────────────────────────────┘
```

---

## 11. Pendientes de Investigación

1. **Cachaza** — No se encontró tabla específica para cachaza. Posiblemente se registra como un tipo de pesada en `pr_ezi_movimientos` con `tipo_pesada` ≠ 'C'.
2. **Otros tipos de pesaje** — Alcohol, melaza, azúcar usan vistas `v_pr_informe_balanza_*` pero la tabla base es `pr_ezi_movimientos` o `pr_ezi_peso_balanza`.
3. **pr_ezi_peso_balanza** — Solo 1 registro. Puede ser una tabla legacy o para un tipo específico de balanza.
4. **Destilería** — Tablas `pr_ezi_destileria_cubas*`, vistas `v_pr_ezi_paradas_destileria`, `v_pr_ezi_informedestileria`. Pendiente analizar.
5. **Relación con OPC/PLC** — Las balanzas probablemente se integran vía OPC KepServer → Node-RED → SQL Server.
6. **Molienda Web** — El proyecto `molienda-web` en el repo consume estos datos para cañeros.

---

## 12. Documentación Relacionada en el Repo

- `/mnt/c/claudecode/proyectos-ingenio/molienda/` — Molienda Web (PHP/MySQL)
- `/mnt/c/claudecode/flows/node red/` — Flows Node-RED de conexión OPC
- `/mnt/c/claudecode/agente_despachos/` — Despachos de azúcar
- `pr_ezi_ordenes_azucar`, `pr_ezi_ordenes_alcohol`, `pr_ezi_ordenes_melaza` — Órdenes de despacho
