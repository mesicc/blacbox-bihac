<?php
$naslovStranice = 'Termini';
require_once 'includes/header.php';

// Obrada forme
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcija = $_POST['akcija'] ?? '';
    
    if ($akcija === 'kreiraj_termin') {
        $podaci = [
            'grupa_id' => (int)$_POST['grupa_id'],
            'dan_u_sedmici' => $_POST['dan_u_sedmici'],
            'vrijeme_pocetka' => $_POST['vrijeme_pocetka'],
            'vrijeme_zavrsetka' => $_POST['vrijeme_zavrsetka']
        ];
        
        kreirajTermin($podaci);
        prikaziPoruku('uspjeh', 'Termin uspjesno kreiran.');
        preusmjeri('termini.php');
    }
    
    if ($akcija === 'kreiraj_trening') {
        $terminId = (int)$_POST['termin_id'];
        $datum = $_POST['datum'];
        
        kreirajTrening($terminId, $datum);
        prikaziPoruku('uspjeh', 'Trening uspjesno zakazan.');
        preusmjeri('termini.php');
    }
    
    if ($akcija === 'azuriraj_prisustvo') {
        $rezervacijaId = (int)$_POST['rezervacija_id'];
        $status = $_POST['status'];
        
        azurirajStatusRezervacije($rezervacijaId, $status);
        prikaziPoruku('uspjeh', 'Prisustvo azurirano.');
        preusmjeri('termini.php?trening=' . $_POST['trening_id']);
    }
}

$grupe = dohvatiSveGrupe();
$odabranaGrupa = isset($_GET['grupa']) ? (int)$_GET['grupa'] : ($grupe[0]['id'] ?? 0);
$termini = $odabranaGrupa ? dohvatiTermineGrupe($odabranaGrupa) : [];

// Ako je odabran trening, prikazi prisustvo
$odabraniTrening = isset($_GET['trening']) ? (int)$_GET['trening'] : null;
$rezervacije = $odabraniTrening ? dohvatiRezervacijeTreninga($odabraniTrening) : [];

$daniUSedmici = ['ponedjeljak', 'utorak', 'srijeda', 'cetvrtak', 'petak', 'subota', 'nedjelja'];
?>

<div class="termini-grid">
    <!-- Termini po grupama -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Termini</h2>
            <button class="btn btn-primary btn-small" onclick="otvoriModal('modalNoviTermin')">+ Novi termin</button>
        </div>
        
        <div class="form-grupa" style="margin-bottom: 1rem;">
            <select onchange="window.location.href='termini.php?grupa='+this.value" style="width: 100%;">
                <?php foreach ($grupe as $grupa): ?>
                    <option value="<?= $grupa['id'] ?>" <?= $odabranaGrupa == $grupa['id'] ? 'selected' : '' ?>><?= $grupa['naziv'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <?php if ($termini): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Dan</th>
                            <th>Vrijeme</th>
                            <th>Akcije</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($termini as $termin): ?>
                            <tr>
                                <td><?= imeDana($termin['dan_u_sedmici']) ?></td>
                                <td><?= substr($termin['vrijeme_pocetka'], 0, 5) ?> - <?= substr($termin['vrijeme_zavrsetka'], 0, 5) ?></td>
                                <td>
                                    <button class="btn btn-secondary btn-small" onclick="zakaziTrening(<?= $termin['id'] ?>, '<?= imeDana($termin['dan_u_sedmici']) ?> <?= substr($termin['vrijeme_pocetka'], 0, 5) ?>')">Zakazi</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Termini Cards -->
            <div class="mobile-termini-list">
                <?php foreach ($termini as $termin): ?>
                    <div class="termin-item">
                        <div class="termin-info">
                            <strong><?= imeDana($termin['dan_u_sedmici']) ?></strong>
                            <span><?= substr($termin['vrijeme_pocetka'], 0, 5) ?> - <?= substr($termin['vrijeme_zavrsetka'], 0, 5) ?></span>
                        </div>
                        <button class="btn btn-secondary btn-small" onclick="zakaziTrening(<?= $termin['id'] ?>, '<?= imeDana($termin['dan_u_sedmici']) ?> <?= substr($termin['vrijeme_pocetka'], 0, 5) ?>')">Zakazi</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: var(--zinc-500); text-align: center; padding: 2rem;">Nema termina za ovu grupu.</p>
        <?php endif; ?>
    </div>
    
    <!-- Zakazani treninzi -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Zakazani treninzi</h2>
        </div>
        
        <?php
        $danas = date('Y-m-d');
        $treninzi = dohvatiTreningeZaDatum($danas);
        $sedmica = [];
        for ($i = 0; $i < 7; $i++) {
            $datum = date('Y-m-d', strtotime("+$i days"));
            $sedmica[$datum] = dohvatiTreningeZaDatum($datum);
        }
        ?>
        
        <div class="treninzi-lista">
            <?php foreach ($sedmica as $datum => $treninziDan): ?>
                <?php if ($treninziDan): ?>
                    <div class="trening-dan">
                        <p class="trening-datum">
                            <?= formatirajDatum($datum, 'l, d.m.Y') ?>
                            <?= $datum === $danas ? '<span class="badge badge-success">Danas</span>' : '' ?>
                        </p>
                        <?php foreach ($treninziDan as $trening): ?>
                            <div class="trening-item">
                                <div class="trening-info">
                                    <strong><?= $trening['grupa_naziv'] ?></strong>
                                    <span class="trening-vrijeme"><?= substr($trening['vrijeme_pocetka'], 0, 5) ?></span>
                                </div>
                                <a href="?trening=<?= $trening['id'] ?>" class="btn btn-secondary btn-small">Prisustvo</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php if ($odabraniTrening): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Evidencija prisustva</h2>
        <a href="termini.php" class="btn btn-secondary btn-small">Zatvori</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Klijent</th>
                    <th>Status</th>
                    <th>Akcije</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rezervacije as $rez): ?>
                    <tr>
                        <td><?= $rez['ime'] . ' ' . $rez['prezime'] ?></td>
                        <td>
                            <?php if ($rez['status'] === 'prisutan'): ?>
                                <span class="badge badge-success">Prisutan</span>
                            <?php elseif ($rez['status'] === 'odsutan'): ?>
                                <span class="badge badge-danger">Odsutan</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Rezervisano</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="prisustvo-forma">
                                <input type="hidden" name="akcija" value="azuriraj_prisustvo">
                                <input type="hidden" name="rezervacija_id" value="<?= $rez['id'] ?>">
                                <input type="hidden" name="trening_id" value="<?= $odabraniTrening ?>">
                                <button type="submit" name="status" value="prisutan" class="btn btn-primary btn-small">Prisutan</button>
                                <button type="submit" name="status" value="odsutan" class="btn btn-danger btn-small">Odsutan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rezervacije)): ?>
                    <tr><td colspan="3" style="text-align: center; color: var(--zinc-500);">Nema rezervacija za ovaj trening.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Mobile Prisustvo Cards -->
    <div class="mobile-prisustvo-list">
        <?php foreach ($rezervacije as $rez): ?>
            <div class="prisustvo-card">
                <div class="prisustvo-header">
                    <strong><?= $rez['ime'] . ' ' . $rez['prezime'] ?></strong>
                    <?php if ($rez['status'] === 'prisutan'): ?>
                        <span class="badge badge-success">Prisutan</span>
                    <?php elseif ($rez['status'] === 'odsutan'): ?>
                        <span class="badge badge-danger">Odsutan</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Rezervisano</span>
                    <?php endif; ?>
                </div>
                <form method="POST" class="prisustvo-forma-mobile">
                    <input type="hidden" name="akcija" value="azuriraj_prisustvo">
                    <input type="hidden" name="rezervacija_id" value="<?= $rez['id'] ?>">
                    <input type="hidden" name="trening_id" value="<?= $odabraniTrening ?>">
                    <button type="submit" name="status" value="prisutan" class="btn btn-primary btn-small">Prisutan</button>
                    <button type="submit" name="status" value="odsutan" class="btn btn-danger btn-small">Odsutan</button>
                </form>
            </div>
        <?php endforeach; ?>
        <?php if (empty($rezervacije)): ?>
            <p style="text-align: center; color: var(--zinc-500); padding: 2rem;">Nema rezervacija za ovaj trening.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Modal: Novi termin -->
<div class="modal-overlay" id="modalNoviTermin">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Novi termin</h3>
            <button class="modal-close" onclick="zatvoriModal('modalNoviTermin')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="akcija" value="kreiraj_termin">
            <div class="modal-body">
                <div class="form-grupa">
                    <label>Grupa *</label>
                    <select name="grupa_id" required>
                        <?php foreach ($grupe as $grupa): ?>
                            <option value="<?= $grupa['id'] ?>"><?= $grupa['naziv'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-grupa">
                    <label>Dan u sedmici *</label>
                    <select name="dan_u_sedmici" required>
                        <?php foreach ($daniUSedmici as $dan): ?>
                            <option value="<?= $dan ?>"><?= imeDana($dan) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-grid">
                    <div class="form-grupa">
                        <label>Pocetak *</label>
                        <input type="time" name="vrijeme_pocetka" required>
                    </div>
                    <div class="form-grupa">
                        <label>Zavrsetak *</label>
                        <input type="time" name="vrijeme_zavrsetka" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalNoviTermin')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Kreiraj</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Zakazi trening -->
<div class="modal-overlay" id="modalZakaziTrening">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Zakazi trening - <span id="termin_info"></span></h3>
            <button class="modal-close" onclick="zatvoriModal('modalZakaziTrening')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="akcija" value="kreiraj_trening">
            <input type="hidden" name="termin_id" id="zakazi_termin_id">
            <div class="modal-body">
                <div class="form-grupa">
                    <label>Datum *</label>
                    <input type="date" name="datum" min="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-small" onclick="zatvoriModal('modalZakaziTrening')">Odustani</button>
                <button type="submit" class="btn btn-primary btn-small">Zakazi</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
