"""Staticky nahlad temy: Elementor JSON -> HTML so strukturou ako generuje Elementor.

Sluzi na kontrolu dizajnu bez WordPressu. Hlavicka, paticka a vlastne widgety
napodobnuju vystup PHP sablon temy.
"""
import html
import time
import json
import os
import re

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
THEME = os.path.join(ROOT, "ascensio-theme")
OUT = os.path.join(ROOT, "preview")
os.makedirs(OUT, exist_ok=True)

IMG = "../ascensio-theme/assets/img"
PHONE = "+421 917 970 708"
TEL = "tel:+421917970708"
EMAIL = "office@ascensio.sk"
HEX = {"primary": "#14263F", "secondary": "#C9A04E", "ascsand": "#F5F2EB", "ascnavy2": "#0C1A2E",
       "ascwhite": "#FFFFFF"}

esc = html.escape


def link_for(slug):
    return "index.html" if slug == "domov" else f"{slug}.html"


def resolve(s):
    s = re.sub(r"\{\{page:([a-z0-9-]+)\}\}", lambda m: link_for(m.group(1)), s)
    return (s.replace("{{img}}", IMG).replace("{{tel}}", TEL).replace("{{phone}}", PHONE)
             .replace("{{email}}", EMAIL).replace("{{home}}", "index.html"))


# ---------------------------------------------------------------- CSS emulacia

css_rules = []


def rule(sel, decl, media=None):
    css_rules.append((media, sel, decl))


def dimcss(d):
    u = d.get("unit", "px")
    return f'{d["top"]}{u} {d["right"]}{u} {d["bottom"]}{u} {d["left"]}{u}'


def con_css(el):
    s = el["settings"]
    sel = f".elementor-element-{el['id']}"
    boxed = s.get("content_width") == "boxed"
    inner = sel + " > .e-con-inner" if boxed else sel
    is_grid = s.get("container_type") == "grid"
    decl = []
    if "padding" in s:
        decl.append(f"padding:{dimcss(s['padding'])}")
    bg = s.get("background_color")
    g = s.get("__globals__", {}).get("background_color")
    if g:
        bg = HEX.get(g.split("id=")[-1], bg)
    if bg:
        decl.append(f"background-color:{bg}")
    if "width" in s:
        decl.append(f"width:{s['width']['size']}%")
    rule(sel, ";".join(decl))
    idecl = []
    if is_grid:
        idecl.append("display:grid")
        idecl.append(f"grid-template-columns:repeat({s['grid_columns_grid']['size']},minmax(0,1fr))")
        gp = s.get("grid_gaps", {}).get("size", 20)
        idecl.append(f"gap:{gp}px")
    else:
        idecl.append("display:flex")
        idecl.append(f"flex-direction:{s.get('flex_direction', 'column')}")
        if "flex_gap" in s:
            idecl.append(f"gap:{s['flex_gap']['size']}px")
        if s.get("flex_wrap"):
            idecl.append("flex-wrap:wrap")
        if s.get("flex_align_items"):
            idecl.append(f"align-items:{s['flex_align_items']}")
        if s.get("flex_justify_content"):
            idecl.append(f"justify-content:{s['flex_justify_content']}")
    rule(inner, ";".join(idecl))
    # tablet
    t = []
    if "padding_tablet" in s:
        t.append(f"padding:{dimcss(s['padding_tablet'])}")
    if "width_tablet" in s:
        t.append(f"width:{s['width_tablet']['size']}%")
    if t:
        rule(sel, ";".join(t), "(max-width:1024px)")
    ti = []
    if is_grid:
        ti.append(f"grid-template-columns:repeat({s['grid_columns_grid_tablet']['size']},minmax(0,1fr))")
    elif s.get("flex_direction_tablet"):
        ti.append(f"flex-direction:{s['flex_direction_tablet']}")
    if ti:
        rule(inner, ";".join(ti), "(max-width:1024px)")
    m = []
    if "padding_mobile" in s:
        m.append(f"padding:{dimcss(s['padding_mobile'])}")
    if "width_mobile" in s:
        m.append(f"width:{s['width_mobile']['size']}%")
    if m:
        rule(sel, ";".join(m), "(max-width:767px)")
    mi = []
    if is_grid:
        mi.append(f"grid-template-columns:repeat({s['grid_columns_grid_mobile']['size']},minmax(0,1fr))")
    elif s.get("flex_direction_mobile"):
        mi.append(f"flex-direction:{s['flex_direction_mobile']}")
    if s.get("flex_align_items_mobile"):
        mi.append(f"align-items:{s['flex_align_items_mobile']}")
    if mi:
        rule(inner, ";".join(mi), "(max-width:767px)")


# ---------------------------------------------------------------- widgety

def fa_icon(ic):
    if not ic or not ic.get("value"):
        return ""
    return f'<i aria-hidden="true" class="{esc(ic["value"])}"></i>'


def wrap_widget(el, inner, extra_cls=""):
    s = el["settings"]
    cls = f'elementor-element elementor-element-{el["id"]} elementor-widget elementor-widget-{el["widgetType"]} {extra_cls} {s.get("_css_classes", "")}'
    align = s.get("align")
    if align and el["widgetType"] in ("heading", "text-editor"):
        rule(f".elementor-element-{el['id']}", f"text-align:{align}")
    return f'<div class="{" ".join(cls.split())}" data-id="{el["id"]}"><div class="elementor-widget-container">{inner}</div></div>'


def a_open(link, cls=""):
    if link and link.get("url"):
        return f'<a href="{esc(link["url"])}"' + (f' class="{cls}"' if cls else "") + ">", "</a>"
    return (f'<span class="{cls}">' if cls else "<span>"), "</span>"


def w_heading(el):
    s = el["settings"]
    tag = s.get("header_size", "h2")
    title = s["title"]
    if s.get("link", {}).get("url"):
        title = f'<a href="{esc(s["link"]["url"])}">{title}</a>'
    return wrap_widget(el, f'<{tag} class="elementor-heading-title elementor-size-default">{title}</{tag}>')


def w_text(el):
    return wrap_widget(el, el["settings"]["editor"])


def w_button(el):
    s = el["settings"]
    icon = s.get("selected_icon")
    icon_html = f'<span class="elementor-button-icon">{fa_icon(icon)}</span>' if icon else ""
    al = s.get("align")
    if al:
        rule(f".elementor-element-{el['id']}", f"text-align:{al}")
    inner = (f'<div class="elementor-button-wrapper"><a class="elementor-button elementor-button-link elementor-size-{s.get("size", "sm")}" href="{esc(s["link"]["url"])}">'
             f'<span class="elementor-button-content-wrapper">{icon_html}<span class="elementor-button-text">{s["text"]}</span></span></a></div>')
    return wrap_widget(el, inner, f"elementor-align-{al}" if al else "")


def w_iconbox(el):
    s = el["settings"]
    link = s.get("link")
    ao, ac = a_open(link)
    io, ic = a_open(link, "elementor-icon")
    tag = s.get("title_size", "h3")
    inner = (f'<div class="elementor-icon-box-wrapper"><div class="elementor-icon-box-icon">{io}{fa_icon(s["selected_icon"])}{ic}</div>'
             f'<div class="elementor-icon-box-content"><{tag} class="elementor-icon-box-title">{ao}{s["title_text"]}{ac}</{tag}>'
             f'<p class="elementor-icon-box-description">{s["description_text"]}</p></div></div>')
    return wrap_widget(el, inner, "elementor-view-default elementor-position-top elementor-mobile-position-top")


def w_iconlist(el):
    s = el["settings"]
    inline = s.get("view") == "inline"
    items = []
    for it in s["icon_list"]:
        ao, ac = ("", "")
        if it.get("link", {}).get("url"):
            ao, ac = f'<a href="{esc(it["link"]["url"])}">', "</a>"
        items.append(f'<li class="elementor-icon-list-item{" elementor-inline-item" if inline else ""}">{ao}'
                     f'<span class="elementor-icon-list-icon">{fa_icon(it["selected_icon"])}</span>'
                     f'<span class="elementor-icon-list-text">{it["text"]}</span>{ac}</li>')
    ul = f'<ul class="elementor-icon-list-items{" elementor-inline-items" if inline else ""}">{"".join(items)}</ul>'
    return wrap_widget(el, ul, "elementor-icon-list--layout-inline" if inline else "elementor-icon-list--layout-traditional")


def w_counter(el):
    s = el["settings"]
    n = s["ending_number"]
    if s.get("thousand_separator") == "yes":
        num = f"{n:,}".replace(",", s.get("thousand_separator_char", ","))
    else:
        num = str(n)
    inner = (f'<div class="elementor-counter"><div class="elementor-counter-title">{s["title"]}</div>'
             f'<div class="elementor-counter-number-wrapper"><span class="elementor-counter-number-prefix"></span>'
             f'<span class="elementor-counter-number">{num}</span><span class="elementor-counter-number-suffix">{s.get("suffix", "")}</span></div></div>')
    return wrap_widget(el, inner)


def w_accordion(el):
    s = el["settings"]
    items = []
    for i, t in enumerate(s["tabs"]):
        active = " elementor-active" if i == 0 else ""
        disp = "block" if i == 0 else "none"
        icon = "fas fa-minus" if i == 0 else "fas fa-plus"
        items.append(f'<div class="elementor-accordion-item"><div class="elementor-tab-title{active}" role="button">'
                     f'<span class="elementor-accordion-icon elementor-accordion-icon-left"><i class="{icon}"></i></span>'
                     f'<a class="elementor-accordion-title" href="">{t["tab_title"]}</a></div>'
                     f'<div class="elementor-tab-content elementor-clearfix{active}" style="display:{disp}">{t["tab_content"]}</div></div>')
    return wrap_widget(el, f'<div class="elementor-accordion">{"".join(items)}</div>')


def w_image(el):
    s = el["settings"]
    return wrap_widget(el, f'<img src="{esc(s["image"]["url"])}" alt="{esc(s["image"].get("alt", ""))}" class="attachment-full size-full">')


def w_map(el):
    s = el["settings"]
    return wrap_widget(el, f'<div class="elementor-custom-embed" style="height:{s["height"]["size"]}px;border-radius:18px;'
                           f'background:repeating-linear-gradient(45deg,#e8e3d8 0 12px,#efebe2 12px 24px);display:flex;align-items:center;'
                           f'justify-content:center;color:#5F6B7C;font-weight:600">Google mapa: {esc(s["address"])}</div>')


# ---- vlastne widgety (napodobnenie PHP) ----

ICONS = {
    "pin": '<path d="M12 21.5s7-6.1 7-11.5a7 7 0 1 0-14 0c0 5.4 7 11.5 7 11.5z"/><circle cx="12" cy="10" r="2.6"/>',
    "euro": '<path d="M17.5 7.2A6.5 6.5 0 1 0 17.5 16.8M4.5 10.2h9M4.5 13.8h9"/>',
    "home": '<path d="M3 10.8 12 3.5l9 7.3"/><path d="M5.5 9.2V20.5h13V9.2"/><path d="M10 20.5v-5.5h4v5.5"/>',
    "arrow": '<path d="M5 12h14M13 6l6 6-6 6"/>',
    "lock": '<rect x="4.5" y="10.5" width="15" height="10.5" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
    "phone": '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
    "mail": '<rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6.5 9-6.5"/>',
    "menu": '<path d="M4 7h16M4 12h16M4 17h16"/>',
    "close": '<path d="M6 6l12 12M18 6 6 18"/>',
    "globe": '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/>',
    "facebook": '<path d="M14.5 8.5h3V4.5h-3a4 4 0 0 0-4 4V11h-3v4h3v6.5h4V15h3l.8-4h-3.8V9a.5.5 0 0 1 .5-.5z"/>',
    "instagram": '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".9" fill="currentColor" stroke="none"/>',
    "tiktok": '<path d="M14 3v11.6a3.6 3.6 0 1 1-3.6-3.6"/><path d="M14 3c.4 2.8 2.3 4.7 5.2 5"/>',
    "check": '<path d="M20 6 9 17l-5-5"/>',
    "calendar": '<rect x="3" y="4.5" width="18" height="16.5" rx="2.5"/><path d="M16 2.5v4M8 2.5v4M3 10h18"/>',
    "briefcase": '<rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A2 2 0 0 1 11 3.5h2a2 2 0 0 1 2 2V7M3 12.5h18"/>',
    "clock": '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/>',
    "tool": '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 12.6l-1.3 1.3-1.4-1.4-6.6 6.6a2 2 0 0 1-2.8-2.8l6.6-6.6-1.4-1.4L15.4 7z"/><path d="M14.7 6.3 12.5 4a4.5 4.5 0 0 0-6 .3"/>',
}
TRADE_ICONS = {
    "elektrikar": '<path d="M13 2 4.5 13.5H12L11 22l8.5-11.5H12z"/>',
    "vodar": '<path d="M12 3.5s5.5 6.2 5.5 10a5.5 5.5 0 0 1-11 0c0-3.8 5.5-10 5.5-10z"/>',
    "zvarac": '<path d="M12 22c4 0 7-2.7 7-6.6 0-3.2-2-5.3-3.5-7.4-.4 1.8-1.4 3-2.7 3.4C13.6 8 12.4 5 9.5 2.5 9.8 6.2 5 8.8 5 15.4 5 19.3 8 22 12 22z"/>',
    "maliar": '<rect x="3" y="3" width="15" height="6" rx="2"/><path d="M18 6h2.5v5.5H11v3"/><rect x="9" y="14.5" width="4" height="7" rx="1"/>',
    "betonar": '<path d="M3 7.5 12 3l9 4.5-9 4.5z"/><path d="M3 12l9 4.5 9-4.5M3 16.5 12 21l9-4.5"/>',
    "sadrokartonista": '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 12h18M12 3v18"/>',
}


def icon(name, cls=""):
    body = ICONS.get(name) or TRADE_ICONS.get(name) or ICONS["tool"]
    return (f'<svg class="asc-icon asc-icon--{name} {cls}" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            f'stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{body}</svg>')


JOBS = json.load(open(os.path.join(THEME, "templates", "sample-jobs.json"), encoding="utf-8"))
TERMS = json.load(open(os.path.join(THEME, "templates", "terms.json"), encoding="utf-8"))
TRADE_NAME = {t["slug"]: t["name"] for t in TERMS["asc_trade"]}
COUNTRY_NAME = {t["slug"]: t["name"] for t in TERMS["asc_country"]}


def job_card(j):
    m = j["meta"]
    top = '<span class="asc-badge asc-badge--top">TOP</span>' if m.get("top") else ""
    return (f'<article class="asc-job" data-trade="{j["trade"]}" data-country="{" ".join(j["countries"])}">'
            f'<div class="asc-job__top"><span class="asc-job__icon">{icon(j["trade"])}</span>{top}'
            f'<span class="asc-badge asc-badge--green">Nástup: {m["nastup"]}</span></div>'
            f'<h3 class="asc-job__title"><a href="ponuka-{j["trade"]}.html">{esc(j["title"])}</a></h3>'
            f'<p class="asc-job__excerpt">{esc(j["excerpt"])}</p>'
            f'<ul class="asc-job__meta"><li>{icon("pin")}<span>{m["lokalita"]}</span></li>'
            f'<li>{icon("euro")}<span><strong>{m["mzda"]}</strong></span></li>'
            f'<li>{icon("home")}<span>Ubytovanie: {m["ubytovanie"]}</span></li></ul>'
            f'<div class="asc-job__foot"><span>Zobraziť ponuku</span>{icon("arrow")}</div></article>')


def jobs_html(columns=3):
    jobs = sorted(JOBS, key=lambda j: 0 if j["meta"].get("top") else 1)
    chips = [f'<button type="button" class="asc-chip is-active" data-filter-trade="" aria-pressed="true">Všetky <span class="asc-chip__count">{len(jobs)}</span></button>']
    for j in JOBS:
        chips.append(f'<button type="button" class="asc-chip" data-filter-trade="{j["trade"]}" aria-pressed="false">'
                     f'{TRADE_NAME[j["trade"]]} <span class="asc-chip__count">1</span></button>')
    opts = "".join(f'<option value="{s}">{n}</option>' for s, n in COUNTRY_NAME.items())
    return (f'<div class="asc-jobs" data-asc-jobs data-pre-trade="" data-pre-country=""><div class="asc-jobs__bar">'
            f'<div class="asc-filter" role="group">{"".join(chips)}</div>'
            f'<label class="asc-jobs__select">Krajina <select data-filter-country><option value="">Všetky krajiny</option>{opts}</select></label></div>'
            f'<div class="asc-jobs__grid{" asc-jobs__grid--2" if columns == 2 else ""}">{"".join(job_card(j) for j in jobs)}</div>'
            f'<div class="asc-jobs__empty" hidden><h3>Nenašli sme ponuku pre tento výber</h3><p>Pošli nám všeobecnú prihlášku.</p>'
            f'<a class="asc-btn" href="prihlaska.html">Poslať prihlášku {icon("arrow")}</a></div></div>')


FORM_N = [0]
PARTNER_T = {
    "sk": ("Kontaktná osoba", "Názov firmy", "Telefón", "E-mail", "Krajina projektu", "Koho hľadáte?",
           "Profesie, počet ľudí, miesto a termín projektu…",
           "Súhlasím so spracovaním osobných údajov na účely odpovede na dopyt.", "Ochrana osobných údajov",
           "Vaše údaje použijeme iba na odpoveď na dopyt."),
    "de": ("Ansprechpartner", "Firmenname", "Telefon", "E-Mail", "Land des Projekts", "Wen suchen Sie?",
           "Gewerke, Anzahl der Mitarbeiter, Einsatzort und Zeitraum…",
           "Ich stimme der Verarbeitung meiner personenbezogenen Daten zur Beantwortung meiner Anfrage zu.",
           "Datenschutz", "Ihre Daten verwenden wir ausschließlich zur Beantwortung Ihrer Anfrage."),
    "fr": ("Personne de contact", "Nom de l’entreprise", "Téléphone", "E-mail", "Pays du projet",
           "Quels profils recherchez-vous ?", "Métiers, effectif, lieu et période du chantier…",
           "J’accepte le traitement de mes données personnelles afin de répondre à ma demande.",
           "Protection des données", "Vos données sont utilisées uniquement pour répondre à votre demande."),
}


def form_html(variant="full", title="", subtitle="", button="Odoslať prihlášku", lang="sk", position=""):
    FORM_N[0] += 1
    fid = f"asc-form-{FORM_N[0]}"
    req = '&nbsp;<span class="req" aria-hidden="true">*</span>'
    head = ""
    if title or subtitle:
        head = '<div class="asc-form__head">' + (f'<h3 class="asc-form__title">{esc(title)}</h3>' if title else "") + \
               (f'<p class="asc-form__sub">{esc(subtitle)}</p>' if subtitle else "") + "</div>"
    f = []
    if variant == "partner":
        t = PARTNER_T[lang]
        f.append(f'<div class="asc-field"><label>{t[1]}{req}</label><input type="text" name="asc_company" required></div>')
        f.append(f'<div class="asc-field"><label>{t[0]}{req}</label><input type="text" name="asc_name" required></div>')
        f.append(f'<div class="asc-field"><label>{t[2]}</label><input type="tel" name="asc_phone" placeholder="+421 9xx xxx xxx" data-optional-phone></div>')
        f.append(f'<div class="asc-field"><label>{t[3]}{req}</label><input type="email" name="asc_email" required></div>')
        f.append(f'<div class="asc-field"><label>{t[4]}</label><input type="text" name="asc_country"></div>')
        f.append(f'<div class="asc-field asc-field--full"><label>{t[5]}</label><textarea name="asc_message" rows="4" placeholder="{t[6]}"></textarea></div>')
        consent, privacy, note = t[7], t[8], t[9]
    else:
        full = variant == "full"
        f.append(f'<div class="asc-field{" asc-field--full" if full else ""}"><label>Meno a priezvisko{req}</label><input type="text" name="asc_name" autocomplete="name" required></div>')
        f.append(f'<div class="asc-field"><label>Telefón{req}</label><input type="tel" name="asc_phone" placeholder="+421 9xx xxx xxx" required></div>')
        if full:
            f.append('<div class="asc-field"><label>E-mail</label><input type="email" name="asc_email"></div>')
        opts = "".join(f'<option{" selected" if n == position else ""}>{n}</option>' for n in TRADE_NAME.values())
        f.append(f'<div class="asc-field"><label>Pozícia{req}</label><select name="asc_position" required><option value="">Vyber pozíciu</option>{opts}<option>Iná pozícia</option></select></div>')
        if full:
            f.append('<div class="asc-field"><label>Prax v odbore</label><select><option>– vyber –</option><option>Bez praxe</option><option>Do 1 roka</option><option>1 – 3 roky</option><option>Viac ako 3 roky</option></select></div>')
            f.append('<div class="asc-field"><label>Kedy môžeš nastúpiť?</label><select><option>– vyber –</option><option>Ihneď</option><option>Do 2 týždňov</option><option>Do mesiaca</option><option>Neskôr</option></select></div>')
            f.append('<label class="asc-check asc-check--inline"><input type="checkbox"> <span>Mám vodičský preukaz sk. B</span></label>')
            f.append('<div class="asc-field asc-field--full"><label>Správa (nepovinné)</label><textarea rows="4" placeholder="Napr. aké máš skúsenosti, odkiaľ si, kedy sa ti môžeme ozvať…"></textarea></div>')
        consent, privacy, note = ("Súhlasím so spracovaním osobných údajov na účely výberového konania.",
                                  "Viac o ochrane údajov", "Tvoje údaje použijeme iba na to, aby sme ťa kontaktovali.")
    f.append(f'<label class="asc-check asc-check--consent"><input type="checkbox" name="asc_consent" required> <span>{consent} '
             f'<a href="ochrana-osobnych-udajov.html">{privacy}</a>{req}</span></label>')
    return (f'<div class="asc-form asc-form--{variant}" id="{fid}"><div class="asc-form__body">{head}'
            f'<form class="asc-form__form" action="#" novalidate data-preview><div class="asc-form__grid">{"".join(f)}</div>'
            f'<button type="submit" class="asc-btn asc-form__submit"><span>{esc(button)}</span> {icon("arrow")}</button>'
            f'<p class="asc-form__note">{icon("lock")} {note}</p><div class="asc-form__msg" role="alert"></div></form></div>'
            f'<div class="asc-form__success" hidden><span class="asc-form__badge">{icon("check")}</span><h3>Ďakujeme!</h3>'
            f'<p>Prihlášku sme prijali a čoskoro sa ti ozveme telefonicky.</p></div></div>')


def w_jobs(el):
    return wrap_widget(el, jobs_html(int(el["settings"].get("columns", 3))))


def w_apply(el):
    s = el["settings"]
    return wrap_widget(el, form_html(s["variant"], s.get("title", ""), s.get("subtitle", ""), s.get("button_text", ""),
                                     s.get("lang", "sk")))


WIDGETS = {"heading": w_heading, "text-editor": w_text, "button": w_button, "icon-box": w_iconbox,
           "icon-list": w_iconlist, "counter": w_counter, "accordion": w_accordion, "image": w_image,
           "google_maps": w_map, "asc-jobs": w_jobs, "asc-apply": w_apply}


def render(el, depth=0):
    if el["elType"] == "widget":
        fn = WIDGETS.get(el["widgetType"])
        if not fn:
            return f"<!-- neznamy widget {el['widgetType']} -->"
        return fn(el)
    s = el["settings"]
    con_css(el)
    is_grid = s.get("container_type") == "grid"
    boxed = s.get("content_width") == "boxed"
    cls = (f'elementor-element elementor-element-{el["id"]} {"e-grid" if is_grid else "e-flex"} '
           f'{"e-con-boxed" if boxed else "e-con-full"} e-con {"e-child" if depth else "e-parent"} {s.get("css_classes", "")}')
    tag = s.get("html_tag", "div")
    idattr = f' id="{s["_element_id"]}"' if s.get("_element_id") else ""
    kids = "".join(render(c, depth + 1) for c in el["elements"])
    if boxed:
        kids = f'<div class="e-con-inner">{kids}</div>'
    return f'<{tag} class="{" ".join(cls.split())}"{idattr} data-id="{el["id"]}">{kids}</{tag}>'


# ---------------------------------------------------------------- hlavicka / paticka

MENU = [("Pracovné ponuky", "pracovne-ponuky.html"), ("Čo zabezpečíme", "index.html#benefity"),
        ("Ako to funguje", "index.html#postup"), ("O nás", "o-nas.html"), ("Pre firmy", "pre-firmy.html"),
        ("Kontakt", "kontakt.html")]
TOPLINKS = [("Für Unternehmen", "fur-unternehmen.html"), ("Pour les entreprises", "pour-les-entreprises.html")]


def header(current):
    def items(lst, cls):
        out = []
        for label, href in lst:
            cur = " current-menu-item" if href == current else ""
            out.append(f'<li class="menu-item{cur}"><a href="{href}">{label}</a></li>')
        return f'<ul class="{cls}">{"".join(out)}</ul>'

    top = items(TOPLINKS, "asc-topbar__links")
    return f'''<a class="skip-link" href="#obsah">Preskočiť na obsah</a>
<div class="asc-topbar"><div class="asc-container asc-topbar__inner">
<p class="asc-topbar__text">{icon("globe")} Práca v Nemecku a Francúzsku · ubytovanie, auto aj náradie zabezpečíme</p>
<div class="asc-topbar__right" style="display:flex;align-items:center;gap:4px"><span class="asc-topbar__label">Pre firmy:</span>{top}</div>
</div></div>
<header class="asc-header" id="asc-header"><div class="asc-container asc-header__inner">
<a class="asc-logo" href="index.html"><img src="{IMG}/ascensio-logo.svg" width="190" height="36" alt="Ascensio"></a>
<nav class="asc-nav" id="asc-nav" aria-label="Hlavné menu">{items(MENU, "asc-menu")}
<div class="asc-nav__extra"><a class="asc-btn" href="prihlaska.html">Chcem prácu {icon("arrow")}</a>
<a class="asc-btn asc-btn--outline" href="{TEL}">{icon("phone")} {PHONE}</a><div class="asc-nav__langs">{top}</div></div></nav>
<div class="asc-header__actions">
<a class="asc-header__phone" href="{TEL}"><span class="asc-header__phoneicon">{icon("phone")}</span><span class="asc-header__phonetext"><small>Zavolajte nám</small>{PHONE}</span></a>
<a class="asc-btn asc-header__cta" href="prihlaska.html">Chcem prácu</a>
<button class="asc-burger" type="button" aria-controls="asc-nav" aria-expanded="false" aria-label="Otvoriť menu">{icon("menu", "asc-icon--menu")}{icon("close", "asc-icon--close")}</button>
</div></div></header>
<main id="obsah" class="asc-main">'''


def footer():
    fmenu = "".join(f'<li><a href="{h}">{l}</a></li>' for l, h in [
        ("Pracovné ponuky", "pracovne-ponuky.html"), ("Prihláška", "prihlaska.html"),
        ("Čo zabezpečíme", "index.html#benefity"), ("Časté otázky", "index.html#otazky"),
        ("O nás", "o-nas.html"), ("Kontakt", "kontakt.html")])
    socials = "".join(f'<li><a href="#" aria-label="{n}">{icon(n)}</a></li>' for n in ("facebook", "instagram", "tiktok"))
    langs = "".join(f'<li><a href="{h}">{l}</a></li>' for l, h in TOPLINKS)
    return f'''</main>
<footer class="asc-footer"><div class="asc-container asc-footer__main">
<div class="asc-footer__brand"><div class="asc-footer__logo"><a class="asc-logo" href="index.html"><img src="{IMG}/ascensio-logo-white.svg" alt="Ascensio"></a></div>
<p class="asc-footer__about">Od roku 1998 pomáhame remeselníkom nájsť stabilnú prácu v zahraničí. Ubytovanie, doprava aj náradie sú u nás samozrejmosťou.</p>
<ul class="asc-social-links">{socials}</ul></div>
<div><h2>Pre uchádzačov</h2><ul class="asc-footer__menu">{fmenu}</ul></div>
<div><h2>Kontakt</h2><ul class="asc-footer__contact">
<li>{icon("phone")}<a href="{TEL}">{PHONE}</a></li><li>{icon("mail")}<a href="mailto:{EMAIL}">{EMAIL}</a></li>
<li>{icon("pin")}<address>AS-SPOL, s.r.o.
Továrenská 1471/7
064 01 Stará Ľubovňa</address></li></ul></div>
<div><h2>Pre firmy</h2><div class="asc-footer__b2b"><strong>Hľadáte remeselníkov?</strong>
<p>Dodávame skúsených pracovníkov pre stavebné projekty v Nemecku a Francúzsku.</p>
<a class="asc-btn asc-btn--sm" href="pre-firmy.html">Spolupráca {icon("arrow")}</a><ul class="asc-footer__langs">{langs}</ul></div></div>
</div>
<div class="asc-footer__bottom"><div class="asc-container"><p>&copy; 2026 AS-SPOL, s.r.o. · Ascensio</p>
<ul class="asc-footer__legal"><li><a href="ochrana-osobnych-udajov.html">Ochrana osobných údajov</a></li></ul>
<a href="#obsah">Hore ↑</a></div></div></footer>
<div class="asc-mbar" id="asc-mbar"><a class="asc-btn asc-btn--outline" href="{TEL}">{icon("phone")} Zavolať</a>
<a class="asc-btn" href="prihlaska.html">Chcem prácu {icon("arrow")}</a></div>'''


VER = str(int(time.time()))


def page(title, body, current, body_cls="asc-elementor-page"):
    css = []
    for media in (None, "(max-width:1024px)", "(max-width:767px)"):
        block = "".join(f"{sel}{{{decl}}}" for m, sel, decl in css_rules if m == media and decl)
        css.append(f"@media {media}{{{block}}}" if media else block)
    return f'''<!doctype html>
<html lang="sk"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{esc(title)} – Ascensio</title>
<link rel="icon" href="{IMG}/ascensio-ikona.svg">
<link rel="stylesheet" href="https://fonts.bunny.net/css?family=archivo:600,700,800|inter:400,500,600,700&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="emu.css">
<link rel="stylesheet" href="../ascensio-theme/assets/css/main.css?v={VER}">
<style>{"".join(css)}</style>
</head><body class="asc-has-mbar {body_cls}">
{header(current)}
<div class="elementor">{body}</div>
{footer()}
<script>window.ascensio={{ajaxUrl:"#",i18n:{{}}}};</script>
<script src="../ascensio-theme/assets/js/main.js?v={VER}"></script>
<script src="emu.js"></script>
</body></html>'''


EMU_CSS = """/* Minimalna emulacia zakladnych stylov Elementoru pre nahlad */
.e-con{display:flex;flex-direction:column;box-sizing:border-box;position:relative;min-width:0;width:100%;flex-shrink:1}
.e-con-boxed>.e-con-inner{width:100%;max-width:1200px;margin:0 auto}
.e-con>.elementor-widget,.e-con-inner>.elementor-widget{max-width:100%;min-width:0}
.e-con.e-flex[style],.e-con{ }
.elementor-heading-title{margin:0;padding:0;line-height:1}
.elementor-widget-text-editor p{margin:0 0 1em}
.elementor-button{display:inline-block;line-height:1;text-align:center}
.elementor-button-wrapper{display:block}
.elementor-align-right{text-align:right}
.elementor-icon-list-items{list-style:none;margin:0;padding:0}
.elementor-icon-list-item{display:flex;align-items:center}
.elementor-icon-list-item a{display:flex;align-items:center}
.elementor-inline-items{display:flex;flex-wrap:wrap}
.elementor-icon{display:inline-block;line-height:1}
.elementor-icon-box-title{margin:0}
.elementor-counter-number-wrapper{display:flex}
.elementor-accordion-item{border:1px solid #d5d8dc}
.elementor-tab-title{padding:15px 20px}
.elementor-widget-image img{max-width:100%;height:auto}
.e-con>.e-con,.e-con-inner>.e-con{flex-shrink:1}
"""
EMU_JS = """document.querySelectorAll('.elementor-accordion .elementor-tab-title').forEach(function(t){t.addEventListener('click',function(e){e.preventDefault();var item=t.parentNode,c=item.querySelector('.elementor-tab-content'),open=t.classList.contains('elementor-active');item.parentNode.querySelectorAll('.elementor-tab-title').forEach(function(x){x.classList.remove('elementor-active');x.nextElementSibling.style.display='none';var i=x.querySelector('i');if(i)i.className='fas fa-plus'});if(!open){t.classList.add('elementor-active');c.style.display='block';var i=t.querySelector('i');if(i)i.className='fas fa-minus'}})});
document.querySelectorAll('form[data-preview]').forEach(function(f){f.setAttribute('action','#')});
window.fetch=function(){return Promise.resolve({json:function(){return Promise.resolve({success:true,data:{}})}})};"""


def job_page(j):
    m = j["meta"]
    req = "".join(f"<li>{esc(x)}</li>" for x in m["poziadavky"].split("\n"))
    offer = "".join(f"<li>{esc(x)}</li>" for x in m["ponukame"].split("\n"))
    rel = "".join(job_card(x) for x in [x for x in JOBS if x is not j][:3])
    rows = "".join(f'<div><dt>{icon(ic)}{lab}</dt><dd>{val}</dd></div>' for ic, lab, val in [
        ("briefcase", "Pozícia", j["title"]), ("pin", "Miesto", m["lokalita"]), ("euro", "Mzda", m["mzda"]),
        ("calendar", "Nástup", m["nastup"]), ("clock", "Úväzok", m["uvazok"]), ("home", "Ubytovanie", m["ubytovanie"])])
    body = f'''<section class="asc-jobhero asc-pattern-bg"><div class="asc-container">
<nav class="asc-crumbs"><a href="index.html">Domov</a> <span>/</span> <a href="pracovne-ponuky.html">Pracovné ponuky</a> <span>/</span> <span>{j["title"]}</span></nav>
<h1>{j["title"]}</h1><div class="asc-jobhero__badges">{'<span class="asc-badge asc-badge--top">TOP ponuka</span>' if m.get("top") else ""}
<span class="asc-badge">{icon("pin")} {m["lokalita"]}</span><span class="asc-badge">{icon("euro")} {m["mzda"]}</span>
<span class="asc-badge">{icon("calendar")} Nástup: {m["nastup"]}</span><span class="asc-badge">{icon("home")} Ubytovanie: {m["ubytovanie"]}</span></div>
<div class="asc-jobhero__cta"><a class="asc-btn" href="#reagovat">Reagovať na ponuku {icon("arrow")}</a><a class="asc-btn asc-btn--outline-light" href="{TEL}">{icon("phone")} {PHONE}</a></div>
</div></section>
<section class="asc-jobbody"><div class="asc-container asc-jobbody__grid"><div class="asc-jobbody__main">
<div class="asc-panel"><h2>Popis práce</h2>{j["content"]}</div>
<div class="asc-panel"><h2>Požiadavky</h2><ul class="asc-ticks asc-ticks--navy">{req}</ul></div>
<div class="asc-panel"><h2>Čo ti zabezpečíme</h2><ul class="asc-ticks">{offer}</ul></div>
<div id="reagovat">{form_html("full", "Reagovať na ponuku: " + j["title"], "Vyplň krátky formulár – ozveme sa ti telefonicky. Polia označené * sú povinné.", "Odoslať prihlášku", position=TRADE_NAME[j["trade"]])}</div>
</div><aside class="asc-summary"><h2>Zhrnutie</h2><dl>{rows}</dl>
<a class="asc-btn" href="#reagovat">Reagovať na ponuku</a><a class="asc-btn asc-btn--outline" href="{TEL}">{icon("phone")} {PHONE}</a>
<p class="asc-summary__help">Máš otázku? Zavolaj – všetko ti vysvetlíme.</p></aside></div></section>
<section class="asc-related"><div class="asc-container"><h2>Ďalšie pracovné ponuky</h2><div class="asc-jobs__grid">{rel}</div></div></section>'''
    return body


def main():
    with open(os.path.join(OUT, "emu.css"), "w", encoding="utf-8") as f:
        f.write(EMU_CSS)
    with open(os.path.join(OUT, "emu.js"), "w", encoding="utf-8") as f:
        f.write(EMU_JS)
    pages_dir = os.path.join(THEME, "templates", "pages")
    for name in sorted(os.listdir(pages_dir)):
        data = json.load(open(os.path.join(pages_dir, name), encoding="utf-8"))
        css_rules.clear()
        FORM_N[0] = 0
        fname = link_for(data["key"])
        if "html" in data:
            body = (f'<header class="asc-pagehead"><div class="asc-container"><h1>{data["title"]}</h1></div></header>'
                    f'<div class="asc-prose entry-content">{resolve(data["html"])}</div>')
            out = page(data["title"], body, fname, "")
        else:
            els = json.loads(resolve(json.dumps(data["elementor"], ensure_ascii=False)))
            body = "".join(render(e) for e in els)
            out = page(data["title"], body, fname)
        with open(os.path.join(OUT, fname), "w", encoding="utf-8") as f:
            f.write(out)
    for j in JOBS:
        css_rules.clear()
        FORM_N[0] = 0
        with open(os.path.join(OUT, f"ponuka-{j['trade']}.html"), "w", encoding="utf-8") as f:
            f.write(page(j["title"], job_page(j), "", "single-asc_job"))
    print("Nahlad:", OUT)


if __name__ == "__main__":
    main()
