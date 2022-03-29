<?php

namespace App\Support;

class MimeTypes
{
    //Mime Types suportados pelo Google Workspace: https://developers.google.com/drive/api/v3/ref-export-formats
    public const HTML = 'text/html';
    public const HTML_ZIP = 'application/zip';
    public const PLAIN_TEXT = 'text/plain';
    public const RICH_TEXT = 'application/rtf';
    public const OPEN_OFFICE_DOC = 'application/vnd.oasis.opendocument.text';
    public const PDF = 'application/pdf';
    public const MS_WORD = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    public const EPUB = 'application/epub+zip';
    public const MS_EXCEL = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    public const OPEN_OFFICE_SHEET = 'application/x-vnd.oasis.opendocument.spreadsheet';
    public const CSV = 'text/csv';
    public const SHEET_ONLY = 'text/tab-separated-values';
    public const JPEG = 'image/jpeg';
    public const PNG = 'image/png';
    public const SVG = 'image/svg+xml';
    public const MS_POWER_POINT = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    public const OPEN_OFFICE_PRESENTATION = 'application/vnd.oasis.opendocument.presentation';
    public const JSON = 'application/vnd.google-apps.script+json';

    //Mime Types do Google Drive API https://developers.google.com/drive/api/v3/mime-types
    public const AUDIO = 'application/vnd.google-apps.audio';
    public const GOOGLE_DOCS = 'application/vnd.google-apps.document';
    public const SHORT_CUT_3RD_PARTY = 'application/vnd.google-apps.drive-sdk';
    public const GOOGLE_DRAWING = 'application/vnd.google-apps.drawing';
    public const GOOGLE_DRIVE_FOLDER = 'application/vnd.google-apps.folder';
    public const GOOGLE_FORMS = 'application/vnd.google-apps.form';
    public const GOOGLE_FUSION_TABLES = 'application/vnd.google-apps.fusiontable';
    public const GOOGLE_JAMBOARD = 'application/vnd.google-apps.jam';
    public const GOOGLE_MY_MAPS = 'application/vnd.google-apps.map';
    public const PHOTO = 'application/vnd.google-apps.photo';
    public const GOOGLE_SLIDES = 'application/vnd.google-apps.presentation';
    public const GOOGLE_APPS_SCRIPT = 'application/vnd.google-apps.script';
    public const SHORTCUT = 'application/vnd.google-apps.shortcut';
    public const GOOGLE_SITES = 'application/vnd.google-apps.site';
    public const GOOGLE_SHEETS = 'application/vnd.google-apps.spreadsheet';
    public const UNKNOWN = 'application/vnd.google-apps.unknown';
    public const VIDEO = 'application/vnd.google-apps.video';
}
