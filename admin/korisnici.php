<?php
$naslovStranice = 'Korisnici';
require_once 'includes/header.php';

// Obrada forme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    if ($akcija === 'kreiraj') {
        $podaci = [
            'korisnicko_ime' => ocistiUnos($_POST['korisnicko_ime']),
            'lozinka' => $_POST['lozinka'],
            'ime' => ocistiUnos($_POST['ime']),
            'prezime' => ocistiUnos($_POST['prezime']),
            'email' => ocistiUnos($_POST['email']),
            'telefon' => ocistiUnos($_POST['telefon']),
            'uloga' => $_POST['uloga'],
            'kreirao_id' => $_SESSION['korisnik_id']
        ];
        
        // Provjera uloge - samo glavni admin moze kreirati druge admine
        if ($podaci['uloga'] !== 'klijent' && !jeGlavniAdmin()) {
            prikaziPoruku('greska', 'Nemate dozvolu za kreiranje admina.');
            preusmjeri('korisnici.php');
        }
        
        try {
            $korisnikId = kreirajKorisnika($podaci);
            
            // Dodaj u grupe
            if (isset($_POST['grupe']) && is_array($_POST['grupe'])) {
                foreach ($_POST['grupe'] as $grupaId) {
                    dodajKorisnikaUGrupu($korisnikId, $grupaId);
                }
            }
            
            prikaziPoruku('uspjeh', 'Korisnik uspjesno kreiran.');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                prikaziPoruku('greska', 'Korisnicko ime vec postoji.');
            } else {
                prikaziPoruku('greska', 'Greska pri kreiranju korisnika.');
            }
        }
        
        preusmjeri('korisnici.php');
    }
    
    if ($akcija === 'azuriraj') {
        $korisnikId = (int)$_POST['korisnik_id'];
        $podaci = [
            'ime' => ocistiUnos($_POST['ime']),
            'prezime' => ocistiUnos($_POST['prezime']),
            'email' => ocistiUnos($_POST['email']),
            'telefon' => ocistiUnos($_POST['telefon']),
            'aktivan' => isset($_POST['aktivan']) ? 1 : 0
        ];
        
        if (!empty($_POST['lozinka'])) {
            $podaci['lozinka'] = $_POST['lozinka'];
        }
        
        azurirajKorisnika($korisnikId, $podaci);
        
        // Azuriraj grupe
        global $konekcija;
        $konekcija->prepare("DELETE FROM korisnik_grupe WHERE korisnik_id = ?")->execute([$korisnikId]);
        
        if (isset($_POST['grupe']) && is_array($_POST['grupe'])) {
            foreach ($_POST['grupe'] as $grupaId) {
                dodajKorisnikaUGrupu($korisnikId, $grupaId);
            }
        }
        
        prikaziPoruku('uspjeh', 'Korisnik uspjesno azuriran.');
        preusmjeri('korisnici.php');
    }
    
    if ($akcija === 'obrisi') {
        $korisnikId = (int)$_POST['korisnik_id'];
        obrisiKorisnika($korisnikId);
        prikaziPoruku('uspjeh', 'Korisnik uspjesno obrisan.');
        preusmjeri('korisnici.php');
    }
}

// Dohvati korisnike
$filter = $_GET['filter'] ?? 'svi';
if ($filter === 'klijenti') {
    $korisnici = dohvatiSveKorisnike('klijent');
} elseif ($filter === 'admini') {
    $korisnici = array_merge(
        dohvatiSveKorisnike('admin'),
        dohvatiSveKorisnike('glavni_admin')
    );
} else {
    $korisnici = dohvatiSveKorisnike();
}

$grupe = dohvatiSveGrupe();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Lista korisnika</h2>
        <div class="card-header-actions">
            <select onchange="window.location.href='korisnici.php?filter='+this.value" class="filter-select">
                <option value="svi" <?= $filter === 'svi' ? 'selected' : '' ?>>Svi korisnici</option>
                <option value="klijenti" <?= $filter === 'klijenti' ? 'selected' : '' ?>>Samo klijenti</option>
                <option value="admini" <?= $filter === 'admini' ? 'selected' : '' ?>>Samo admini</option>
            </select>
            <button class="btn btn-primary btn-small" onclick="otvoriModal('modalNoviKorisnik')">+ Novi korisnik</button>
        </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Ime i prezime</th>
                    <th>Korisnicko ime</th>
                    <th>Email</th>
                    <th>Uloga</th>
                    <th>Grupe</th>
                    <th>Status</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($korisnici as $korisnik): ?>
                    <?php $korisnikGrupe = dohvatiGrupeKorisnika($korisnik['id']); ?>
                    <tr>
                        <td><?= $korisnik['ime'] . ' ' . $korisnik['prezime'] ?></td>
                        <td><?= $korisnik['korisnicko_ime'] ?></td>
                        <td><?= $korisnik['email'] ?: '-' ?></td>
                        <td>
                            <?php if ($korisnik['uloga'] === 'glavni_admin'): ?>
                                <span class="badge badge-danger">Glavni Admin</span>
                            <?php elseif ($korisnik['uloga'] === 'admin'): ?>
                                <span class="badge badge-warning">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-info">Klijent</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($korisnikGrupe): ?>
                                <?php foreach ($korisnikGrupe as $g): ?>
                                    <span class="badge badge-success"><?= $g['naziv'] ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($korisnik['aktivan']): ?>
                                <span class="badge badge-success">Aktivan</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Neaktivan</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-secondary btn-small" onclick="urediKorisnika(<?= htmlspecialchars(json_encode($korisnik)) ?>, <?= htmlspecialchars(json_encode(array_column($korisnikGrupe, 'id'))) ?>)">Uredi</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards View -->
<div class="mobile-cards">
    <?php foreach ($korisnici as $korisnik): ?>
        <?php $korisnikGrupe = dohvatiGrupeKorisnika($korisnik['id']); ?>
        <div class="mobile-card">
            <div class="mobile-card-header">
                <div>
                    <strong><?= $korisnik['ime'] . ' ' . $korisnik['prezime'] ?></strong>
                    <div class="mobile-card-subtitle">@<?= $korisnik['korisnicko_ime'] ?></div>
                </div>
                <button class="btn btn-secondary btn-small" onclick="urediKorisnika(<?= htmlspecialchars(json_encode($korisnik)) ?>, <?= htmlspecialchars(json_encode(array_column($korisnikGrupe, 'id'))) ?>)">Uredi</button>
            </div>
            <div class="mobile-card-body">
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Email:</span>
                    <span><?= $korisnik['email'] ?: '-' ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Uloga:</span>
                    <span>
                        <?php if ($korisnik['uloga'] === 'glavni_admin'): ?>
                            <span class="badge badge-danger">Glavni Admin</span>
                        <?php elseif ($korisnik['uloga'] === 'admin'): ?>
                            <span class="badge badge-warning">Admin</span>
                        <?php else: ?>
                            <span class="badge badge-info">Klijent</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Status:</span>
                    <span>
                        <?php if ($korisnik['aktivan']): ?>
                            <span class="badge badge-success">Aktivan</span>
                        <?php else: ?>
                            <span class="badge badge-danger">Neaktivan</span>
                        <?php endif; ?>
                    </span>
                </div>
                <?php if ($korisnikGrupe): ?>
                <div class="mobile-card-row mobile-card-row-wrap">
                    <span class="mobile-card-label">Grupe:</span>
                    <span class="badges-container">
                        <?php foreach ($korisnikGrupe as $g): ?>
                            <span class="badge badge-success"><?= $g['naziv'] ?></span>
                        <?php endforeach; ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal: Novi korisnik -->
<div class="modal-overlay" id="modalNoviKorisnik">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Novi korisnik</h3>
            <button class="modal-close" onclick="zatvoriModal('modalNoviKorisnik')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="akcija" value="kreiraj">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-grupa">
                        <label>Ime *</label>
                        <input type="text" name="ime" required>
                    </div>
                    <div class="form-grupa">
                        <label>Prezime *</label>
                        <input type="text" name="prezime" required>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-grupa">
                        <label>Korisnicko ime *</label>
                        <input type="text" name="korisnicko_ime" required>
                    </div>
                    <div class="form-grupa">
                        <label>Lozinka *</label>
                        <input type="password" name="lozinka" required>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-grupa">
                        <label>Email</label>
                        <input type="email" name="email">
                    </div>
                    <div class="form-grupa">
                        <label>Telefon</label>
                        <input type="text" name="telefon">
                    </div>
                </div>
                
                <div class="form-grupa">
                    <label>Uloga *</label>
                    <select name="uloga" required>
                        <option value="klijent">Klijent</option>
                        <?php if (jeGlavniAdmin()): ?>
                            <option value="admin">Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="form-grupa">
                    <label>Grupe</label>
                    <div class="checkbox-group">
                        <?php foreach ($grupe as $grupa): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="grupe[]" value="<?= $grupa['id'] ?>">
                                <?= $grupa['naziv'] ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalNoviKorisnik')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Kreiraj korisnika</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Uredi korisnika -->
<div class="modal-overlay" id="modalUrediKorisnika">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Uredi korisnika</h3>
            <button class="modal-close" onclick="zatvoriModal('modalUrediKorisnika')">&times;</button>
        </div>
        <form method="POST" id="formaUrediKorisnika">
            <input type="hidden" name="akcija" value="azuriraj">
            <input type="hidden" name="korisnik_id" id="uredi_korisnik_id">
            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-grupa">
                        <label>Ime *</label>
                        <input type="text" name="ime" id="uredi_ime" required>
                    </div>
                    <div class="form-grupa">
                        <label>Prezime *</label>
                        <input type="text" name="prezime" id="uredi_prezime" required>
                    </div>
                </div>
                
                <div class="form-grupa">
                    <label>Nova lozinka (ostavite prazno ako ne mijenjate)</label>
                    <input type="password" name="lozinka">
                </div>
                
                <div class="form-grid">
                    <div class="form-grupa">
                        <label>Email</label>
                        <input type="email" name="email" id="uredi_email">
                    </div>
                    <div class="form-grupa">
                        <label>Telefon</label>
                        <input type="text" name="telefon" id="uredi_telefon">
                    </div>
                </div>
                
                <div class="form-grupa">
                    <label>Grupe</label>
                    <div class="checkbox-group" id="uredi_grupe">
                        <?php foreach ($grupe as $grupa): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="grupe[]" value="<?= $grupa['id'] ?>">
                                <?= $grupa['naziv'] ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-grupa">
                    <label class="checkbox-item">
                        <input type="checkbox" name="aktivan" id="uredi_aktivan" value="1">
                        Aktivan korisnik
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-small" onclick="obrisiKorisnika()">Obrisi</button>
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalUrediKorisnika')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>

<!-- Forma za brisanje -->
<form method="POST" id="formaBrisi" style="display: none;">
    <input type="hidden" name="akcija" value="obrisi">
    <input type="hidden" name="korisnik_id" id="brisi_korisnik_id">
</form>

<?php require_once 'includes/footer.php'; ?>
