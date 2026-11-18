# Nový web Ascensio.sk – WordPress téma pre Elementor

## Čo je v priečinku

| Súbor / priečinok | Na čo slúži |
|---|---|
| `ascensio-theme.zip` | **Hotová téma na nahratie do WordPressu** (Vzhľad → Témy → Pridať → Nahrať). |
| `ascensio-theme/` | Zdrojové súbory témy (to isté ako ZIP, rozbalené). |
| `logo/` | Návrh zjednodušeného loga – SVG súbory a prezentácia `logo/index.html`. |
| `elementor-sablony/` | Stránky ako Elementor šablóny na ručný import (záloha / pre iný web). |
| `preview/` | Statický náhľad dizajnu (iba na prezeranie, nie je to WordPress). |
| `build/` | Skripty, ktorými sa generujú šablóny, náhľad a logo (pre vývojára). |

## Štruktúra webu (80 % uchádzači / 20 % firmy)

| Stránka | Pre koho | Obsah |
|---|---|---|
| **Domov** | uchádzači | rýchla prihláška hneď v úvode, profesie, čo zabezpečíme, ako to funguje, kde pracujeme, referencie, časté otázky, plná prihláška; na konci krátky pás pre firmy |
| **Pracovné ponuky** | uchádzači | ponuky s filtrom podľa profesie a krajiny (spravujú sa v administrácii, nie v Elementore) |
| **Detail ponuky** | uchádzači | popis, požiadavky, čo zabezpečíme, zhrnutie, formulár s predvyplnenou pozíciou |
| **Prihláška** | uchádzači | samostatná stránka s formulárom – vhodná ako cieľ reklám |
| **O nás** | obaja | história, čísla, hodnoty, sídlo |
| **Pre firmy** + **Für Unternehmen** + **Pour les entreprises** | firmy | profesie, čo zabezpečíme, spolupráca, dopytový formulár – po slovensky, nemecky a francúzsky |
| **Kontakt** | obaja | telefón, e-mail, sídlo, formulár, mapa |
| **Ochrana osobných údajov** | – | vzorová GDPR šablóna na doplnenie |

Na mobile je stále viditeľná spodná lišta **Zavolať / Chcem prácu**. Keď je na obrazovke formulár, lišta sa sama schová.

---

## Inštalácia (asi 10 minút)

1. **WordPress** – nainštalujte na hostingu (najlepšie najprv na testovaciu subdoménu, napr. `novy.ascensio.sk`).
2. **Elementor** – Pluginy → Pridať nový → „Elementor“ → Inštalovať → Aktivovať. Stačí bezplatná verzia.
3. **Téma** – Vzhľad → Témy → Pridať novú → Nahrať tému → `ascensio-theme.zip` → Aktivovať.
   Elementor môže hneď potom otvoriť svoj uvítací sprievodca („Getting Started“) – stačí ho preskočiť („Skip“).
4. **Nastavenie webu** – Vzhľad → **Nastavenie webu Ascensio** → nechajte zaškrtnuté všetko → **Spustiť nastavenie**.
   Vytvorí sa 9 stránok, menu, 7 ukážkových ponúk a v Elementore sa nastavia farby a písma značky.
5. Otvorte web a skontrolujte ho podľa zoznamu v časti [Pred spustením](#pred-spustením--čo-treba-doplniť-alebo-overiť).

Nastavenie môžete spustiť aj opakovane – nevytvorí duplicitné stránky a nikdy neprepíše stránky, ktoré nevytvorilo.

---

## Ako web upravovať (bez programovania)

### Texty, obrázky a sekcie stránok → Elementor
Stránky → vyberte stránku → **Upraviť s Elementorom**. Každý nadpis, text, tlačidlo, ikona aj obrázok sa upravuje kliknutím.

- **Sekciu skopírujete** pravým klikom → Duplikovať. Kópia bude vyzerať rovnako.
- **Polia „CSS triedy“** (napr. `asc-iconcard`, `asc-s--navy`) v záložke Rozšírené nemažte – priraďujú prvkom dizajn témy.
- **Farby celého webu**: v Elementore ikona menu → Nastavenia stránky → **Globálne farby**. Zmena „Navy (primárna)“ alebo „Zlatá (sekundárna)“ sa prejaví na celom webe vrátane hlavičky, pätičky a formulárov.
- Fotky z projektov nahraďte v obrázkových prvkoch (teraz sú tam ilustrácie).

### Pracovné ponuky → menu „Pracovné ponuky“
**Pracovné ponuky → Pridať ponuku**:
- **Názov** = pozícia (napr. „Zvárač TIG – Mníchov“)
- **Text** = popis práce
- **Detaily ponuky**: miesto, mzda, nástup, úväzok, ubytovanie, požiadavky (každá na nový riadok), čo ponúkame, platnosť, *Top ponuka* (zvýrazní sa a ide navrch)
- vpravo **Profesia** a **Krajina** (podľa nich funguje filter)

Ponuka sa sama zobrazí na stránke Pracovné ponuky a dostane vlastnú stránku s formulárom. Ponuky sú automaticky označené pre **Google pre pracovné ponuky** (štruktúrované dáta JobPosting).

> Tip: **uveďte konkrétnu mzdu** (napr. „od 15 € / hod.“). Ponuky so mzdou majú výrazne viac reakcií.

**Odkazy s filtrom** do reklám a príspevkov: `…/pracovne-ponuky/?profesia=zvarac` alebo `?krajina=nemecko`.

### Prihlášky → menu „Prihlášky“
Každý odoslaný formulár sa uloží do menu **Prihlášky** (meno, telefón, pozícia, prax…) a zároveň príde e-mailom.
- Stav prihlášky (Nová → Kontaktovaný → Pohovor → Prijatý / Zamietnutý) meníte v detaile.
- Počet nových prihlášok vidno pri položke menu.
- E-mail pre upozornenia: **Vzhľad → Prispôsobiť → Ascensio – nastavenia webu → Prihlášky a formuláre**.
- Odporúčame plugin **WP Mail SMTP**, aby e-maily nekončili v spame.

### Hlavička, pätička, kontakty
- **Položky menu**: Vzhľad → Menu (Hlavné menu, Horná lišta – pre firmy, Pätička, Právne odkazy).
- **Telefón, e-mail, adresa, text tlačidla, horná lišta, sociálne siete, WhatsApp, mobilná lišta**: Vzhľad → Prispôsobiť → **Ascensio – nastavenia webu**.
- **Logo**: Vzhľad → Prispôsobiť → Identita webu (bez nahratia sa použije nové logo z témy).
- Ak chcete hlavičku/pätičku skladať v Elementore: téma podporuje **Elementor Pro** (Theme Builder) aj bezplatný plugin **Elementor Header & Footer Builder**.

### Vlastné prvky v Elementore (kategória „Ascensio“)
- **Pracovné ponuky** – výpis ponúk; nastavíte počet, stĺpce, filter, iba TOP, iba vybrané profesie/krajiny.
- **Prihláška / dopyt** – formulár v troch variantoch: *rýchla prihláška*, *plná prihláška*, *dopyt firmy* (SK/DE/FR/EN). Môžete predvybrať pozíciu – vhodné pre landing page jednej pozície.

To isté funguje aj ako skratky: `[ascensio_ponuky]`, `[ascensio_prihlaska typ="quick"]`.

**Landing page pre kampaň** (napr. „Hľadáme zváračov“): Stránky → Pridať → Upraviť s Elementorom → v nastaveniach stránky šablóna *Elementor Canvas* (bez menu) → pridajte nadpis, benefity a widget Prihláška s predvybranou pozíciou.

### Meranie konverzií
Po odoslaní formulára sa do `dataLayer` pošle udalosť **`asc_form_submit`** (s typom formulára). V Google Tag Manager ju napojíte na GA4 alebo Meta Pixel (konverzie z reklám na Facebooku/TikToku).

---

## Pred spustením – čo treba doplniť alebo overiť

**Údaje, ktoré som prevzal zo súčasného webu a treba ich potvrdiť:**
- [ ] **Telefón**: súčasný web má dve rôzne čísla – v hlavičke `+421 918 970 711`, v pätičke `+421 917 970 708`. Téma používa **917 970 708**. Ak je správne iné, zmeňte ho v Prispôsobiť → Kontakty *a* v textoch stránok (tlačidlá „Zavolať“).
- [ ] **Čísla**: 1998, 450+ zamestnancov, 350+ projektov, 650 000 hodín ročne (súčasný web uvádza raz 400+, raz 450).
- [ ] **Ukážkové ponuky**: majú mzdu „Podľa praxe – dohodou“ a miesto „Nemecko, Francúzsko“ – doplňte skutočné údaje.

**Texty, ktoré som doplnil a treba ich odsúhlasiť** (na súčasnom webe nie sú):
- [ ] Benefity *Férové podmienky*, *Legálne zamestnanie*, *Podpora počas projektu*.
- [ ] Odpovede v **Častých otázkach** (jazyk, prax, cestovanie domov, nástup).
- [ ] Stránky pre firmy: *oblečenie a ochranné pomôcky*, *jeden kontakt*, *tímy aj jednotlivci*.
- [ ] Nemecké a francúzske texty – nechajte prečítať rodeného hovoriaceho alebo partnera.

**Obsah, ktorý treba nahradiť:**
- [ ] **Referencie zamestnancov** – sú to ukážky („Meno P. … ukážka“). Nahraďte skutočnými výrokmi (so súhlasom zamestnanca) alebo sekciu skryte.
- [ ] **Fotky** – ilustrácie stavby a tímu nahraďte skutočnými fotkami z projektov a tímu. Pre dôveru uchádzačov je to najdôležitejšia zmena.
- [ ] **Ochrana osobných údajov** – doplňte IČO, dobu uchovávania a nechajte skontrolovať.

**Technické:**
- [ ] Cookie lišta (napr. **Complianz** alebo **CookieYes**) – potrebná kvôli Google mape na stránke Kontakt a analytike.
- [ ] **WP Mail SMTP** a skúšobné odoslanie prihlášky (overiť, že príde e-mail a uloží sa do menu Prihlášky).
- [ ] SEO plugin (Rank Math / Yoast) – voliteľné, téma má základné meta popisy.
- [ ] Presmerovania zo starých adries Wixu (napr. `/pracovné-ponuky` → `/pracovne-ponuky/`) cez plugin **Redirection**.

---

## Logo

V priečinku `logo/` je návrh zjednodušeného loga (prezentácia: `logo/index.html`):
- rovnaký zaoblený charakter písma a farby → značka ostáva rozpoznateľná,
- 2,5× hrubší ťah → čitateľné aj na mobile a ako malá ikona,
- bez „holding“ (variant s ním je pre oficiálne dokumenty),
- zlatá šípka nad „i“ = *ascensio* (lat. vzostup) a zároveň strecha → stavebníctvo.

Pred tlačou ho dajte grafikovi previesť do krivkových súborov (AI/PDF) a doladiť medzery medzi písmenami.

---

## Technické informácie

- WordPress 6.3+, PHP 7.4+, Elementor 3.24+ (bezplatná verzia; používa flexbox a grid kontajnery). Otestované s **WordPress 7.1.4, Elementor 4.4.1, PHP 8.3**.
- Písma Archivo a Inter sa načítavajú z **Bunny Fonts** (náhrada Google Fonts bez prenosu IP adries do USA). Nastavenie webu preto v Elementore vypne načítavanie Google Fonts.
- Nastavenie webu prepne Elementor na **vkladanie CSS priamo do stránky** (Elementor → Nastavenia → Výkon → CSS Print Method: Internal Embedding). Web tak nezávisí od zápisu súborov do `uploads/elementor` – pri probléme s právami na hostingu by inak stratil vzhľad. Ak hosting funguje bez problémov, môžete prepnúť na „External File“ (súbory si prehliadač uloží do cache).
- Ak by po nejakej úprave chýbali farby alebo rozloženie: Elementor → Nástroje → **Regenerovať súbory a dáta**.
- Formuláre: ochrana proti spamu (skryté pole, časová pasca, limit pokusov z jednej IP), bez reCAPTCHA a bez cookies.

### Stav testovania
Téma bola nainštalovaná a otestovaná v lokálnom WordPresse (WordPress Playground) s Elementorom 4.4.1:
- aktivácia témy, Nastavenie webu (vrátane opakovaného spustenia – nevytvára duplikáty), všetkých 9 stránok + detail ponuky, 404 a vyhľadávanie bez PHP chýb,
- editor Elementoru: stránka sa otvorí, všetkých 93 prvkov domovskej stránky sa zobrazí, widgety „Pracovné ponuky“ a „Prihláška / dopyt“ sú v kategórii Ascensio, úprava textu sa uloží a prejaví na webe,
- rýchla aj plná prihláška a nemecký dopyt firmy (kontrola povinných polí, uloženie do menu Prihlášky, zmena stavu),
- úprava ponuky v administrácii (pole Mzda) sa prejaví na webe; filter ponúk vrátane odkazu `?profesia=zvarac`; mobilné zobrazenie bez presahu do strany.

Neotestované zostalo: **doručenie e-mailu** s prihláškou (testovacie prostredie e-maily neposiela – po spustení overte so SMTP pluginom) a správanie na konkrétnom hostingu.

### Pre vývojára
- `python build/build_templates.py` – vygeneruje Elementor dáta stránok z `build/build_templates.py` (texty sú tam).
- `python build/preview.py` – vygeneruje statický náhľad do `preview/`.
- `python build/make_logo.py`, `python build/make_images.py` – logo a ilustrácie.
- `python build/php_check.py` – základná kontrola PHP syntaxe (zátvorky, if/endif), nenahrádza `php -l`.
- `python build/css_specificity.py` – pridá `body ` pred selektory pre Elementor prvky (Elementor 4 načítava svoje CSS až po téme); spúšťať po úprave `main.css`.
- `python build/package.py` – zabalí tému do `ascensio-theme.zip`.
- Lokálny test: `npx @wp-playground/cli server --mount=./ascensio-theme:/wordpress/wp-content/themes/ascensio --blueprint=./build/playground-blueprint.json` (potom tému aktivovať vo Vzhľad → Témy).
