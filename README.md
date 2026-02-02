# 🏋️ BlackBox Bihać – Fitness Club Web Application

**BlackBox Bihać** je moderna web aplikacija za fitness klub koja omogućava jednostavno upravljanje članovima, treninzima i terminima, kao i efikasnu komunikaciju između kluba i klijenata.

Aplikacija je podijeljena na:
- **Javnu web stranicu** (`index.html`)
- **Klijent panel**
- **Administrator panel**

Sve je dizajnirano s fokusom na jednostavno korisničko iskustvo i responzivan dizajn.

---

## 📁 Struktura projekta

```text
blacbox-bihac/
├── sql/
│   └── baza.sql                    # MySQL skripta za kreiranje baze
├── includes/
│   ├── konfiguracija.php           # Konfiguracijski podaci
│   ├── konekcija.php               # PDO konekcija na bazu
│   ├── funkcije.php                # Pomoćne funkcije
│   └── autentifikacija.php         # Sistem prijave i odjave
├── admin/
│   ├── includes/
│   │   ├── header.php              # Zajednički header (admin)
│   │   └── footer.php              # Zajednički footer (admin)
│   ├── index.php                   # Admin dashboard
│   ├── korisnici.php               # Upravljanje korisnicima
│   ├── grupe.php                   # Upravljanje grupama
│   ├── termini.php                 # Upravljanje terminima i treninzima
│   ├── clanarine.php               # Evidencija članarina
│   ├── izvjestaji.php              # Mjesečni izvještaji
│   └── statistika.php              # Statistika i zarada
├── klijent/
│   ├── includes/
│   │   ├── header.php              # Zajednički header (klijent)
│   │   └── footer.php              # Zajednički footer (klijent)
│   ├── index.php                   # Klijent dashboard
│   ├── rezervacije.php             # Rezervacija treninga
│   ├── historija.php               # Historija treninga
│   └── profil.php                  # Profil i članarine
├── prijava.php                     # Prijava korisnika
├── odjava.php                      # Odjava korisnika
├── css/
│   └── style.css                   # CSS stilovi
├── js/
│   └── script.js                   # JavaScript skripte
└── index.html                      # Javna početna stranica
```


---

## ⚙️ Tehnologije

### Frontend
- **HTML5**
- **CSS3**
- **JavaScript**

### Backend
- **PHP**

### Baza podataka
- **MySQL**
- **phpMyAdmin**

### Slanje emailova
- **FormSubmit**
  - Korišten za slanje email poruka bez potrebe za server-side mail konfiguracijom

---

## 👥 Funkcionalnosti

### 🧑‍💼 Admin panel
- Upravljanje korisnicima
- Upravljanje treninzima i terminima
- Evidencija članarina
- Pregled mjesečnih izvještaja
- Statistika i zarada kluba

### 🧑‍🤝‍🧑 Klijentski panel
- Pregled dostupnih treninga
- Rezervacija termina
- Pregled historije treninga
- Upravljanje ličnim profilom i članarinom

### 🌐 Javna stranica
- Osnovne informacije o klubu
- Pregled trening programa
- Kontakt forma (FormSubmit)

---

## 🎯 Cilj aplikacije

Cilj aplikacije **BlackBox Bihać** je:
- Digitalizacija poslovanja fitness kluba
- Olakšana komunikacija između kluba i članova
- Brzo i efikasno upravljanje terminima i članarinama
- Moderan i responzivan web prikaz

---

## 🚀 Pokretanje projekta

1. Importovati bazu iz `sql/baza.sql` u MySQL (phpMyAdmin)
2. Podesiti konekciju u `includes/konekcija.php`
3. Pokrenuti projekat putem lokalnog servera (XAMPP, WAMP, Laragon)
4. Otvoriti `index.html` za javni prikaz ili `prijava.php` za pristup panelima

---

## 📌 Napomena

Ova aplikacija je razvijena za **stvarno i aktivno korištenje u fitness klubu BlackBox Bihać**,  
s ciljem unapređenja poslovanja, digitalizacije procesa i olakšane komunikacije između kluba i članova.

---

## 📄 Autor

**BlackBox Bihać**  
Web aplikacija za fitness klub
