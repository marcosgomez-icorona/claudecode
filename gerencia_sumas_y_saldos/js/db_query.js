// db_query.js
// Servicio Express que expone una API para ejecutar la consulta MCP contra SQL Server 2008 R2.
// EDITA la sección "config" con los valores reales de tu entorno antes de iniciar el servidor.

const express = require('express');
const sql = require('mssql');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

// -----------------------------------------------------------------------------
// CONFIGURACIÓN DE CONEXIÓN
// -----------------------------------------------------------------------------
// Reemplaza los valores a continuación con los datos de tu servidor SQL.
// - user: usuario con permisos de lectura en la base CORONA.
// - password: contraseña del usuario.
// - server: nombre del servidor (ej.: 192.168.0.23\\SQLEXPRESS o la IP del host).
// - database: nombre de la base de datos (usualmente CORONA).
// - options.encrypt: false para entornos locales sin TLS.
// - options.enableArithAbort: true (requerido para SQL Server 2008 R2).

const config = {
  user: 'PowerBi',
  password: 'Bi478',
  server: '192.168.0.177', // IP o hostname del servidor
  // Si la instancia es nombrada, descomenta la siguiente línea y ajusta el nombre
  // instanceName: 'SQLEXPRESS',
  database: 'CORONA',
  options: {
    encrypt: false,               // false para entornos locales sin TLS
    enableArithAbort: true,      // requerido por SQL Server 2008 R2
    trustServerCertificate: true // necesario si usas TLS auto‑firmado
  },
  // Tiempo máximo de conexión (ms). Ajusta si la red es lenta.
  connectionTimeout: 15000,
  requestTimeout: 30000
};

// -----------------------------------------------------------------------------
// CONSULTA MCP (snapshot por cuenta y periodo)
// -----------------------------------------------------------------------------
const queryTemplate = `SELECT
    V_EZI_CUENTAS.CODIGO,
    V_EZI_CUENTAS.DESCRIPCION AS CUENTA,
    V_EZI_CUENTAS.CRubro,
    V_EZI_CUENTAS.NRubro,
    V_EZI_CUENTAS.CSubrubro1,
    V_EZI_CUENTAS.NSubrubro1,
    V_EZI_CUENTAS.CSubrubro2,
    V_EZI_CUENTAS.NSubrubro2,
    V_EZI_CUENTAS.CSubrubro3,
    V_EZI_CUENTAS.NSubrubro3,
    SUM(V_VALOR_.IMPORTE) AS DEBE_PERIODO,
    SUM(V_VALOR_1.IMPORTE) AS HABER_PERIODO,
    SUM(V_VALOR_.IMPORTE) - SUM(V_VALOR_1.IMPORTE) AS SALDO_PERIODO
FROM V_TRCONTABLE_
INNER JOIN V_ITEMCONTABLE_ ON V_TRCONTABLE_.ITEMSTRANSACCION_ID = V_ITEMCONTABLE_.BO_PLACE_ID
INNER JOIN V_VALOR_ ON V_ITEMCONTABLE_.DEBE_ID = V_VALOR_.ID
INNER JOIN V_VALOR_ AS V_VALOR_1 ON V_ITEMCONTABLE_.HABER_ID = V_VALOR_1.ID
INNER JOIN V_EZI_CUENTAS ON V_ITEMCONTABLE_.REFERENCIA_ID = V_EZI_CUENTAS.ID
WHERE SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION,1,8) >= @startDate
  AND SUBSTRING(V_TRCONTABLE_.FECHAAPLICACION,1,8) <= @endDate
  AND V_EZI_CUENTAS.CODIGO BETWEEN '0' AND '9'
  AND V_TRCONTABLE_.ESTADO = 'C'
GROUP BY
    V_EZI_CUENTAS.CODIGO,
    V_EZI_CUENTAS.DESCRIPCION,
    V_EZI_CUENTAS.CRubro,
    V_EZI_CUENTAS.NRubro,
    V_EZI_CUENTAS.CSubrubro1,
    V_EZI_CUENTAS.NSubrubro1,
    V_EZI_CUENTAS.CSubrubro2,
    V_EZI_CUENTAS.NSubrubro2,
    V_EZI_CUENTAS.CSubrubro3,
    V_EZI_CUENTAS.NSubrubro3
ORDER BY V_EZI_CUENTAS.CODIGO;`;

app.post('/query', async (req, res) => {
  const { startDate, endDate } = req.body;
  if (!startDate || !endDate) {
    return res.status(400).json({ error: 'startDate y endDate son obligatorios' });
  }
  try {
    await sql.connect(config);
    const request = new sql.Request();
    request.input('startDate', sql.VarChar, startDate);
    request.input('endDate', sql.VarChar, endDate);
    const result = await request.query(queryTemplate);
    res.json(result.recordset);
  } catch (err) {
    console.error('Error al ejecutar la consulta:', err);
    res.status(500).json({ error: err.message });
  } finally {
    sql.close();
  }
});

const PORT = 3000;
app.listen(PORT, () => console.log(`Servicio DB corriendo en http://192.168.0.23:${PORT}`));
