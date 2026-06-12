function saveDispatchOutput_(report, runUuid) {
  const fileName = 'propuesta_despachos_' + runUuid + '.md';
  const folder = getOrCreateFolder_(DISPATCH_CONFIG.outputFolderName);
  folder.createFile(fileName, report, MimeType.PLAIN_TEXT);
}

function getOrCreateFolder_(folderName) {
  const folders = DriveApp.getFoldersByName(folderName);
  if (folders.hasNext()) return folders.next();
  return DriveApp.createFolder(folderName);
}

function logDispatchError_(runUuid, type, err) {
  const message = [
    new Date().toISOString(),
    runUuid,
    type,
    err && err.message ? err.message : String(err)
  ].join(' | ');

  console.error(message);
}

