korišten FormSumbit za slanje mailova





blacbox-bihac/
├── sql/
│   └── baza.sql                    # MySQL skripta za kreiranje baze
├── includes/
│   ├── konfiguracija.php           # Konfiguracijski podaci
│   ├── konekcija.php               # PDO konekcija na bazu
│   ├── funkcije.php                # Sve pomocne funkcije
│   └── autentifikacija.php         # Sistem prijave/odjave
├── admin/
│   ├── includes/
│   │   ├── header.php              # Zajednicki header za admin
│   │   └── footer.php              # Zajednicki footer
│   ├── index.php                   # Admin dashboard
│   ├── korisnici.php               # Upravljanje korisnicima
│   ├── grupe.php                   # Upravljanje grupama
│   ├── termini.php                 # Upravljanje terminima i treninzima
│   ├── clanarine.php               # Evidencija clanarina
│   ├── izvjestaji.php              # Mjesecni izvjestaji
│   └── statistika.php              # Statistika i zarada
├── klijent/
│   ├── includes/
│   │   ├── header.php              # Zajednicki header za klijenta
│   │   └── footer.php              # Zajednicki footer
│   ├── index.php                   # Klijent dashboard
│   ├── rezervacije.php             # Rezervacija treninga
│   ├── historija.php               # Historija treninga
│   └── profil.php                  # Profil i clanarine
├── prijava.php                     # Stranica za prijavu
├── odjava.php                      # Odjava korisnika
├── css/
│   └── style.css                   # Postojeci CSS
├── js/
│   └── script.js                   # Postojeci JS
└── index.html                      # Glavna web stranica