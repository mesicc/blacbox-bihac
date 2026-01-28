<?php
// ============================================
// BLACKBOX BIHAC - KONFIGURACIJA
// ============================================

// Baza podataka
define('DB_HOST', 'localhost');
define('DB_KORISNIK', 'root');
define('DB_LOZINKA', '');
define('DB_NAZIV', 'blackbox_bihac');

// Aplikacija
define('APP_NAZIV', 'BlackBox Bihac');
define('APP_URL', 'http://localhost/blacbox-bihac');

// Sesija
define('SESIJA_TRAJANJE', 3600); // 1 sat

// Timezone
date_default_timezone_set('Europe/Sarajevo');

// Error reporting (iskljuci u produkciji)
error_reporting(E_ALL);
ini_set('display_errors', 1);