"""Generuje ilustracie a vlajky pre temu (SVG, bez externych zdrojov)."""
import os
import random

IMG = os.path.join(os.path.dirname(__file__), "..", "ascensio-theme", "assets", "img")
os.makedirs(IMG, exist_ok=True)
rng = random.Random(7)

NAVY, NAVY3, GOLD, GOLD2 = "#14263F", "#1D3556", "#C9A04E", "#DDB766"


def save(name, svg):
    with open(os.path.join(IMG, name), "w", encoding="utf-8") as f:
        f.write(svg)


# ---------- vlajky ----------
save("vlajka-de.svg", '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 5 3" width="50" height="30">'
     '<rect width="5" height="1" fill="#000"/><rect y="1" width="5" height="1" fill="#DD0000"/>'
     '<rect y="2" width="5" height="1" fill="#FFCE00"/></svg>\n')
save("vlajka-fr.svg", '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 3 2" width="45" height="30">'
     '<rect width="1" height="2" fill="#0055A4"/><rect x="1" width="1" height="2" fill="#fff"/>'
     '<rect x="2" width="1" height="2" fill="#EF4135"/></svg>\n')


# ---------- ilustracia stavby ----------
def construction():
    W, H = 800, 600
    ground = 470
    p = []
    p.append(f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-label="Ilustrácia stavebného projektu">')
    p.append('<defs>'
             f'<linearGradient id="sky" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{NAVY3}"/><stop offset="1" stop-color="{NAVY}"/></linearGradient>'
             f'<radialGradient id="sun" cx="0.82" cy="0.18" r="0.5"><stop offset="0" stop-color="{GOLD}" stop-opacity=".35"/><stop offset="1" stop-color="{GOLD}" stop-opacity="0"/></radialGradient>'
             '<linearGradient id="bld" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2A4A75"/><stop offset="1" stop-color="#1B3150"/></linearGradient>'
             '</defs>')
    p.append(f'<rect width="{W}" height="{H}" fill="url(#sky)"/>')
    p.append(f'<rect width="{W}" height="{H}" fill="url(#sun)"/>')
    # chevrony v pozadi (motiv z loga)
    for i, (x, y, s, o) in enumerate([(110, 120, 46, .10), (110, 160, 46, .07), (110, 200, 46, .045)]):
        p.append(f'<path d="M{x - s},{y} L{x},{y - s * .8} L{x + s},{y}" fill="none" stroke="{GOLD2}" '
                 f'stroke-opacity="{o}" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>')

    # vzdialene budovy
    far = [(0, 330, 90), (80, 300, 70), (150, 350, 60), (560, 320, 80), (640, 290, 90), (730, 340, 70)]
    for x, top, w in far:
        p.append(f'<rect x="{x}" y="{top}" width="{w}" height="{ground - top}" fill="#1F3758" opacity=".7"/>')

    # hotove budovy s oknami
    def building(x, top, w, cols, rows, lit=0.25):
        out = [f'<rect x="{x}" y="{top}" width="{w}" height="{ground - top}" rx="3" fill="url(#bld)"/>']
        pad, gap = 14, 10
        ww = (w - 2 * pad - (cols - 1) * gap) / cols
        wh = 16
        for r in range(rows):
            for c in range(cols):
                wx = x + pad + c * (ww + gap)
                wy = top + 18 + r * (wh + 14)
                if wy + wh > ground - 20:
                    continue
                on = rng.random() < lit
                fill = GOLD2 if on else "#ffffff"
                op = .85 if on else .08
                out.append(f'<rect x="{wx:.1f}" y="{wy}" width="{ww:.1f}" height="{wh}" rx="2" fill="{fill}" opacity="{op}"/>')
        return "".join(out)

    p.append(building(40, 250, 150, 4, 8, .22))
    p.append(building(600, 220, 140, 3, 9, .28))

    # rozostavana budova – oceľová konštrukcia
    bx, bw, btop = 270, 260, 190
    floors = 6
    fh = (ground - btop) / floors
    frame = []
    for i in range(floors + 1):
        y = ground - i * fh
        frame.append(f'M{bx},{y:.1f} H{bx + bw}')
    for c in range(5):
        x = bx + c * bw / 4
        frame.append(f'M{x:.1f},{ground} V{btop}')
    # diagonaly
    for i in range(floors):
        y1 = ground - i * fh
        y2 = y1 - fh
        if i % 2 == 0:
            frame.append(f'M{bx},{y1:.1f} L{bx + bw / 4:.1f},{y2:.1f}')
            frame.append(f'M{bx + bw},{y1:.1f} L{bx + 3 * bw / 4:.1f},{y2:.1f}')
    p.append(f'<path d="{" ".join(frame)}" fill="none" stroke="{GOLD}" stroke-width="5" stroke-linecap="round" opacity=".9"/>')
    # dokoncene spodne podlazia (vyplnene)
    for i in range(2):
        y = ground - (i + 1) * fh
        p.append(f'<rect x="{bx + 4}" y="{y + 4:.1f}" width="{bw - 8}" height="{fh - 8:.1f}" fill="#ffffff" opacity=".06"/>')
        for c in range(4):
            p.append(f'<rect x="{bx + 14 + c * bw / 4:.1f}" y="{y + 16:.1f}" width="{bw / 4 - 28:.1f}" height="{fh - 32:.1f}" rx="2" fill="{GOLD2}" opacity="{.55 if (i + c) % 3 == 0 else .15}"/>')

    # zeriav
    mx = 560
    mtop = 90
    p.append(f'<path d="M{mx},{ground} V{mtop} M{mx + 22},{ground} V{mtop}" stroke="#E8EDF5" stroke-width="4" opacity=".9"/>')
    zig = []
    y = ground
    left = True
    while y > mtop + 10:
        ny = y - 22
        zig.append(f'M{mx if left else mx + 22},{y} L{mx + 22 if left else mx},{ny}')
        y = ny
        left = not left
    p.append(f'<path d="{" ".join(zig)}" stroke="#E8EDF5" stroke-width="2.5" opacity=".7"/>')
    # rameno
    jy = mtop
    p.append(f'<path d="M{mx - 330},{jy} H{mx + 120} M{mx - 330},{jy + 16} H{mx + 22}" stroke="#E8EDF5" stroke-width="4" opacity=".9"/>')
    tri = []
    x = mx - 330
    up = True
    while x < mx:
        tri.append(f'M{x},{jy + 16 if up else jy} L{x + 20},{jy if up else jy + 16}')
        x += 20
        up = not up
    p.append(f'<path d="{" ".join(tri)}" stroke="#E8EDF5" stroke-width="2" opacity=".6"/>')
    p.append(f'<path d="M{mx + 11},{jy - 50} L{mx - 200},{jy} M{mx + 11},{jy - 50} L{mx + 110},{jy} M{mx + 11},{jy - 50} V{jy}" stroke="#E8EDF5" stroke-width="2" opacity=".6"/>')
    p.append(f'<rect x="{mx + 70}" y="{jy + 4}" width="44" height="34" rx="3" fill="{GOLD}"/>')  # protivaha
    p.append(f'<rect x="{mx - 4}" y="{jy + 18}" width="30" height="26" rx="3" fill="{GOLD}"/>')  # kabina
    # lano a bremeno
    hx = mx - 250
    hy = 260
    p.append(f'<path d="M{hx},{jy + 16} V{hy}" stroke="#E8EDF5" stroke-width="2" opacity=".8"/>')
    p.append(f'<path d="M{hx},{hy} L{hx - 46},{hy + 26} M{hx},{hy} L{hx + 46},{hy + 26}" stroke="#E8EDF5" stroke-width="2" opacity=".7"/>')
    p.append(f'<rect x="{hx - 60}" y="{hy + 26}" width="120" height="14" rx="3" fill="{GOLD2}"/>')

    # zem
    p.append(f'<rect x="0" y="{ground}" width="{W}" height="{H - ground}" fill="#0F1F35"/>')
    p.append(f'<path d="M0,{ground} H{W}" stroke="{GOLD}" stroke-width="3" opacity=".5"/>')
    # ploty / kuzele
    for x in range(30, W, 46):
        p.append(f'<rect x="{x}" y="{ground + 26}" width="28" height="6" rx="3" fill="#ffffff" opacity=".07"/>')
    for x in (230, 250, 548, 568):
        p.append(f'<path d="M{x},{ground} l7,-22 l7,22 z" fill="{GOLD}" opacity=".9"/>')
    p.append('</svg>\n')
    return "".join(p)


save("ilustracia-stavba.svg", construction())


# ---------- tim (abstraktne postavy s prilbami) ----------
def team():
    W, H = 800, 600
    p = [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-label="Ilustrácia tímu Ascensio">',
         '<defs>'
         f'<linearGradient id="bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{NAVY3}"/><stop offset="1" stop-color="{NAVY}"/></linearGradient>'
         f'<radialGradient id="glow" cx="0.5" cy="0.35" r="0.6"><stop offset="0" stop-color="{GOLD}" stop-opacity=".28"/><stop offset="1" stop-color="{GOLD}" stop-opacity="0"/></radialGradient>'
         '</defs>',
         f'<rect width="{W}" height="{H}" fill="url(#bg)"/>', f'<rect width="{W}" height="{H}" fill="url(#glow)"/>']
    # chevrony
    for i in range(3):
        y = 150 + i * 46
        p.append(f'<path d="M340,{y} L400,{y - 46} L460,{y}" fill="none" stroke="{GOLD2}" stroke-opacity="{.16 - i * .04:.2f}" '
                 f'stroke-width="14" stroke-linecap="round" stroke-linejoin="round"/>')

    def person(cx, base, scale, body, helmet):
        s = scale
        head_r = 46 * s
        hy = base - 230 * s
        out = []
        # telo
        out.append(f'<path d="M{cx - 120 * s},{base} C{cx - 120 * s},{base - 150 * s} {cx - 70 * s},{base - 175 * s} {cx},{base - 175 * s} '
                   f'C{cx + 70 * s},{base - 175 * s} {cx + 120 * s},{base - 150 * s} {cx + 120 * s},{base} Z" fill="{body}"/>')
        # reflexna vesta pruhy
        out.append(f'<path d="M{cx - 70 * s},{base - 70 * s} H{cx + 70 * s}" stroke="{GOLD2}" stroke-width="{10 * s}" stroke-linecap="round" opacity=".85"/>')
        out.append(f'<path d="M{cx - 30 * s},{base - 168 * s} L{cx - 22 * s},{base}" stroke="{GOLD2}" stroke-width="{8 * s}" opacity=".6"/>')
        out.append(f'<path d="M{cx + 30 * s},{base - 168 * s} L{cx + 22 * s},{base}" stroke="{GOLD2}" stroke-width="{8 * s}" opacity=".6"/>')
        # hlava
        out.append(f'<circle cx="{cx}" cy="{hy}" r="{head_r}" fill="#E9D7BE"/>')
        # prilba
        out.append(f'<path d="M{cx - 56 * s},{hy - 8 * s} C{cx - 56 * s},{hy - 70 * s} {cx + 56 * s},{hy - 70 * s} {cx + 56 * s},{hy - 8 * s} Z" fill="{helmet}"/>')
        out.append(f'<rect x="{cx - 66 * s}" y="{hy - 14 * s}" width="{132 * s}" height="{14 * s}" rx="{7 * s}" fill="{helmet}"/>')
        out.append(f'<path d="M{cx},{hy - 62 * s} V{hy - 14 * s}" stroke="#000" stroke-opacity=".12" stroke-width="{8 * s}"/>')
        return "".join(out)

    p.append(person(230, 600, .95, "#2E5283", GOLD))
    p.append(person(570, 600, .95, "#2E5283", "#F2F4F7"))
    p.append(person(400, 600, 1.12, "#3A6399", GOLD2))
    p.append('</svg>\n')
    return "".join(p)


save("foto-tim.svg", team())
print("OK")
