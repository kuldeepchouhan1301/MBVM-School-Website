const WEBHOOK_SECRET = 'mbvm-school-secret-2026';
const UPLOAD_FOLDER_NAME = 'MBVM Admission Uploads';

const SHEETS = {
  admission: {
    name: 'Admissions',
    headers: [
      'submitted_at',
      'fullname',
      'nationality',
      'dob',
      'class',
      'session',
      'father_name',
      'father_mobile',
      'mother_name',
      'mother_mobile',
      'email',
      'id_card',
    ],
  },
  contact: {
    name: 'Contacts',
    headers: ['submitted_at', 'name', 'email', 'subject', 'message'],
  },
  result: {
    name: 'Results',
    headers: [
      'submitted_at',
      'student_name',
      'registration_no',
      'class_name',
      'session_year',
      'roll_no',
      'total_marks',
      'obtained_marks',
      'percentage',
      'grade',
      'status',
      'remarks',
    ],
  },
};

function doPost(event) {
  try {
    const payload = JSON.parse(event.postData.contents || '{}');

    if (WEBHOOK_SECRET && payload.secret !== WEBHOOK_SECRET) {
      return jsonResponse({ success: false, message: 'Invalid secret' }, 403);
    }

    const config = SHEETS[payload.type];
    if (!config) {
      return jsonResponse({ success: false, message: 'Unknown record type' }, 400);
    }

    const spreadsheet = SpreadsheetApp.getActiveSpreadsheet();
    const sheet = getOrCreateSheet(spreadsheet, config.name, config.headers);
    const record = payload.record || {};

    if (payload.type === 'admission' && payload.file) {
      record.id_card = saveUploadedFile(payload.file);
    }

    const row = config.headers.map((key) => {
      if (key === 'submitted_at') {
        return payload.submitted_at || new Date().toISOString();
      }

      return record[key] || '';
    });

    sheet.appendRow(row);

    return jsonResponse({ success: true });
  } catch (error) {
    return jsonResponse({ success: false, message: error.message }, 500);
  }
}

function saveUploadedFile(filePayload) {
  const bytes = Utilities.base64Decode(filePayload.contents_base64 || '');
  const name = filePayload.name || `admission-file-${Date.now()}`;
  const mimeType = filePayload.mime_type || 'application/octet-stream';
  const blob = Utilities.newBlob(bytes, mimeType, name);
  const folder = getOrCreateFolder(UPLOAD_FOLDER_NAME);
  const file = folder.createFile(blob);

  return file.getUrl();
}

function getOrCreateFolder(folderName) {
  const folders = DriveApp.getFoldersByName(folderName);

  if (folders.hasNext()) {
    return folders.next();
  }

  return DriveApp.createFolder(folderName);
}

function getOrCreateSheet(spreadsheet, sheetName, headers) {
  let sheet = spreadsheet.getSheetByName(sheetName);

  if (!sheet) {
    sheet = spreadsheet.insertSheet(sheetName);
  }

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(headers);
    sheet.setFrozenRows(1);
  }

  return sheet;
}

function jsonResponse(payload) {
  return ContentService
    .createTextOutput(JSON.stringify(payload))
    .setMimeType(ContentService.MimeType.JSON);
}
