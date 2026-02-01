<?php
require_once 'includes/autentifikacija.php';

// Ako je vec prijavljen, preusmjeri
if (jePrijavljen()) {
    if (jeAdmin()) {
        preusmjeri(APP_URL . '/admin/index.php');
    } else {
        preusmjeri(APP_URL . '/klijent/index.php');
    }
}

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnickoIme = ocistiUnos($_POST['korisnicko_ime'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';
    
    if (empty($korisnickoIme) || empty($lozinka)) {
        $greska = 'Unesite korisnicko ime i lozinku.';
    } else {
        $rezultat = prijaviKorisnika($korisnickoIme, $lozinka);
        
        if ($rezultat['uspjeh']) {
            if (in_array($rezultat['uloga'], ['admin', 'glavni_admin'])) {
                preusmjeri(APP_URL . '/admin/index.php');
            } else {
                preusmjeri(APP_URL . '/klijent/index.php');
            }
        } else {
            $greska = $rezultat['poruka'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava - <?= APP_NAZIV ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/prijava.css">
</head>
<body class="dark-mode">
    <div class="prijava-container">
        <div class="prijava-box">
            <div class="prijava-logo">
                <h1>BLACKBOX BIHAC</h1>
                <p style="color: var(--zinc-500); margin-top: 0.25rem; font-size: 0.9rem;">Prijavite se na sistem</p>
            </div>
            
            <?php if ($greska): ?>
                <div class="greska-poruka"><?= $greska ?></div>
            <?php endif; ?>
            
            <form class="prijava-form" method="POST">
                <div class="form-grupa">
                    <label for="korisnicko_ime">Korisničko ime</label>
                    <input type="text" id="korisnicko_ime" name="korisnicko_ime" required 
                           value="<?= htmlspecialchars($_POST['korisnicko_ime'] ?? '') ?>"
                           placeholder="Vaše korisničko ime">
                </div>
                
                <div class="form-grupa">
                    <label for="lozinka">Lozinka</label>
                    <input type="password" id="lozinka" name="lozinka" required
                           placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-primary prijava-btn">
                    Prijavi se
                </button>
            </form>
            
            <div class="prijava-footer">
                <a href="index.html">← Nazad na početnu</a>
            </div>
        </div>
    </div>
</body>
</html>