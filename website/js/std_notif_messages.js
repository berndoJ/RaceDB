const NOTIFICATION_NO_CONNECTION = "<b>Fehler</b><br/>Es konnte keine Verbindung zum Server aufgenommen werden.";
const NOTIFICATION_NO_SQL_CONNECTION = "<b>Fehler</b><br/>Der Server konnte keine Verbindung zur SQL-Datenbank aufbauen. Bitte kontaktieren Sie den Betreiber.";
const NOTIFICATION_UNKNOWN_RESPONSE = "<b>Fehler</b><br/>Der Server hat eine ungültige Antwort geliefert.";

function display_notification_default(notif) {
    switch (notif) {
        case NOTIFICATION_NO_CONNECTION:
            display_notification("error", notif);
            break;
        case NOTIFICATION_NO_SQL_CONNECTION:
            display_notification("error", notif);
            break;
        case NOTIFICATION_UNKNOWN_RESPONSE:
            display_notification("error", notif);
            break;
        default:
            display_notification("info", notif);
            break;
    }
}