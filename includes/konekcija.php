<?php
// ============================================
// BLACKBOX BIHAC - KONEKCIJA SA BAZOM
// ============================================

require_once __DIR__ . '/konfiguracija.php';

function dohvatiKonekciju() {
    static $konekcija = null;
    
    if ($konekcija === null) {
        try {
            $konekcija = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAZIV . ";charset=utf8mb4",
                DB_KORISNIK,
                DB_LOZINKA,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $greska) {
            die("Greska pri konekciji na bazu: " . $greska->getMessage());
        }
    }
    
    return $konekcija;
}

// Globalna konekcija
$konekcija = dohvatiKonekciju();