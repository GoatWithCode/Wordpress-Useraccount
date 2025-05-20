<?php
// Datenbankverbindung
$db_host = 'DATABASE HOST';
$db_user = 'USERNAME';
$db_pass = 'PASSWORD';
$db_name = 'DATABASENAME';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Neuen Benutzer vorbereiten
$username = 'NEWUSER';
$password_plain = 'USERPASS';
$email = 'new_user@example.com';
$role = 'administrator';

// Passwort als WP-Hash erzeugen
$hashed_password = password_hash($password_plain, PASSWORD_BCRYPT);

// Zeitstempel
$now = date('Y-m-d H:i:s');

// Benutzer einfügen
$sql = "INSERT INTO wp_users 
    (user_login, user_pass, user_nicename, user_email, user_status, display_name, user_registered)
    VALUES 
    ('$username', '$hashed_password', '$username', '$email', 0, '$username', '$now')";

if ($conn->query($sql) === TRUE) {
    $user_id = $conn->insert_id;

    // Rolle zuweisen in wp_usermeta
    $meta_sql_1 = "INSERT INTO wp_usermeta (user_id, meta_key, meta_value) VALUES 
        ($user_id, 'wp_capabilities', '" . serialize([$role => true]) . "'),
        ($user_id, 'wp_user_level', '0')";

    if ($conn->query($meta_sql_1) === TRUE) {
        echo "Benutzer erfolgreich erstellt mit ID: $user_id";
    } else {
        echo "Fehler bei Rollen-Zuweisung: " . $conn->error;
    }
} else {
    echo "Fehler beim Erstellen des Benutzers: " . $conn->error;
}

$conn->close();
?>