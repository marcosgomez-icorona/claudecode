/**
 * logger.js — Logger simple con timestamps ISO.
 */

const LEVELS = { debug: 10, info: 20, warn: 30, error: 40 };
const currentLevel = LEVELS[process.env.LOG_LEVEL] ?? LEVELS.info;

function ts() {
  return new Date().toISOString();
}

export const log = {
  debug: (msg) => { if (LEVELS.debug >= currentLevel) console.debug(`${ts()} DEBUG ${msg}`); },
  info:  (msg) => { if (LEVELS.info  >= currentLevel) console.info(`${ts()} INFO  ${msg}`); },
  warn:  (msg) => { if (LEVELS.warn  >= currentLevel) console.warn(`${ts()} WARN  ${msg}`); },
  error: (msg) => { if (LEVELS.error >= currentLevel) console.error(`${ts()} ERROR ${msg}`); },
};
