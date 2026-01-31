<?php
$naslovStranice = 'Grupe';
require_once 'includes/header.php';

// Obrada forme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    if ($akcija === 'kreiraj') {
        $podaci = [
            'naziv' => ocistiUnos($_POST['naziv']),
            'opis' => ocistiUnos($_POST['opis']),
            'kapacitet' => (int)$_POST['kapacitet']
        ];
        
        kreirajGrupu($podaci);
        prikaziPoruku('uspjeh', 'Grupa uspjesno kreirana.');
        preusmjeri('grupe.php');
    }
    
    if ($akcija === 'azuriraj') {
        global $konekcija;
        $grupaId = (int)$_POST['grupa_id'];
        
        $upit = $konekcija->prepare("UPDATE grupe SET naziv = ?, opis = ?, kapacitet = ?, aktivna = ? WHERE id = ?");
        $upit->execute([
            ocistiUnos($_POST['naziv']),
            ocistiUnos($_POST['opis']),
            (int)$_POST['kapacitet'],
            isset($_POST['aktivna']) ? 1 : 0,
            $grupaId
        ]);
        
        prikaziPoruku('uspjeh', 'Grupa uspjesno azurirana.');
        preusmjeri('grupe.php');
    }
}

$grupe = dohvatiSveGrupe();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Lista grupa</h2>
        <button class="btn btn-primary btn-small" onclick="otvoriModal('modalNovaGrupa')">+ Nova grupa</button>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Naziv</th>
                    <th>Opis</th>
                    <th>Kapacitet</th>
                    <th>Broj clanova</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupe as $grupa): ?>
                    <?php $clanovi = dohvatiKorisnikeGrupe($grupa['id']); ?>
                    <tr>
                        <td><strong><?= $grupa['naziv'] ?></strong></td>
                        <td><?= $grupa['opis'] ?: '-' ?></td>
                        <td><?= $grupa['kapacitet'] ?></td>
                        <td><?= count($clanovi) ?></td>
                        <td>
                            <button class="btn btn-secondary btn-small" onclick="urediGrupu(<?= htmlspecialchars(json_encode($grupa)) ?>)">Uredi</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Mobile Cards View -->
<div class="mobile-cards">
    <?php foreach ($grupe as $grupa): ?>
        <?php $clanovi = dohvatiKorisnikeGrupe($grupa['id']); ?>
        <div class="mobile-card">
            <div class="mobile-card-header">
                <strong><?= $grupa['naziv'] ?></strong>
                <button class="btn btn-secondary btn-small" onclick="urediGrupu(<?= htmlspecialchars(json_encode($grupa)) ?>)">Uredi</button>
            </div>
            <div class="mobile-card-body">
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Opis:</span>
                    <span><?= $grupa['opis'] ?: '-' ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Kapacitet:</span>
                    <span><?= $grupa['kapacitet'] ?></span>
                </div>
                <div class="mobile-card-row">
                    <span class="mobile-card-label">Broj clanova:</span>
                    <span class="badge badge-info"><?= count($clanovi) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
    .mobile-cards {
        display: none;
    }
    
    .mobile-card {
        background: var(--zinc-800);
        border: 1px solid var(--zinc-700);
        margin-bottom: 1rem;
        padding: 1rem;
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--zinc-700);
    }
    
    .mobile-card-header strong {
        color: var(--white);
        font-size: 1rem;
    }
    
    .mobile-card-body {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .mobile-card-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--zinc-300);
        font-size: 0.875rem;
    }
    
    .mobile-card-label {
        color: var(--zinc-500);
    }
    
    @media (max-width: 600px) {
        .table-container {
            display: none;
        }
        
        .mobile-cards {
            display: block;
        }
    }
</style>

<!-- Modal: Nova grupa -->
<div class="modal-overlay" id="modalNovaGrupa">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Nova grupa</h3>
            <button class="modal-close" onclick="zatvoriModal('modalNovaGrupa')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="akcija" value="kreiraj">
            <div class="modal-body">
                <div class="form-grupa">
                    <label>Naziv grupe *</label>
                    <input type="text" name="naziv" required>
                </div>
                <div class="form-grupa">
                    <label>Opis</label>
                    <textarea name="opis" rows="3"></textarea>
                </div>
                <div class="form-grupa">
                    <label>Kapacitet</label>
                    <input type="number" name="kapacitet" value="20" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalNovaGrupa')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Kreiraj grupu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Uredi grupu -->
<div class="modal-overlay" id="modalUrediGrupu">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Uredi grupu</h3>
            <button class="modal-close" onclick="zatvoriModal('modalUrediGrupu')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="akcija" value="azuriraj">
            <input type="hidden" name="grupa_id" id="uredi_grupa_id">
            <div class="modal-body">
                <div class="form-grupa">
                    <label>Naziv grupe *</label>
                    <input type="text" name="naziv" id="uredi_naziv" required>
                </div>
                <div class="form-grupa">
                    <label>Opis</label>
                    <textarea name="opis" id="uredi_opis" rows="3"></textarea>
                </div>
                <div class="form-grupa">
                    <label>Kapacitet</label>
                    <input type="number" name="kapacitet" id="uredi_kapacitet" min="1">
                </div>
                <div class="form-grupa">
                    <label class="checkbox-item">
                        <input type="checkbox" name="aktivna" id="uredi_aktivna" value="1">
                        Aktivna grupa
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalUrediGrupu')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Sacuvaj</button>
            </div>
        </form>
    </div>
</div>

<script>
function urediGrupu(grupa) {
    document.getElementById('uredi_grupa_id').value = grupa.id;
    document.getElementById('uredi_naziv').value = grupa.naziv;
    document.getElementById('uredi_opis').value = grupa.opis || '';
    document.getElementById('uredi_kapacitet').value = grupa.kapacitet;
    document.getElementById('uredi_aktivna').checked = grupa.aktivna == 1;
    otvoriModal('modalUrediGrupu');
}
</script>

<?php require_once 'includes/footer.php'; ?>
