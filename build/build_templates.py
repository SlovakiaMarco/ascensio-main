"""Generuje Elementor sablony stranok pre temu Ascensio.

Vystupy:
  ascensio-theme/templates/pages/*.json   – data pre importer v teme (s {{placeholdermi}})
  ascensio-theme/templates/sample-jobs.json – ukazkove pracovne ponuky
  elementor-sablony/*.json                – rucny import cez Elementor > Sablony > Importovat

Placeholdery, ktore importer nahradi:
  {{page:slug}}  odkaz na stranku    {{img}}  priecinok s obrazkami temy
  {{phone}} {{tel}} {{email}}        {{home}} adresa webu
"""
import json
import os
import re

from el import (accordion, apply_form, button, col, con, counter, eyebrow, gmap, grid, heading,
                icon_box, icon_list, image, jobs, reset_ids, row, section, text)

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
THEME = os.path.join(ROOT, "ascensio-theme")
OUT_PAGES = os.path.join(THEME, "templates", "pages")
OUT_MANUAL = os.path.join(ROOT, "elementor-sablony")
os.makedirs(OUT_PAGES, exist_ok=True)
os.makedirs(OUT_MANUAL, exist_ok=True)

P = lambda slug: "{{page:" + slug + "}}"
JOBS = P("pracovne-ponuky")
APPLY = P("prihlaska")
TEL = "{{tel}}"
PHONE = "{{phone}}"
EMAIL = "{{email}}"
IMG = "{{img}}"

TRADES = [
    ("monter", "Montér", "tools", "Montáž konštrukcií a zariadení podľa technických výkresov."),
    ("elektrikar", "Elektrikár", "bolt", "Elektroinštalácie v obytných aj komerčných objektoch."),
    ("vodar", "Vodár – inštalatér", "faucet", "Vodovodné, kanalizačné a sanitárne rozvody."),
    ("zvarac", "Zvárač", "fire", "Zváranie kovových konštrukcií a komponentov."),
    ("sadrokartonista", "Sadrokartonista", "border-all", "Steny, priečky a podhľady zo sadrokartónu."),
    ("betonar", "Betonár", "cubes", "Debnenie, betonáž a výroba prefabrikátov."),
    ("maliar", "Maliar", "paint-roller", "Maľovanie, nátery, stierky a tapetovanie."),
]


def stats(lang="sk"):
    L = {
        "sk": ("rok založenia", "zamestnancov", "dokončených projektov", "odpracovaných hodín ročne", " "),
        "de": ("Gründungsjahr", "Mitarbeiter", "abgeschlossene Projekte", "Arbeitsstunden pro Jahr", "."),
        "fr": ("Année de création", "collaborateurs", "projets réalisés", "heures travaillées par an", " "),
    }[lang]
    return grid([
        counter(1998, L[0], sep=None),
        counter(450, L[1], suffix="+", sep=L[4]),
        counter(350, L[2], suffix="+", sep=L[4]),
        counter(650000, L[3], suffix="+", sep=L[4]),
    ], cols=4, cols_t=2, cols_m=2, gap=24, cls="asc-stats__grid")


def page_hero(eb, title, lead, extra=None, cls=""):
    kids = [eyebrow(eb, "asc-eyebrow--light"), heading(title, "h1", "asc-pagehero__title"),
            text(f"<p>{lead}</p>", "asc-lead asc-lead--light")]
    if extra:
        kids += extra
    return section([con(kids, gap=18, cls="asc-pagehero__inner")],
                   cls=("asc-s--navy asc-s--pattern asc-pagehero " + cls).strip(), bg="navy",
                   pad=(88, 88), pad_m=(56, 56))


# ============================================================ DOMOV

def page_home():
    hero = section([
        row([
            col([
                eyebrow("Hľadáme remeselníkov do Nemecka a Francúzska", "asc-eyebrow--chip"),
                heading('Pracuj v zahraničí.<br><span class="asc-hl">O ubytovanie, auto aj náradie</span> '
                        'sa postaráme my.', "h1", "asc-hero__title"),
                text("<p>Hľadáme montérov, elektrikárov, zváračov a ďalších remeselníkov na stavebné "
                     "projekty v západnej Európe. Férové podmienky, stabilná práca a firma "
                     "s tradíciou od roku 1998.</p>", "asc-lead asc-lead--light"),
                con([
                    button("Pozrieť pracovné ponuky", JOBS, "gold", icon="arrow-right"),
                    button("Zavolať " + PHONE, TEL, "outline-light", icon="phone-alt"),
                ], direction="row", gap=12, wrap=True, cls="asc-btnrow"),
                icon_list(["Na trhu od roku 1998", "450+ zamestnancov", "Ubytovanie zabezpečené"],
                          inline=True, cls="asc-checks asc-checks--light"),
            ], width=56, gap=24, cls="asc-hero__text"),
            col([
                apply_form("quick", "Rýchla prihláška",
                           "Nechaj nám kontakt – zavoláme ti a všetko vysvetlíme. Nezáväzne.",
                           "Zavolajte mi", cls="asc-formcard"),
            ], width=44, cls="asc-hero__form"),
        ], gap=56),
    ], cls="asc-s--navy asc-s--pattern asc-hero", bg="navy", pad=(88, 104), pad_m=(48, 56),
        anchor="uvod")

    stats_s = section([stats()], cls="asc-stats", bg="white", pad=(48, 48), pad_m=(36, 36))

    trade_cards = [icon_box(ic, name, desc, f"{JOBS}?profesia={slug}", "asc-iconcard asc-trade")
                   for slug, name, ic, desc in TRADES]
    trade_cards.append(icon_box("hard-hat", "Iná profesia?",
                                "Pošli prihlášku – ozveme sa, keď budeme mať vhodnú ponuku.",
                                APPLY, "asc-iconcard asc-trade asc-trade--alt"))
    trades = section([
        row([
            col([eyebrow("Pracovné pozície"), heading("Koho hľadáme"),
                 text("<p>Vyber si svoju profesiu a pozri si aktuálne ponuky. Skúsenosti sú "
                      "výhodou, no nie vždy podmienkou.</p>", "asc-lead")], width=70, gap=14),
            col([button("Všetky ponuky", JOBS, "outline", icon="arrow-right", align="right",
                        cls="asc-align-end")], width=30, cls="asc-col-end"),
        ], align="flex-end", gap=24),
        grid(trade_cards, cols=4, cols_t=2, cols_m=2, gap=20),
    ], bg="white", anchor="pozicie", pad=(88, 96))

    benefits = section([
        con([eyebrow("Čo ti zabezpečíme", align="center"),
             heading("U nás pracuješ bez starostí", align="center"),
             text("<p>Sústreď sa na svoju prácu – bývanie, dopravu aj vybavenie zabezpečíme my.</p>",
                  "asc-lead", align="center")], gap=14, cls="asc-head-center"),
        grid([
            icon_box("bed", "Ubytovanie", "Zabezpečíme ubytovanie čo najbližšie k miestu projektu."),
            icon_box("shuttle-van", "Firemné auto",
                     "Na projekt aj z ubytovania sa prepravuješ firemným vozidlom."),
            icon_box("toolbox", "Profesionálne náradie",
                     "Pracuješ s kvalitným náradím značiek Milwaukee a Hilti."),
            icon_box("tshirt", "Pracovné oblečenie",
                     "Dostaneš pracovné oblečenie, aby si bol na stavbe dobre vybavený."),
            icon_box("euro-sign", "Férové podmienky",
                     "Mzdu a podmienky si jasne dohodneme ešte pred odchodom."),
            icon_box("file-signature", "Legálne zamestnanie",
                     "Pracuješ na zmluvu a so všetkými potrebnými dokladmi."),
            icon_box("hands-helping", "Podpora počas projektu",
                     "Keď niečo potrebuješ, sme na telefóne. Nie si na to sám."),
            icon_box("calendar-check", "Dlhodobá práca",
                     "350+ dokončených projektov – práca nekončí po jednej zákazke."),
        ], cols=4, cols_t=2, cols_m=1, gap=20, cls="asc-benefits"),
    ], cls="asc-s--sand", bg="sand", anchor="benefity")

    def step(n, title, desc):
        return con([heading(n, "div", "asc-step__num"), heading(title, "h3"),
                    text(f"<p>{desc}</p>")], gap=8, cls="asc-step")

    process = section([
        row([
            col([eyebrow("Ako to funguje"), heading("Od prihlášky po prvý deň na stavbe"),
                 text("<p>Celý proces je jednoduchý a rýchly. Všetko ti povieme vopred – mzdu, "
                      "miesto, ubytovanie aj termín nástupu.</p>", "asc-lead"),
                 con([button("Poslať prihlášku", "#prihlaska", "gold"),
                      button("Zavolať", TEL, "outline", icon="phone-alt")],
                     direction="row", gap=12, wrap=True, cls="asc-btnrow")],
                width=40, gap=16),
            col([grid([
                step("01", "Pošli prihlášku alebo zavolaj",
                     "Vyplnenie formulára trvá dve minúty. Môžeš nám aj rovno zavolať."),
                step("02", "Krátky telefonický pohovor",
                     "Prejdeme tvoje skúsenosti, predstavu o mzde a možný termín nástupu."),
                step("03", "Všetko pripravíme",
                     "Dohodneme podmienky, pripravíme zmluvu, ubytovanie aj dopravu."),
                step("04", "Nastúpiš na projekt",
                     "Na mieste ťa čaká tím, náradie aj pracovné oblečenie."),
            ], cols=2, cols_t=2, cols_m=1, gap=20, cls="asc-steps")], width=60),
        ], gap=56),
    ], bg="white", anchor="postup")

    def country(flag, name):
        return con([image(f"{IMG}/vlajka-{flag}.svg", name, "asc-flag"), heading(name, "h3")],
                   direction="row", gap=14, align="center", cls="asc-country")

    where = section([
        row([
            col([image(f"{IMG}/ilustracia-stavba.svg", "Stavebný projekt Ascensio",
                       "asc-img asc-img--frame")], width=50),
            col([eyebrow("Kde pracujeme", "asc-eyebrow--light"),
                 heading("Stavebné projekty v západnej Európe"),
                 text("<p>Naši ľudia pracujú najmä na projektoch v Nemecku a vo Francúzsku. Ročne spolu "
                      "odpracujeme viac ako 650&nbsp;000 hodín na stavbách, kde sa dbá na kvalitu "
                      "a bezpečnosť.</p>", "asc-lead asc-lead--light"),
                 con([country("de", "Nemecko"), country("fr", "Francúzsko")],
                     direction="row", gap=12, wrap=True, cls="asc-countries"),
                 button("Pozrieť ponuky", JOBS, "gold", icon="arrow-right")],
                width=50, gap=18),
        ], gap=64),
    ], cls="asc-s--navy", bg="navy", anchor="kde-pracujeme")

    def quote(q, name, role):
        return con([text(f"<p>„{q}“</p>", "asc-quote__text"),
                    heading(name, "div", "asc-quote__name"),
                    heading(role, "div", "asc-quote__role")], gap=6, cls="asc-quote")

    refs = section([
        con([eyebrow("Skúsenosti zamestnancov", align="center"),
             heading("Čo hovoria naši ľudia", align="center")], gap=14, cls="asc-head-center"),
        grid([
            quote("Sem vložte krátku skúsenosť zamestnanca – ako prebehol nástup a prvé dni na projekte.",
                  "Meno P.", "Montér · Nemecko · ukážka"),
            quote("Sem vložte skúsenosť s ubytovaním, dopravou firemným autom a vybavením na stavbe.",
                  "Meno P.", "Elektrikár · Francúzsko · ukážka"),
            quote("Sem vložte skúsenosť človeka, ktorý s Ascensio pracuje dlhodobo, a prečo zostáva.",
                  "Meno P.", "Zvárač · Nemecko · ukážka"),
        ], cols=3, cols_t=1, cols_m=1, gap=20),
        con([text("<p>Pozri si, ako to u nás vyzerá:</p>", "asc-social__label"),
             button("TikTok", "https://www.tiktok.com/@ascensio_angels", "outline", "sm",
                    icon="tiktok"),
             button("Instagram", "https://www.instagram.com/ascensio_holding/", "outline", "sm",
                    icon="instagram"),
             button("Facebook", "https://www.facebook.com/ascensioholding7", "outline", "sm",
                    icon="facebook-f")],
            direction="row", gap=10, wrap=True, align="center", justify="center", cls="asc-social"),
    ], cls="asc-s--sand", bg="sand", anchor="referencie")
    # ikony socialnych sieti su v kniznici fa-brands
    for b in refs["elements"][2]["elements"][1:]:
        b["settings"]["selected_icon"]["library"] = "fa-brands"
        b["settings"]["selected_icon"]["value"] = b["settings"]["selected_icon"]["value"].replace("fas ", "fab ")

    faq = section([
        row([
            col([eyebrow("Časté otázky"), heading("Máš otázky? Tu sú odpovede."),
                 text("<p>Nenašiel si odpoveď? Zavolaj nám alebo napíš – radi poradíme.</p>", "asc-lead"),
                 icon_list([(PHONE, TEL, "phone-alt"), (EMAIL, "mailto:" + EMAIL, "envelope")],
                           cls="asc-contactlist")],
                width=38, gap=16, align="flex-start"),
            col([accordion([
                ("Kto zabezpečuje ubytovanie a dopravu?",
                 "<p>Ubytovanie aj dopravu na projekt zabezpečujeme my – nemusíš nič hľadať ani riešiť. "
                 "Konkrétne podmienky ti vysvetlíme pri pohovore.</p>"),
                ("Potrebujem vedieť po nemecky alebo po francúzsky?",
                 "<p>Pri väčšine pozícií nie je znalosť jazyka podmienkou – pracuješ v tíme, kde sa "
                 "dorozumieš. Ak jazyk ovládaš, je to výhoda.</p>"),
                ("Musím mať prax v odbore?",
                 "<p>Prax je výhodou a ovplyvňuje mzdu. Pri niektorých pozíciách radi privítame aj "
                 "šikovných ľudí s menšou praxou.</p>"),
                ("Ako často chodím domov?",
                 "<p>Závisí to od projektu. Systém turnusov a cestovania domov ti presne vysvetlíme ešte "
                 "pred nástupom.</p>"),
                ("Aké náradie a oblečenie dostanem?",
                 "<p>Profesionálne náradie (spolupracujeme so značkami Milwaukee a Hilti) a pracovné "
                 "oblečenie ti zabezpečí firma.</p>"),
                ("Ako rýchlo môžem nastúpiť?",
                 "<p>Po pohovore a dohode podmienok sa snažíme nástup zariadiť čo najskôr. Presný termín "
                 "závisí od konkrétneho projektu.</p>"),
            ])], width=62),
        ], gap=64, align="flex-start"),
    ], bg="white", anchor="otazky")

    cta = section([
        row([
            col([eyebrow("Prihláška", "asc-eyebrow--light"), heading("Začni pracovať s Ascensio"),
                 text("<p>Vyplň krátky formulár. Ozveme sa ti, prejdeme aktuálne ponuky a dohodneme "
                      "ďalší postup.</p>", "asc-lead asc-lead--light"),
                 icon_list(["Nezáväzne a bezplatne", "Ozveme sa ti telefonicky",
                            "Tvoje údaje chránime podľa GDPR"], cls="asc-checks asc-checks--light"),
                 con([text("<p>Radšej voláš?</p>", "asc-muted-light"),
                      button(PHONE, TEL, "outline-light", icon="phone-alt")], gap=8,
                     cls="asc-callbox")],
                width=44, gap=20, align="flex-start"),
            col([apply_form("full", "Prihláška do práce", "Polia označené * sú povinné.",
                            "Odoslať prihlášku", cls="asc-formcard")], width=56),
        ], gap=56, align="flex-start"),
    ], cls="asc-s--navy asc-s--pattern", bg="navy", anchor="prihlaska")

    b2b = section([
        row([
            col([eyebrow("Pre firmy a partnerov"),
                 heading("Hľadáte spoľahlivých remeselníkov pre svoj projekt?", "h2", "asc-h3"),
                 text("<p>Dodávame skúsených montérov, elektrikárov, zváračov a ďalších odborníkov pre "
                      "stavebné projekty v Nemecku a Francúzsku – vrátane dopravy, ubytovania "
                      "a náradia.</p>")], width=60, gap=12),
            col([con([button("Spolupráca pre firmy", P("pre-firmy"), "navy", icon="arrow-right"),
                      con([button("Deutsch", P("fur-unternehmen"), "outline", "sm"),
                           button("Français", P("pour-les-entreprises"), "outline", "sm")],
                          direction="row", gap=10, wrap=True)], gap=12, align="flex-start",
                     cls="asc-b2b__actions")], width=40, cls="asc-col-end"),
        ], gap=40),
    ], cls="asc-s--sand asc-b2b", bg="sand", pad=(64, 64), pad_m=(48, 48), anchor="pre-firmy")

    return [hero, stats_s, trades, benefits, process, where, refs, faq, cta, b2b]


# ============================================================ PRACOVNE PONUKY

def page_jobs():
    return [
        page_hero("Kariéra v zahraničí", "Pracovné ponuky",
                  "Aktuálne voľné pozície na stavebných projektoch v Nemecku a vo Francúzsku. "
                  "Ubytovanie, doprava a náradie sú súčasťou ponuky.",
                  [icon_list(["Ubytovanie", "Firemné auto", "Náradie Milwaukee a Hilti",
                              "Pracovné oblečenie"], inline=True, cls="asc-checks asc-checks--light")]),
        section([jobs()], bg="white", pad=(64, 96), pad_m=(40, 64), anchor="ponuky"),
        section([
            row([
                col([heading("Nenašiel si svoju profesiu?", "h2", "asc-h3"),
                     text("<p>Pošli nám všeobecnú prihlášku. Keď sa objaví vhodná ponuka, ozveme sa ti.</p>")],
                    width=60, gap=10),
                col([con([button("Poslať prihlášku", APPLY, "gold", icon="arrow-right"),
                          button("Zavolať", TEL, "outline", icon="phone-alt")],
                         direction="row", gap=12, wrap=True, justify="flex-end", cls="asc-btnrow")],
                    width=40, cls="asc-col-end"),
            ], gap=32),
        ], cls="asc-s--sand", bg="sand", pad=(56, 56), pad_m=(40, 40)),
    ]


# ============================================================ PRIHLASKA

def page_apply():
    def mini(n, t, d):
        return con([heading(n, "div", "asc-step__num asc-step__num--sm"),
                    con([heading(t, "h3", "asc-h4"), text(f"<p>{d}</p>")], gap=4)],
                   direction="row", gap=16, align="flex-start", cls="asc-ministep")

    return [
        page_hero("Prihláška", "Prihlás sa do 2 minút",
                  "Vyplň krátky formulár a my sa ti ozveme. Prihláška je nezáväzná a bezplatná."),
        section([
            row([
                col([heading("Čo bude nasledovať?", "h2", "asc-h3"),
                     mini("1", "Ozveme sa ti", "Zavoláme ti na číslo, ktoré nám necháš."),
                     mini("2", "Prejdeme ponuky", "Povieme ti, kde a za akých podmienok môžeš pracovať."),
                     mini("3", "Dohodneme nástup", "Pripravíme zmluvu, ubytovanie aj dopravu."),
                     con([heading("Čo ti zabezpečíme", "h3", "asc-h4"),
                          icon_list(["Ubytovanie blízko projektu", "Firemné auto",
                                     "Náradie Milwaukee a Hilti", "Pracovné oblečenie"])],
                         gap=12, cls="asc-card asc-card--plain"),
                     icon_list([("Radšej voláš? " + PHONE, TEL, "phone-alt")], cls="asc-contactlist")],
                    width=40, gap=20, align="flex-start"),
                col([apply_form("full", "Prihláška do práce", "Polia označené * sú povinné.",
                                "Odoslať prihlášku", cls="asc-formcard")], width=60),
            ], gap=56, align="flex-start"),
        ], cls="asc-s--sand", bg="sand", anchor="prihlaska", pad=(72, 96)),
    ]


# ============================================================ O NAS

def page_about():
    def value(icon, t, d):
        return icon_box(icon, t, d, cls="asc-iconcard asc-value")

    return [
        page_hero("O nás", "Od roku 1998 staviame na ľuďoch",
                  "Ascensio je slovenská firma zo Starej Ľubovne, ktorá zamestnáva remeselníkov na "
                  "stavebných projektoch v západnej Európe. Dnes máme tím viac ako 450 ľudí."),
        section([
            row([
                col([eyebrow("Kto sme"), heading("Stabilná práca v zahraničí s férovým prístupom"),
                     text("<p>Sme firma s dlhoročnou tradíciou, ktorá ponúka stabilné a dobre platené "
                          "pracovné miesta v zahraničí. Naši zamestnanci pracujú na projektoch v západnej "
                          "Európe a ročne spolu odpracujeme viac ako 650&nbsp;000 hodín.</p>"
                          "<p>Veríme, že spokojní zamestnanci sú základom úspechu. Preto poskytujeme "
                          "férové podmienky, profesionálny prístup a kompletný servis – ubytovanie, "
                          "kvalitné náradie, firemné auto aj pracovné oblečenie.</p>")],
                    width=52, gap=16),
                col([image(f"{IMG}/foto-tim.svg", "Tím Ascensio na projekte", "asc-img asc-img--frame")],
                    width=48),
            ], gap=64),
        ], bg="white"),
        section([stats()], cls="asc-s--sand asc-stats", bg="sand", pad=(56, 56), pad_m=(40, 40)),
        section([
            con([eyebrow("Naše hodnoty", align="center"), heading("Na čom nám záleží", align="center")],
                gap=14, cls="asc-head-center"),
            grid([
                value("balance-scale", "Férovosť", "Jasné podmienky dohodnuté vopred, bez prekvapení."),
                value("handshake", "Spoľahlivosť", "Čo sľúbime, to dodržíme – zamestnancom aj partnerom."),
                value("hands-helping", "Starostlivosť", "Postaráme sa o bývanie, dopravu aj vybavenie."),
                value("award", "Kvalita", "Skúsení remeselníci a profesionálne náradie na každej stavbe."),
            ], cols=4, cols_t=2, cols_m=1, gap=20),
        ], bg="white"),
        section([
            row([
                col([heading("Pridaj sa k nám"),
                     text("<p>Hľadáme ľudí, ktorí chcú poctivo pracovať a dobre zarobiť. O zvyšok sa "
                          "postaráme my.</p>", "asc-lead asc-lead--light"),
                     con([button("Pracovné ponuky", JOBS, "gold", icon="arrow-right"),
                          button("Poslať prihlášku", APPLY, "outline-light")],
                         direction="row", gap=12, wrap=True, cls="asc-btnrow")], width=55, gap=16),
                col([con([heading("Sídlo spoločnosti", "h3", "asc-h4"),
                          text("<p><strong>AS-SPOL, s.r.o.</strong><br>Továrenská 1471/7<br>"
                               "064 01 Stará Ľubovňa</p>"),
                          icon_list([(PHONE, TEL, "phone-alt"), (EMAIL, "mailto:" + EMAIL, "envelope")],
                                    cls="asc-contactlist")],
                         gap=12, cls="asc-card asc-card--glass")], width=45),
            ], gap=48),
        ], cls="asc-s--navy asc-s--pattern", bg="navy"),
    ]


# ============================================================ PRE FIRMY (SK / DE / FR)

B2B = {
    "sk": dict(
        eb="Pre firmy a partnerov",
        h1="Kvalifikovaní remeselníci pre vaše stavebné projekty",
        lead="Ascensio od roku 1998 dodáva skúsených montérov, elektrikárov, zváračov a ďalších odborníkov "
             "pre stavebné firmy v Nemecku a vo Francúzsku. Dopravu, ubytovanie aj náradie zabezpečíme my – "
             "vy sa sústredíte na projekt.",
        cta="Nezáväzný dopyt", call="Zavolať",
        prof_eb="Profesie", prof_h="Koho vám vieme dodať",
        prof=["Montáž", "Elektroinštalácie", "Vodoinštalácie", "Zváranie", "Sadrokartón",
              "Betonárske práce", "Maliarske práce", "Ďalšie profesie na požiadanie"],
        prof_d=["Konštrukcie, zariadenia a technológie", "Rozvody a systémy v budovách",
                "Vodovod, kanalizácia, sanita", "Kovové konštrukcie a komponenty",
                "Steny, priečky a podhľady", "Debnenie, betonáž, prefabrikáty",
                "Nátery, stierky a povrchové úpravy", "Napíšte nám, čo potrebujete"],
        prov_eb="Kompletný servis", prov_h="Čo pre vás zabezpečíme",
        prov_lead="Naši ľudia prichádzajú pripravení. Logistiku a vybavenie riešime sami, aby ste vy "
                  "nemuseli.",
        prov=[("users", "Tímy aj jednotlivci", "Dodáme jednotlivých remeselníkov alebo celé pracovné skupiny."),
              ("shuttle-van", "Vlastná doprava", "Pracovníci prichádzajú na stavbu firemnými vozidlami."),
              ("bed", "Ubytovanie", "Ubytovanie našich ľudí organizujeme sami."),
              ("toolbox", "Profesionálne náradie", "Milwaukee a Hilti – tím prichádza plne vybavený."),
              ("hard-hat", "Oblečenie a ochranné pomôcky", "Vybavenie v súlade s bezpečnostnými predpismi."),
              ("headset", "Jeden kontakt", "Plánovanie a komunikácia cez jednu kontaktnú osobu.")],
        proc_eb="Spolupráca", proc_h="Ako spolupracujeme",
        proc=[("Dopyt", "Pošlete nám profesie, počet ľudí, miesto a termín."),
              ("Ponuka", "Pripravíme návrh tímu a cenovú ponuku."),
              ("Nasadenie", "Tím príde na stavbu s vlastnou dopravou a náradím."),
              ("Starostlivosť", "Priebežne komunikujeme a riešime všetko potrebné.")],
        form_eb="Kontakt", form_h="Nezáväzný dopyt",
        form_lead="Napíšte nám, akých pracovníkov potrebujete. Ozveme sa vám s návrhom riešenia.",
        form_title="Dopyt na pracovníkov", form_btn="Odoslať dopyt",
        addr="Sídlo",
    ),
    "de": dict(
        eb="Für Unternehmen und Partner",
        h1="Qualifizierte Handwerker für Ihre Bauprojekte",
        lead="Seit 1998 stellt Ascensio erfahrene Monteure, Elektriker, Schweißer und weitere Fachkräfte "
             "für Bauunternehmen in Deutschland und Frankreich bereit. Anreise, Unterkunft und Werkzeug "
             "organisieren wir – Sie konzentrieren sich auf Ihr Projekt.",
        cta="Unverbindliche Anfrage", call="Anrufen",
        prof_eb="Gewerke", prof_h="Unsere Fachkräfte",
        prof=["Montage", "Elektroinstallation", "Sanitärinstallation", "Schweißen", "Trockenbau",
              "Betonarbeiten", "Malerarbeiten", "Weitere Gewerke auf Anfrage"],
        prof_d=["Konstruktionen, Anlagen und Technik", "Leitungen und Systeme in Gebäuden",
                "Wasser, Abwasser, Sanitär", "Stahlkonstruktionen und Bauteile",
                "Wände, Trennwände und Decken", "Schalung, Betonage, Fertigteile",
                "Anstriche, Spachtel- und Oberflächenarbeiten", "Sprechen Sie uns an"],
        prov_eb="Rundum-Service", prov_h="Was wir für Sie übernehmen",
        prov_lead="Unsere Mitarbeiter kommen einsatzbereit. Logistik und Ausrüstung organisieren wir "
                  "selbst – damit Sie es nicht müssen.",
        prov=[("users", "Teams und Einzelkräfte", "Einzelne Handwerker oder komplette Arbeitsgruppen."),
              ("shuttle-van", "Eigene Fahrzeuge", "Unsere Mitarbeiter reisen mit Firmenfahrzeugen an."),
              ("bed", "Unterkunft", "Die Unterbringung unserer Mitarbeiter organisieren wir selbst."),
              ("toolbox", "Profi-Werkzeug", "Milwaukee und Hilti – das Team kommt voll ausgerüstet."),
              ("hard-hat", "Arbeitskleidung und PSA", "Ausrüstung gemäß den Sicherheitsvorschriften."),
              ("headset", "Ein Ansprechpartner", "Planung und Kommunikation über eine feste Kontaktperson.")],
        proc_eb="Zusammenarbeit", proc_h="So arbeiten wir zusammen",
        proc=[("Anfrage", "Sie nennen uns Gewerke, Anzahl, Einsatzort und Zeitraum."),
              ("Angebot", "Wir erstellen einen Teamvorschlag und ein Angebot."),
              ("Einsatz", "Das Team reist mit eigenen Fahrzeugen und Werkzeug an."),
              ("Betreuung", "Wir bleiben im Austausch und kümmern uns um alles Weitere.")],
        form_eb="Kontakt", form_h="Unverbindliche Anfrage",
        form_lead="Teilen Sie uns mit, welche Fachkräfte Sie benötigen. Wir melden uns mit einem Vorschlag.",
        form_title="Anfrage Fachkräfte", form_btn="Anfrage senden",
        addr="Firmensitz",
    ),
    "fr": dict(
        eb="Pour les entreprises et partenaires",
        h1="Des artisans qualifiés pour vos chantiers",
        lead="Depuis 1998, Ascensio met à disposition des monteurs, électriciens, soudeurs et autres "
             "professionnels expérimentés pour les entreprises du bâtiment en Allemagne et en France. "
             "Nous gérons le transport, l’hébergement et l’outillage – vous vous concentrez sur votre projet.",
        cta="Demande sans engagement", call="Appeler",
        prof_eb="Métiers", prof_h="Nos corps de métier",
        prof=["Montage", "Électricité", "Plomberie", "Soudure", "Plâtrerie – plaques de plâtre",
              "Béton et coffrage", "Peinture", "Autres métiers sur demande"],
        prof_d=["Structures, équipements et installations", "Réseaux et systèmes dans les bâtiments",
                "Eau, évacuation, sanitaires", "Structures et composants métalliques",
                "Cloisons, doublages et plafonds", "Coffrage, bétonnage, préfabrication",
                "Peintures, enduits et finitions", "Contactez-nous"],
        prov_eb="Service complet", prov_h="Ce que nous prenons en charge",
        prov_lead="Nos équipes arrivent prêtes à travailler. Nous organisons la logistique et "
                  "l’équipement – pour que vous n’ayez pas à le faire.",
        prov=[("users", "Équipes ou renforts", "Des artisans individuels ou des équipes complètes."),
              ("shuttle-van", "Véhicules propres", "Nos collaborateurs arrivent en véhicules de société."),
              ("bed", "Hébergement", "Nous organisons nous-mêmes l’hébergement de nos équipes."),
              ("toolbox", "Outillage professionnel", "Milwaukee et Hilti – l’équipe arrive entièrement équipée."),
              ("hard-hat", "Tenues et EPI", "Équipement conforme aux règles de sécurité."),
              ("headset", "Un interlocuteur unique", "Planification et communication via un seul contact.")],
        proc_eb="Collaboration", proc_h="Comment nous travaillons",
        proc=[("Demande", "Indiquez-nous les métiers, l’effectif, le lieu et la période."),
              ("Proposition", "Nous préparons une proposition d’équipe et un devis."),
              ("Mise en place", "L’équipe arrive avec ses véhicules et son outillage."),
              ("Suivi", "Nous restons en contact et gérons tout le nécessaire.")],
        form_eb="Contact", form_h="Demande sans engagement",
        form_lead="Dites-nous de quels professionnels vous avez besoin. Nous revenons vers vous avec une "
                  "proposition.",
        form_title="Demande de personnel", form_btn="Envoyer la demande",
        addr="Siège",
    ),
}
PROF_ICONS = ["tools", "bolt", "faucet", "fire", "border-all", "cubes", "paint-roller", "plus"]


def page_b2b(lang):
    t = B2B[lang]

    def step(n, title, desc):
        return con([heading(n, "div", "asc-step__num"), heading(title, "h3"), text(f"<p>{desc}</p>")],
                   gap=8, cls="asc-step asc-step--dark")

    return [
        section([con([
            eyebrow(t["eb"], "asc-eyebrow--light"), heading(t["h1"], "h1", "asc-pagehero__title"),
            text(f"<p>{t['lead']}</p>", "asc-lead asc-lead--light"),
            con([button(t["cta"], "#dopyt", "gold", icon="arrow-right"),
                 button(t["call"] + " " + PHONE, TEL, "outline-light", icon="phone-alt")],
                direction="row", gap=12, wrap=True, cls="asc-btnrow"),
        ], gap=18, cls="asc-pagehero__inner asc-pagehero__inner--wide")],
            cls="asc-s--navy asc-s--pattern asc-pagehero asc-pagehero--b2b", bg="navy",
            pad=(96, 96), pad_m=(56, 56)),
        section([stats(lang)], cls="asc-stats", bg="white", pad=(48, 48), pad_m=(36, 36)),
        section([
            con([eyebrow(t["prof_eb"], align="center"), heading(t["prof_h"], align="center")],
                gap=14, cls="asc-head-center"),
            grid([icon_box(PROF_ICONS[i], t["prof"][i], t["prof_d"][i], cls="asc-iconcard asc-trade")
                  for i in range(8)], cols=4, cols_t=2, cols_m=1, gap=20),
        ], cls="asc-s--sand", bg="sand"),
        section([
            row([
                col([eyebrow(t["prov_eb"]), heading(t["prov_h"]),
                     text(f"<p>{t['prov_lead']}</p>", "asc-lead")], width=34, gap=14,
                    align="flex-start"),
                col([grid([icon_box(i, h, d, cls="asc-iconcard asc-benefit asc-benefit--row")
                           for i, h, d in t["prov"]], cols=2, cols_t=2, cols_m=1, gap=20)],
                    width=66),
            ], gap=56, align="flex-start"),
        ], bg="white"),
        section([
            con([eyebrow(t["proc_eb"], "asc-eyebrow--light", align="center"),
                 heading(t["proc_h"], align="center")], gap=14, cls="asc-head-center"),
            grid([step(f"0{i + 1}", a, b) for i, (a, b) in enumerate(t["proc"])],
                 cols=4, cols_t=2, cols_m=1, gap=20, cls="asc-steps"),
        ], cls="asc-s--navy", bg="navy"),
        section([
            row([
                col([eyebrow(t["form_eb"]), heading(t["form_h"]),
                     text(f"<p>{t['form_lead']}</p>", "asc-lead"),
                     icon_list([(PHONE, TEL, "phone-alt"), (EMAIL, "mailto:" + EMAIL, "envelope"),
                                ("AS-SPOL, s.r.o., Továrenská 1471/7, 064 01 Stará Ľubovňa, Slovakia",
                                 None, "map-marker-alt")], cls="asc-contactlist")],
                    width=40, gap=16, align="flex-start"),
                col([apply_form("partner", t["form_title"], "", t["form_btn"], lang,
                                cls="asc-formcard")], width=60),
            ], gap=56, align="flex-start"),
        ], cls="asc-s--sand", bg="sand", anchor="dopyt"),
    ]


# ============================================================ KONTAKT

def page_contact():
    return [
        page_hero("Kontakt", "Sme tu pre teba",
                  "Máš otázku k pracovnej ponuke alebo chceš rovno nastúpiť? Zavolaj, napíš alebo "
                  "vyplň formulár."),
        section([
            grid([
                icon_box("phone-alt", "Zavolaj nám", PHONE, TEL, "asc-iconcard asc-contactcard"),
                icon_box("envelope", "Napíš nám", EMAIL, "mailto:" + EMAIL, "asc-iconcard asc-contactcard"),
                icon_box("map-marker-alt", "Sídlo firmy",
                         "AS-SPOL, s.r.o., Továrenská 1471/7, 064 01 Stará Ľubovňa", None,
                         "asc-iconcard asc-contactcard"),
            ], cols=3, cols_t=1, cols_m=1, gap=20),
        ], bg="white", pad=(64, 64), pad_m=(40, 40)),
        section([
            row([
                col([apply_form("full", "Napíš nám", "Polia označené * sú povinné.", "Odoslať správu",
                                cls="asc-formcard")], width=55),
                col([gmap("Továrenská 1471/7, 064 01 Stará Ľubovňa"),
                     con([heading("Ste firma?", "h3", "asc-h4"),
                          text("<p>Pre partnerov a klientov máme samostatnú stránku s informáciami "
                               "o spolupráci.</p>"),
                          con([button("Pre firmy", P("pre-firmy"), "navy", "sm"),
                               button("Deutsch", P("fur-unternehmen"), "outline", "sm"),
                               button("Français", P("pour-les-entreprises"), "outline", "sm")],
                              direction="row", gap=8, wrap=True)],
                         gap=10, cls="asc-card asc-card--plain")], width=45, gap=20),
            ], gap=40, align="flex-start"),
        ], cls="asc-s--sand", bg="sand", anchor="formular"),
    ]


PRIVACY_HTML = """<p><em>Toto je vzorová šablóna. Pred zverejnením ju prosím nechajte skontrolovať a doplniť (napr. zodpovednou osobou alebo právnikom). Miesta na doplnenie sú označené [DOPLNIŤ].</em></p>
<h2>1. Prevádzkovateľ</h2>
<p>AS-SPOL, s.r.o., Továrenská 1471/7, 064 01 Stará Ľubovňa, IČO: [DOPLNIŤ], zapísaná v Obchodnom registri [DOPLNIŤ]. Kontakt: {{email}}, {{phone}}.</p>
<h2>2. Aké údaje spracúvame</h2>
<p>Pri odoslaní prihlášky do práce spracúvame údaje, ktoré nám poskytnete: meno a priezvisko, telefón, e-mail, požadovanú pozíciu, informácie o praxi, termín možného nástupu a vašu správu. Pri dopyte firmy spracúvame kontaktné údaje zástupcu firmy.</p>
<h2>3. Účel a právny základ</h2>
<ul><li>Výber zamestnancov a komunikácia s uchádzačmi – opatrenia pred uzavretím zmluvy na žiadosť dotknutej osoby (čl. 6 ods. 1 písm. b) GDPR) a súhlas (čl. 6 ods. 1 písm. a) GDPR).</li><li>Odpoveď na dopyt firmy – oprávnený záujem (čl. 6 ods. 1 písm. f) GDPR).</li></ul>
<h2>4. Ako dlho údaje uchovávame</h2>
<p>Údaje uchádzačov uchovávame počas výberového konania a následne najviac [DOPLNIŤ, napr. 12 mesiacov], ak nám na to dáte súhlas. Potom ich vymažeme.</p>
<h2>5. Príjemcovia</h2>
<p>Údaje neposkytujeme tretím stranám okrem poskytovateľov IT služieb (hosting, e-mail) a prípadne našich partnerov, u ktorých sa uchádzate o prácu – vždy len v nevyhnutnom rozsahu. [DOPLNIŤ]</p>
<h2>6. Vaše práva</h2>
<p>Máte právo na prístup k údajom, opravu, vymazanie, obmedzenie spracúvania, prenosnosť, odvolanie súhlasu a podanie sťažnosti na Úrad na ochranu osobných údajov SR. Kontaktujte nás na {{email}}.</p>
<h2>7. Cookies</h2>
<p>Informácie o súboroch cookies nájdete v nastaveniach cookies na tejto stránke. [DOPLNIŤ podľa použitého cookie pluginu]</p>"""


# ============================================================ VYSTUP

PAGES = [
    # key, title, slug, builder, seed, menu_order
    ("domov", "Domov", "domov", page_home),
    ("pracovne-ponuky", "Pracovné ponuky", "pracovne-ponuky", page_jobs),
    ("prihlaska", "Prihláška", "prihlaska", page_apply),
    ("o-nas", "O nás", "o-nas", page_about),
    ("pre-firmy", "Pre firmy", "pre-firmy", lambda: page_b2b("sk")),
    ("fur-unternehmen", "Für Unternehmen", "fur-unternehmen", lambda: page_b2b("de")),
    ("pour-les-entreprises", "Pour les entreprises", "pour-les-entreprises", lambda: page_b2b("fr")),
    ("kontakt", "Kontakt", "kontakt", page_contact),
]

SEO = {
    "domov": "Práca v zahraničí pre remeselníkov – Nemecko a Francúzsko. Ubytovanie, firemné auto a náradie zabezpečíme. Ascensio, od roku 1998.",
    "pracovne-ponuky": "Aktuálne pracovné ponuky v Nemecku a Francúzsku: montér, elektrikár, vodár, zvárač, sadrokartonista, betonár, maliar.",
    "prihlaska": "Nezáväzná prihláška do práce v zahraničí. Vyplnenie trvá 2 minúty, ozveme sa vám telefonicky.",
    "o-nas": "Ascensio – slovenská firma zo Starej Ľubovne, od roku 1998 zamestnáva remeselníkov na projektoch v západnej Európe.",
    "pre-firmy": "Kvalifikovaní remeselníci pre stavebné projekty v Nemecku a Francúzsku – vrátane dopravy, ubytovania a náradia.",
    "fur-unternehmen": "Qualifizierte Handwerker aus der Slowakei für Bauprojekte in Deutschland – inklusive Anreise, Unterkunft und Werkzeug.",
    "pour-les-entreprises": "Des artisans qualifiés de Slovaquie pour vos chantiers en France – transport, hébergement et outillage inclus.",
    "kontakt": "Kontakt na Ascensio: telefón, e-mail, sídlo v Starej Ľubovni a formulár pre uchádzačov aj firmy.",
}

SAMPLE_JOBS = [
    dict(title="Montér", trade="monter", text=(
        "Montáž a inštalácia rôznych konštrukcií a zariadení podľa technických výkresov. Manipulácia "
        "s materiálom, presná montáž a kontrola kvality vykonanej práce."),
         req=["Manuálna zručnosť a technické myslenie", "Schopnosť pracovať v tíme",
              "Skúsenosti v montáži sú výhodou"], top=True),
    dict(title="Elektrikár", trade="elektrikar", text=(
        "Montáž a opravy elektrických rozvodov a systémov v obytných aj komerčných priestoroch. "
        "Identifikácia a odstránenie porúch, zapojenie nových zariadení a dodržiavanie platných noriem."),
         req=["Technická odbornosť a precíznosť", "Znalosť bezpečnostných štandardov",
              "Prax v odbore je vítaná"], top=True),
    dict(title="Vodár – inštalatér", trade="vodar", text=(
        "Inštalácia, údržba a oprava vodovodných a kanalizačných systémov v rôznych typoch objektov. "
        "Práca s potrubím, armatúrami a sanitárnymi zariadeniami, diagnostika porúch."),
         req=["Technická a manuálna zručnosť", "Znalosť bezpečnostných predpisov",
              "Skúsenosti v odbore sú výhodou"], top=False),
    dict(title="Zvárač", trade="zvarac", text=(
        "Spájanie kovových komponentov rôznymi zváracími technikami, čítanie technických výkresov, "
        "príprava materiálov a zabezpečenie kvality zvarov."),
         req=["Technická zručnosť a presnosť", "Dodržiavanie bezpečnostných predpisov",
              "Skúsenosti v odbore sú výhodou"], top=True),
    dict(title="Sadrokartonista", trade="sadrokartonista", text=(
        "Inštalácia sadrokartónových stien, priečok a stropov podľa technických plánov. Rezanie, "
        "upevňovanie a povrchová úprava sadrokartónu vrátane tmelenia a brúsenia spojov."),
         req=["Manuálna zručnosť a presnosť", "Schopnosť pracovať samostatne",
              "Skúsenosti v odbore sú výhodou"], top=False),
    dict(title="Betonár", trade="betonar", text=(
        "Práca s debnením, výroba a oprava betónových prefabrikátov, liatie do foriem a dokončovacie práce."),
         req=["Fyzická zdatnosť a presnosť", "Schopnosť pracovať v tíme",
              "Skúsenosti v odbore sú výhodou"], top=False),
    dict(title="Maliar", trade="maliar", text=(
        "Aplikácia farieb, lakov a náterov na rôzne povrchy, špachtľovanie tenkovrstvých omietok, "
        "brúsenie a tapetovanie. Príprava povrchov a miešanie farieb."),
         req=["Precíznosť a manuálna zručnosť", "Zmysel pre estetický výsledok",
              "Skúsenosti v odbore sú výhodou"], top=False),
]
DEFAULT_OFFER = ["Ubytovanie zabezpečené firmou", "Firemné auto na dopravu na projekt",
                 "Profesionálne náradie Milwaukee a Hilti", "Pracovné oblečenie",
                 "Férové podmienky dohodnuté vopred"]


def manual_resolve(s):
    """Pre rucny import: placeholdery -> relativne URL (WordPress v koreni domeny)."""
    s = re.sub(r"\{\{page:([a-z0-9-]+)\}\}", lambda m: "/" if m.group(1) == "domov" else f"/{m.group(1)}/", s)
    return (s.replace("{{img}}", "/wp-content/themes/ascensio/assets/img")
             .replace("{{tel}}", "tel:+421917970708").replace("{{phone}}", "+421 917 970 708")
             .replace("{{email}}", "office@ascensio.sk").replace("{{home}}", ""))


def main():
    for i, (key, title, slug, builder) in enumerate(PAGES):
        reset_ids(1000 + i * 37)
        data = builder()
        payload = {"key": key, "title": title, "slug": slug, "seo": SEO.get(key, ""),
                   "order": i, "elementor": data}
        with open(os.path.join(OUT_PAGES, f"{key}.json"), "w", encoding="utf-8") as f:
            json.dump(payload, f, ensure_ascii=False, indent=1)
        manual = {"version": "0.4", "title": f"Ascensio – {title}", "type": "page",
                  "content": json.loads(manual_resolve(json.dumps(data, ensure_ascii=False))),
                  "page_settings": {"hide_title": "yes"}}
        with open(os.path.join(OUT_MANUAL, f"ascensio-{key}.json"), "w", encoding="utf-8") as f:
            json.dump(manual, f, ensure_ascii=False)

    privacy = {"key": "ochrana-osobnych-udajov", "title": "Ochrana osobných údajov",
               "slug": "ochrana-osobnych-udajov", "seo": "", "order": 99, "html": PRIVACY_HTML}
    with open(os.path.join(OUT_PAGES, "ochrana-osobnych-udajov.json"), "w", encoding="utf-8") as f:
        json.dump(privacy, f, ensure_ascii=False, indent=1)

    jobs_out = []
    for j in SAMPLE_JOBS:
        jobs_out.append({
            "title": j["title"], "trade": j["trade"], "countries": ["nemecko", "francuzsko"],
            "content": f"<p>{j['text']}</p>",
            "excerpt": j["text"].split(". ")[0].rstrip(".") + ".",
            "meta": {"lokalita": "Nemecko, Francúzsko", "mzda": "Podľa praxe – dohodou", "nastup": "Ihneď",
                     "uvazok": "Plný úväzok", "ubytovanie": "Zabezpečené",
                     "poziadavky": "\n".join(j["req"]), "ponukame": "\n".join(DEFAULT_OFFER),
                     "top": "1" if j["top"] else ""},
        })
    with open(os.path.join(THEME, "templates", "sample-jobs.json"), "w", encoding="utf-8") as f:
        json.dump(jobs_out, f, ensure_ascii=False, indent=1)

    terms = {"asc_trade": [{"slug": s, "name": n.split(" – ")[0] if " – " in n else n, "icon": ic}
                           for s, n, ic, _ in TRADES],
             "asc_country": [{"slug": "nemecko", "name": "Nemecko", "code": "DE"},
                             {"slug": "francuzsko", "name": "Francúzsko", "code": "FR"}]}
    terms["asc_trade"][2]["name"] = "Vodár – inštalatér"
    with open(os.path.join(THEME, "templates", "terms.json"), "w", encoding="utf-8") as f:
        json.dump(terms, f, ensure_ascii=False, indent=1)
    print("OK:", len(PAGES), "stranok")


if __name__ == "__main__":
    main()
