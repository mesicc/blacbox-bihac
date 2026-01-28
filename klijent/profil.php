<?php
$naslovStranice = 'Moj profil';
require_once 'includes/header.php';

$korisnikId = $trenutniKorisnik['id'];

// Obrada forme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $podaci = [
        'ime' => ocistiUnos($_POST['ime']),
        'prezime' => ocistiUnos($_POST['prezime']),
        'email' => ocistiUnos($_POST['email']),
        'telefon' => ocistiUnos($_POST['telefon'])
    ];
    
    // Provjera lozinke
    if (!empty($_POST['nova_lozinka'])) {
        if ($_POST['nova_lozinka'] !== $_POST['potvrda_lozinke']) {
            prikaziPoruku('greska', 'Lozinke se ne podudaraju.');
            preusmjeri('profil.php');
        }
        
        if (!password_verify($_POST['trenutna_lozinka'], $trenutniKorisnik['lozinka'])) {
            prikaziPoruku('greska', 'Trenutna lozinka nije ispravna.');
            preusmjeri('profil.php');
        }
        
        $podaci['lozinka'] = $_POST['nova_lozinka'];
    }
    
    azurirajKorisnika($korisnikId, $podaci);
    prikaziPoruku('uspjeh', 'Profil uspjesno azuriran.');
    preusmjeri('profil.php');
}

// Dohvati clanarine
$clanarine = dohvatiClanarineKorisnika($korisnikId);
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
    <!-- Osnovni podaci -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Osnovni podaci</h2>
        </div>
        
        <form method="POST">
            <div class="form-grupa">
                <label>Ime</label>
                <input type="text" name="ime" value="<?= $trenutniKorisnik['ime'] ?>" required>
            </div>
            
            <div class="form-grupa">
                <label>Prezime</label>
                <input type="text" name="prezime" value="<?= $trenutniKorisnik['prezime'] ?>" required>
            </div>
            
            <div class="form-grupa">
                <label>Email</label>
                <input type="email" name="email" value="<?= $trenutniKorisnik['email'] ?>">
            </div>
            
            <div class="form-grupa">
                <label>Telefon</label>
                <input type="text" name="telefon" value="<?= $trenutniKorisnik['telefon'] ?>">
            </div>
            
            <hr style="border-color: var(--zinc-800); margin: 1.5rem 0;">
            
            <h3 style="color: var(--white); margin-bottom: 1rem;">Promjena lozinke</h3>
            
            <div class="form-grupa">
                <label>Trenutna lozinka</label>
                <input type="password" name="trenutna_lozinka">
            </div>
            
            <div class="form-grupa">
                <label>Nova lozinka</label>
                <input type="password" name="nova_lozinka">
            </div>
            
            <div class="form-grupa">
                <label>Potvrda nove lozinke</label>
                <input type="password" name="potvrda_lozinke">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Sacuvaj promjene</button>
        </form>
    </div>
    
    <!-- Clanarine -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Moje clanarine</h2>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Mjesec</th>
                        <th>Iznos</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($clanarine): ?>
                        <?php foreach ($clanarine as $c): ?>
                            <tr>
                                <td><?= imeMjeseca($c['mjesec']) ?> <?= $c['godina'] ?></td>
                                <td><?= number_format($c['iznos'], 2) ?> KM</td>
                                <td>
                                    <?php if ($c['placeno']): ?>
                                        <span class="badge badge-success">Placeno</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Neplaceno</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; color: var(--zinc-500);">Nema podataka o clanarinama.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>