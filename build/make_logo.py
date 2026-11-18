"""Generuje SVG varianty zjednoduseneho loga Ascensio.

Koncept: zachovat zaobleny monoline charakter povodneho loga (spojitost znacky),
ale zhrubnut tah, zjednodusit tvary pismen, vyhodit podtitul "holding"
a nahradit bodku nad "i" zlatou sipkou nahor (ascensio = lat. vzostup).
"""
import math
import os

OUT = os.path.join(os.path.dirname(__file__), "..", "logo")
THEME_IMG = os.path.join(os.path.dirname(__file__), "..", "ascensio-theme", "assets", "img")
os.makedirs(OUT, exist_ok=True)
os.makedirs(THEME_IMG, exist_ok=True)

NAVY = "#14263F"
GOLD = "#C9A04E"
WHITE = "#FFFFFF"

SW = 16          # hrubka tahu (x-vyska = 100)
R = 42           # polomer okruhlych tvarov (stredova ciara)
GAP = 30         # vzdialenost stredovych ciar medzi pismenami
TOP, BOT, MID = 8, 92, 50


def arc_pt(cx, cy, r, deg):
    a = math.radians(deg)
    return cx + r * math.cos(a), cy + r * math.sin(a)


def letter_a(x):
    cx = x + R
    d = (f"M{cx + R},{MID} A{R},{R} 0 1 0 {cx - R},{MID} A{R},{R} 0 1 0 {cx + R},{MID} "
         f"M{cx + R},{TOP} V{BOT}")
    return d, x + 2 * R


def letter_s(x, w=58):
    # dva "luky" ako geometricke monoline s
    p = lambda px, py: f"{x + px:.1f},{py:.1f}"
    d = (f"M{p(w - 3, 21)} C{p(w - 8, 12)} {p(w * 0.74, 8)} {p(w / 2, 8)} "
         f"C{p(w * 0.22, 8)} {p(2, 15)} {p(2, 28.5)} "
         f"C{p(2, 42)} {p(w * 0.25, 46)} {p(w / 2, 50)} "
         f"C{p(w * 0.77, 54)} {p(w, 59)} {p(w, 71.5)} "
         f"C{p(w, 85)} {p(w * 0.78, 92)} {p(w / 2, 92)} "
         f"C{p(w * 0.24, 92)} {p(4, 86)} {p(0, 78)}")
    return d, x + w


def letter_c(x):
    cx = x + R
    x1, y1 = arc_pt(cx, MID, R, -42)
    x2, y2 = arc_pt(cx, MID, R, 42)
    d = f"M{x1:.1f},{y1:.1f} A{R},{R} 0 1 0 {x2:.1f},{y2:.1f}"
    return d, x1


def letter_e(x):
    cx = x + R
    x2, y2 = arc_pt(cx, MID, R, 42)
    d = f"M{cx - R},{MID} H{cx + R} A{R},{R} 0 1 0 {x2:.1f},{y2:.1f}"
    return d, cx + R


def letter_n(x, w=70):
    d = (f"M{x},{TOP} V{BOT} M{x},{MID - 2} C{x},{TOP + 14} {x + w * 0.22},{TOP} {x + w / 2},{TOP} "
         f"C{x + w * 0.78},{TOP} {x + w},{TOP + 14} {x + w},{MID - 2} V{BOT}")
    return d, x + w


def letter_i(x):
    return f"M{x},{TOP} V{BOT}", x


def letter_o(x):
    cx = x + R
    d = f"M{cx + R},{MID} A{R},{R} 0 1 0 {cx - R},{MID} A{R},{R} 0 1 0 {cx + R},{MID}"
    return d, x + 2 * R


def chevron(cx, base_y=-24, half=17, h=17):
    return f"M{cx - half},{base_y} L{cx},{base_y - h} L{cx + half},{base_y}"


def build_wordmark():
    """Vrati list (pismeno, path, x_start, x_end)."""
    parts = []
    x = 0
    seq = [("a", letter_a), ("s", letter_s), ("c", letter_c), ("e", letter_e),
           ("n", letter_n), ("s", letter_s), ("i", letter_i), ("o", letter_o)]
    adjust = {("c", "e"): -4, ("a", "s"): 0}
    prev = None
    for ch, fn in seq:
        if prev:
            x += GAP + adjust.get((prev, ch), 0)
        d, end = fn(x)
        parts.append((ch, d, x, end))
        x = end
        prev = ch
    return parts


def svg_wrap(body, vb, title, w=None):
    vbx, vby, vbw, vbh = vb
    size = f' width="{w}" height="{w * vbh / vbw:.0f}"' if w else ""
    return (f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="{vbx} {vby} {vbw} {vbh}"{size} '
            f'role="img" aria-label="{title}">\n<title>{title}</title>\n{body}\n</svg>\n')


def wordmark_svg(word_color, accent_color, as_color=None, holding=False):
    parts = build_wordmark()
    g_main, g_as = [], []
    i_x = None
    for idx, (ch, d, x0, x1) in enumerate(parts):
        if as_color and idx < 2:
            g_as.append(d)
        else:
            g_main.append(d)
        if ch == "i":
            i_x = x0
    stroke = f'fill="none" stroke-width="{SW}" stroke-linecap="round" stroke-linejoin="round"'
    body = f'<g {stroke}>\n'
    if g_as:
        body += f'  <path stroke="{as_color}" d="{" ".join(g_as)}"/>\n'
    body += f'  <path stroke="{word_color}" d="{" ".join(g_main)}"/>\n'
    body += f'  <path stroke="{accent_color}" d="{chevron(i_x)}"/>\n'
    body += '</g>'
    right = parts[-1][3] + SW / 2
    top = -24 - 17 - SW / 2
    height = 100 - top
    if holding:
        body += (f'\n<text x="{right / 2:.0f}" y="150" text-anchor="middle" fill="{word_color}" '
                 f'font-family="Archivo, Arial, sans-serif" font-size="30" font-weight="600" '
                 f'letter-spacing="18">HOLDING</text>')
        height += 62
    pad = 4
    return body, (-SW / 2 - pad, top - pad, right + SW / 2 + 2 * pad, height + 2 * pad)


def icon_svg(bg, fg, accent):
    # stvorcova ikona: male "a" so zlatou sipkou nad nim
    size = 200
    s = 0.95
    ox, oy = 100 - (2 * R) * s / 2 - 6, 70
    a_d, _ = letter_a(0)
    body = (f'<rect width="{size}" height="{size}" rx="44" fill="{bg}"/>\n'
            f'<g transform="translate({ox:.1f},{oy}) scale({s})" fill="none" stroke-width="{SW + 2}" '
            f'stroke-linecap="round" stroke-linejoin="round">\n'
            f'  <path stroke="{fg}" d="{a_d}"/>\n'
            f'  <path stroke="{accent}" d="{chevron(R + 6, base_y=-22, half=24, h=22)}"/>\n'
            f'</g>')
    return body, (0, 0, size, size)


def write(name, body_vb, title, also_theme=False):
    body, vb = body_vb
    svg = svg_wrap(body, vb, title)
    for folder in [OUT] + ([THEME_IMG] if also_theme else []):
        with open(os.path.join(folder, name), "w", encoding="utf-8") as f:
            f.write(svg)


write("ascensio-logo.svg", wordmark_svg(NAVY, GOLD), "Ascensio", also_theme=True)
write("ascensio-logo-white.svg", wordmark_svg(WHITE, GOLD), "Ascensio", also_theme=True)
write("ascensio-logo-dvojfarebne.svg", wordmark_svg(NAVY, GOLD, as_color=GOLD), "Ascensio")
write("ascensio-logo-holding.svg", wordmark_svg(NAVY, GOLD, holding=True), "Ascensio Holding")
write("ascensio-logo-mono-navy.svg", wordmark_svg(NAVY, NAVY), "Ascensio")
write("ascensio-ikona.svg", icon_svg(NAVY, WHITE, GOLD), "Ascensio", also_theme=True)
write("ascensio-ikona-zlata.svg", icon_svg(GOLD, NAVY, NAVY), "Ascensio")
print("OK", os.path.abspath(OUT))
