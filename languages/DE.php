<?php
/**
 *
 * WebsiteBaker Community Edition (WBCE)
 * Way Better Content Editing.
 * Visit http://wbce.org to learn more and to join the community.
 *
 * @copyright Ryan Djurovich (2004-2009)
 * @copyright WebsiteBaker Org. e.V. (2009-2015)
 * @copyright WBCE Project (2015-)
 * @license GNU GPL2 (or any later version)
 *
 */

// Deutsche Modulbeschreibung
$module_description = 'Dieses Modul ermöglicht die Erstellung einer Datenbanksicherung oder die Sicherung aller Dateien auf dem Server.';

// Textausgaben
$MOD_BACKUP['BACKUP_HEADER'] 	  		= 'DATENSICHERUNG';
$MOD_BACKUP['BACKUP_COMPLETE'] 	  		= 'Gesamtsicherung - Alle Daten und die gesamte Datenbank<br>Die Rechte für alle Datenbanken sind dafür erforderlich!';
$MOD_BACKUP['BACKUP_WBCE'] 	  			= 'CMS Sicherung - Nur CMS-Daten und die CMS-Datenbank';
$MOD_BACKUP['BACKUP_PAGES'] 	  		= 'Alle erstellten Seiten sichern';
$MOD_BACKUP['BACKUP_INFO']				= 'Die Sicherung aller Verzeichnisse und Dateien kann je nach Umfang der installierten Module und Templates sowie der Anzahl Mediendateien lange dauern.'
                                        . '<br>Es werden nur Dateien bis max. %s MB ins Backup aufgenommen.<br>Leere Verzeichnisse werden nicht gesichert. Verzeichnis-Links wird nicht gefolgt!';
$MOD_BACKUP['BACKUP_START'] 	  		= 'Ausgewählte Sicherung starten';

$MOD_BACKUP['BACKUP_LIST_TITLE']		= 'Historie und Funktionen';
$MOD_BACKUP['BACKUP_LIST_HEADER'] 		= '<th>Datum</th><th class="l">Größe</th><th class="l">Typ</th><th class="r">Funktionen</th>';
$MOD_BACKUP['BACKUP_LIST_FULL'] 		= 'Gesamtsicherung';
$MOD_BACKUP['BACKUP_LIST_WBCE'] 		= 'CMS-Sicherung';
$MOD_BACKUP['BACKUP_LIST_PAGE'] 		= 'Seitensicherung';
$MOD_BACKUP['BACKUP_LIST_RECO'] 		= 'Wiederherstellung';

$MOD_BACKUP['BACKUP_WAIT']				= '<span id="waiting">Bitte warten <i class="fa fa fa-circle-o-notch fa-spin"></span>';
$MOD_BACKUP['BACKUP_DONE']				= 'Sicherung erfolgreich!';
$MOD_BACKUP['BACKUP_LOGFILE']			= 'Protokoll';
$MOD_BACKUP['BACKUP_ZIP_DOWNLOAD']		= 'Sicherung (Zip-Archiv) herunterladen';
$MOD_BACKUP['BACKUP_SQL_DOWNLOAD']		= 'SQL-Export herunterladen';

$MOD_BACKUP['BACKUP_DELETE']			= 'Diesen Eintrag löschen';
$MOD_BACKUP['BACKUP_SURE']				= 'Sind Sie wirklich sicher?';
$MOD_BACKUP['BACKUP_RESTORED']			= 'Sicherung erfolgreich wiederhergestellt.';
$MOD_BACKUP['BACKUP_RESTORE_WARNING']	= '<br><br>Bei der Wiederherstellung der Daten werden die vorhandenen Dateien und Datenbanktabellen gelöscht und die ausgewählten Daten wiederhergestellt.';
$MOD_BACKUP['BACKUP_WARNING1']			= '<br><br>Während dieser Funktion <b>keinesfalls</b> die Anwendung benutzen, sonst kann es zu Inkonsistenzen kommen!';
$MOD_BACKUP['BACKUP_WARNING2']			= '<br><br>Danach startet das Hauptmenü, eventuell ist eine erneute Anmeldung notwendig.';

$MOD_BACKUP['BACKUP_ZIP_ERROR']			= 'Zip-Fehler, Status: %s';
$MOD_BACKUP['BACKUP_LOG_ERROR']			= 'Fehler beim Erstellen der Log-Datei!';
$MOD_BACKUP['BACKUP_CREATE_DIR_ERROR']	= 'Fehler beim Erstellen des Verzeichnisses "%s"!';
$MOD_BACKUP['BACKUP_CREATE_SQL_ERROR']	= 'Fehler beim Erstellen der SQL-Datei!';
$MOD_BACKUP['BACKUP_DELETE_ZIP_ERROR']	= 'Fehler beim Löschen der Zip-Datei!';
$MOD_BACKUP['BACKUP_DELETE_LOG_ERROR']	= 'Fehler beim Löschen der zugehörigen Logdatei!';
$MOD_BACKUP['BACKUP_DELETE_SQL_ERROR']	= 'Fehler beim Löschen der SQL-Export Datei!';
$MOD_BACKUP['BACKUP_DELETE_PAGE_ERROR']	= 'Fehler beim Löschen des Seitenverzeichnisses "%s"!';
$MOD_BACKUP['BACKUP_READ_SQL_ERROR']	= 'Fehler beim Wiederherstellen der Sicherung!<br>SQL-Datei konnte nicht gelesen werden!';
$MOD_BACKUP['BACKUP_PARAMETER_ERROR']	= 'Parameter "%s" fehlt in der Datei "backup_settings.php"!';
