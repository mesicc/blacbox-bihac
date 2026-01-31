/* ------------------------------------------------------------------ */
/* ------------------------ ADMIN.JS -------------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Glavni JavaScript fajl za admin panel
 * Sadrzi sve funkcije koje se koriste u admin folderu
 */

/* ------------------------------------------------------------------ */
/* ------------------------ MODAL FUNKCIJE -------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Otvara modal po ID-u
 * @param {string} modalId - ID modala koji se otvara
 */
function otvoriModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Zatvara modal po ID-u
 * @param {string} modalId - ID modala koji se zatvara
 */
function zatvoriModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/* ------------------------------------------------------------------ */
/* ------------------------ SIDEBAR FUNKCIJE ------------------------ */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Otvara sidebar na mobilnim uredajima
 */
function openSidebar() {
    var sidebar = document.getElementById('adminSidebar');
    var sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebar) {
        sidebar.classList.add('active');
    }
    if (sidebarOverlay) {
        sidebarOverlay.classList.add('active');
    }
    document.body.style.overflow = 'hidden';
}

/**
 * Zatvara sidebar na mobilnim uredajima
 */
function closeSidebar() {
    var sidebar = document.getElementById('adminSidebar');
    var sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebar) {
        sidebar.classList.remove('active');
    }
    if (sidebarOverlay) {
        sidebarOverlay.classList.remove('active');
    }
    document.body.style.overflow = '';
}

/**
 * Toggle sidebar
 */
function toggleSidebar() {
    var sidebar = document.getElementById('adminSidebar');
    if (sidebar && sidebar.classList.contains('active')) {
        closeSidebar();
    } else {
        openSidebar();
    }
}

/* ------------------------------------------------------------------ */
/* ------------------------ KORISNICI.PHP --------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Otvara modal za uredjivanje korisnika sa popunjenim podacima
 * @param {Object} korisnik - Objekat sa podacima korisnika
 * @param {Array} grupe - Niz ID-eva grupa kojima korisnik pripada
 */
function urediKorisnika(korisnik, grupe) {
    const korisnikIdField = document.getElementById('uredi_korisnik_id');
    const imeField = document.getElementById('uredi_ime');
    const prezimeField = document.getElementById('uredi_prezime');
    const emailField = document.getElementById('uredi_email');
    const telefonField = document.getElementById('uredi_telefon');
    const aktivanField = document.getElementById('uredi_aktivan');
    
    if (korisnikIdField) korisnikIdField.value = korisnik.id;
    if (imeField) imeField.value = korisnik.ime;
    if (prezimeField) prezimeField.value = korisnik.prezime;
    if (emailField) emailField.value = korisnik.email || '';
    if (telefonField) telefonField.value = korisnik.telefon || '';
    if (aktivanField) aktivanField.checked = korisnik.aktivan == 1;
    
    // Resetuj sve checkboxe za grupe
    document.querySelectorAll('#uredi_grupe input[type="checkbox"]').forEach(cb => {
        cb.checked = grupe.includes(parseInt(cb.value));
    });
    
    otvoriModal('modalUrediKorisnika');
}

/**
 * Brise korisnika nakon potvrde
 * Koristi skrivenu formu za slanje POST zahtjeva
 */
function obrisiKorisnika() {
    if (confirm('Da li ste sigurni da zelite obrisati ovog korisnika?')) {
        const brisiKorisnikId = document.getElementById('brisi_korisnik_id');
        const urediKorisnikId = document.getElementById('uredi_korisnik_id');
        const formaBrisi = document.getElementById('formaBrisi');
        
        if (brisiKorisnikId && urediKorisnikId) {
            brisiKorisnikId.value = urediKorisnikId.value;
        }
        if (formaBrisi) {
            formaBrisi.submit();
        }
    }
}

/* ------------------------------------------------------------------ */
/* ------------------------ GRUPE.PHP ------------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Otvara modal za uredjivanje grupe sa popunjenim podacima
 * @param {Object} grupa - Objekat sa podacima grupe
 */
function urediGrupu(grupa) {
    const grupaIdField = document.getElementById('uredi_grupa_id');
    const nazivField = document.getElementById('uredi_naziv');
    const opisField = document.getElementById('uredi_opis');
    const kapacitetField = document.getElementById('uredi_kapacitet');
    const aktivnaField = document.getElementById('uredi_aktivna');
    
    if (grupaIdField) grupaIdField.value = grupa.id;
    if (nazivField) nazivField.value = grupa.naziv;
    if (opisField) opisField.value = grupa.opis || '';
    if (kapacitetField) kapacitetField.value = grupa.kapacitet;
    if (aktivnaField) aktivnaField.checked = grupa.aktivna == 1;
    
    otvoriModal('modalUrediGrupu');
}

/* ------------------------------------------------------------------ */
/* ------------------------ CLANARINE.PHP --------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Otvara modal za uredjivanje clanarine sa popunjenim podacima
 * @param {number} korisnikId - ID korisnika
 * @param {string} ime - Ime i prezime korisnika za prikaz
 * @param {Object|null} clanarina - Objekat sa podacima clanarine ili null
 */
function urediClanarinu(korisnikId, ime, clanarina) {
    const korisnikIdField = document.getElementById('clanarina_korisnik_id');
    const imeKlijentaField = document.getElementById('modal_ime_klijenta');
    const iznosField = document.getElementById('clanarina_iznos');
    const placenoField = document.getElementById('clanarina_placeno');
    const napomenaField = document.getElementById('clanarina_napomena');
    
    if (korisnikIdField) korisnikIdField.value = korisnikId;
    if (imeKlijentaField) imeKlijentaField.textContent = ime;
    
    if (clanarina) {
        if (iznosField) iznosField.value = clanarina.iznos;
        if (placenoField) placenoField.checked = clanarina.placeno == 1;
        if (napomenaField) napomenaField.value = clanarina.napomena || '';
    } else {
        if (iznosField) iznosField.value = '50.00';
        if (placenoField) placenoField.checked = false;
        if (napomenaField) napomenaField.value = '';
    }
    
    otvoriModal('modalClanarina');
}

/* ------------------------------------------------------------------ */
/* ------------------------ TERMINI.PHP ----------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Otvara modal za zakazivanje treninga
 * @param {number} terminId - ID termina
 * @param {string} info - Informacije o terminu za prikaz (dan i vrijeme)
 */
function zakaziTrening(terminId, info) {
    const terminIdField = document.getElementById('zakazi_termin_id');
    const terminInfoField = document.getElementById('termin_info');
    
    if (terminIdField) terminIdField.value = terminId;
    if (terminInfoField) terminInfoField.textContent = info;
    
    otvoriModal('modalZakaziTrening');
}

/* ------------------------------------------------------------------ */
/* ------------------------ INDEX.PHP ------------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

// Index.php ne sadrzi dodatne JS funkcije osim globalnih

/* ------------------------------------------------------------------ */
/* ------------------------ STATISTIKA.PHP -------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

// Statistika.php ne sadrzi dodatne JS funkcije osim globalnih

/* ------------------------------------------------------------------ */
/* ------------------------ IZVJESTAJI.PHP -------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

// Izvjestaji.php ne sadrzi dodatne JS funkcije osim globalnih

/* ------------------------------------------------------------------ */
/* ------------------------ UTILITY FUNKCIJE ------------------------ */
/* -------------------------------------------Kemal Mesic------------ */

/**
 * Formatira broj kao valutu (KM)
 * @param {number} broj - Broj za formatiranje
 * @returns {string} Formatirani string sa valutom
 */
function formatirajValutu(broj) {
    return new Intl.NumberFormat('bs-BA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(broj) + ' KM';
}

/**
 * Prikazuje toast notifikaciju
 * @param {string} poruka - Tekst poruke
 * @param {string} tip - Tip poruke ('success' ili 'error')
 */
function prikaziNotifikaciju(poruka, tip = 'success') {
    // Kreiraj toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${tip}`;
    toast.textContent = poruka;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${tip === 'success' ? 'rgba(22, 163, 74, 0.9)' : 'rgba(220, 38, 38, 0.9)'};
        color: white;
        border-radius: 4px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    // Ukloni nakon 3 sekunde
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Debounce funkcija za optimizaciju
 * @param {Function} func - Funkcija za debounce
 * @param {number} wait - Vrijeme cekanja u ms
 * @returns {Function} Debounced funkcija
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/* ------------------------------------------------------------------ */
/* ------------------------ INICIJALIZACIJA ------------------------- */
/* -------------------------------------------Kemal Mesic------------ */

// DOMContentLoaded event za dodatnu inicijalizaciju
document.addEventListener('DOMContentLoaded', function() {
    // Dodaj CSS animacije za toast
    var style = document.createElement('style');
    style.textContent = '@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }';
    document.head.appendChild(style);
    
    // Menu toggle listener
    var menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleSidebar);
    }
    
    // Sidebar overlay listener
    var sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Close sidebar when clicking a nav link on mobile
    var navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeSidebar();
            }
        });
    });
    
    // Close sidebar on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeSidebar();
        }
    });
    
    // Close modal on overlay click
    var modalOverlays = document.querySelectorAll('.modal-overlay');
    modalOverlays.forEach(function(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            var activeModals = document.querySelectorAll('.modal-overlay.active');
            activeModals.forEach(function(modal) {
                modal.classList.remove('active');
            });
            document.body.style.overflow = '';
        }
    });
    
    // Auto-hide alert messages after 5 seconds
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s ease';
            setTimeout(function() { 
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
});
