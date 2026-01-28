<?php
// ============================================
// BLACKBOX BIHAC - AUTENTIFIKACIJA
// ============================================

session_start();

require_once __DIR__ . '/funkcije.php';

function prijaviKorisnika($korisnickoIme, $lozinka) {
    $korisnik = dohvatiKorisnikaPoKorisnickomImenu($korisnickoIme);
    
    if (!$korisnik) {
        return ['uspjeh' => false, 'poruka' => 'Korisnicko ime ne postoji.'];
    }
    
    if (!$korisnik['aktivan']) {
        return ['uspjeh' => false, 'poruka' => 'Vas racun je deaktiviran. Kontaktirajte admina.'];
    }
    
    if (!password_verify($lozinka, $korisnik['lozinka'])) {
        return ['uspjeh' => false, 'poruka' => 'Pogresna lozinka.'];
    }
    
    // Postavi sesiju
    $_SESSION['korisnik_id'] = $korisnik['id'];
    $_SESSION['korisnicko_ime'] = $korisnik['korisnicko_ime'];
    $_SESSION['ime'] = $korisnik['ime'];
    $_SESSION['prezime'] = $korisnik['prezime'];
    $_SESSION['uloga'] = $korisnik['uloga'];
    $_SESSION['vrijeme_prijave'] = time();
    
    return ['uspjeh' => true, 'uloga' => $korisnik['uloga']];
}

function odjaviKorisnika() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

function jePrijavljen() {
    return isset($_SESSION['korisnik_id']) && isset($_SESSION['vrijeme_prijave']);
}

function jeAdmin() {
    return jePrijavljen() && in_array($_SESSION['uloga'], ['admin', 'glavni_admin']);
}

function jeGlavniAdmin() {
    return jePrijavljen() && $_SESSION['uloga'] === 'glavni_admin';
}

function jeKlijent() {
    return jePrijavljen() && $_SESSION['uloga'] === 'klijent';
}

function dohvatiTrenutnogKorisnika() {
    if (!jePrijavljen()) {
        return null;
    }
    return dohvatiKorisnikaPoId($_SESSION['korisnik_id']);
}

function zahtijevajPrijavu() {
    if (!jePrijavljen()) {
        prikaziPoruku('greska', 'Morate se prijaviti za pristup ovoj stranici.');
        preusmjeri(APP_URL . '/prijava.php');
    }
}

function zahtijevajAdmina() {
    zahtijevajPrijavu();
    
    if (!jeAdmin()) {
        prikaziPoruku('greska', 'Nemate dozvolu za pristup ovoj stranici.');
        preusmjeri(APP_URL . '/klijent/index.php');
    }
}

function zahtijevajGlavnogAdmina() {
    zahtijevajPrijavu();
    
    if (!jeGlavniAdmin()) {
        prikaziPoruku('greska', 'Samo glavni admin ima pristup ovoj stranici.');
        preusmjeri(APP_URL . '/admin/index.php');
    }
}

function zahtijevajKlijenta() {
    zahtijevajPrijavu();
    
    if (!jeKlijent()) {
        preusmjeri(APP_URL . '/admin/index.php');
    }
}