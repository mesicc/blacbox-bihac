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
    <style>
        .prijava-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .prijava-box {
            width: 100%;
            max-width: 400px;
            padding: 2.5rem;
            background: var(--zinc-900);
            border: 1px solid var(--zinc-800);
        }
        
        .prijava-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .prijava-logo h1 {
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--red-500), var(--orange-500));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .prijava-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        
        .form-grupa {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .form-grupa label {
            font-size: 0.875rem;
            color: var(--zinc-400);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .form-grupa input {
            padding: 0.75rem 1rem;
            background: var(--black);
            border: 1px solid var(--zinc-800);
            color: var(--white);
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-grupa input:focus {
            outline: none;
            border-color: var(--red-600);
        }
        
        .greska-poruka {
            padding: 0.75rem 1rem;
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: var(--red-500);
            font-size: 0.875rem;
        }
        
        .prijava-btn {
            margin-top: 0.5rem;
        }
        
        .prijava-footer {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .prijava-footer a {
            color: var(--red-500);
            text-decoration: none;
        }
        
        .prijava-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="dark-mode">
    <div class="prijava-container">
        <div class="prijava-box">
            <div class="prijava-logo">
                <h1>BLACKBOX BIHAC</h1>
                <p style="color: var(--zinc-500); margin-top: 0.5rem;">Prijavite se na sistem</p>
            </div>
            
            <?php if ($greska): ?>
                <div class="greska-poruka"><?= $greska ?></div>
            <?php endif; ?>
            
            <form class="prijava-form" method="POST">
                <div class="form-grupa">
                    <label for="korisnicko_ime">Korisnicko ime</label>
                    <input type="text" id="korisnicko_ime" name="korisnicko_ime" required 
                           value="<?= htmlspecialchars($_POST['korisnicko_ime'] ?? '') ?>">
                </div>
                
                <div class="form-grupa">
                    <label for="lozinka">Lozinka</label>
                    <input type="password" id="lozinka" name="lozinka" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full prijava-btn">
                    <span>Prijavi se</span>
                </button>
            </form>
            
            <div class="prijava-footer">
                <a href="index.html">Nazad na pocetnu</a>
            </div>
        </div>
    </div>
</body>
</html>