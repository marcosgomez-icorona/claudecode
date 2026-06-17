/**
 * Configuración de backends Node-RED para el Dashboard de Conciliación Bancaria
 *
 * El dashboard intenta conectar en orden: cloud → LAN
 * Si el primario (cloud) falla en <timeout>ms, pasa al secundario (LAN)
 */
const CONFIG = {
  backends: [
    {
      name: 'Cloud',
      url: 'http://ingcorona.ddns.net:4040',
      timeout: 5000
    },
    {
      name: 'LAN',
      url: 'http://192.168.0.23:1880',
      timeout: 5000
    }
  ]
};
