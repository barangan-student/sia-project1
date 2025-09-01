<?php
$db = new SQLite3('friends.db');

$db->exec("CREATE TABLE IF NOT EXISTS cezar_friends (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    number TEXT NOT NULL,
    url TEXT
)");
?>
