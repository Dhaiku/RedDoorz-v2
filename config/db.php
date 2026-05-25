<?php
/**
 * db.php — now loads Firestore instead of MySQL.
 * All code that previously used $conn->query() now uses the
 * fs_*() helper functions from firestore.php.
 */
require_once __DIR__ . '/firestore.php';
