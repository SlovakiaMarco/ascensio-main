"""Pomocne funkcie na skladanie Elementor dat (kontajnery + widgety).

Vystup je kompatibilny s formatom `_elementor_data` (Elementor 3.x, flexbox/grid kontajnery).
"""
import random

_rng = random.Random(20261118)


def eid():
    return format(_rng.getrandbits(28), "07x")


def reset_ids(seed):
    _rng.seed(seed)


# ---------- jednotky ----------

def px(v, unit="px"):
    return {"unit": unit, "size": v, "sizes": []}


def dims(t, r=None, b=None, l=None, unit="px"):
    r = t if r is None else r
    b = t if b is None else b
    l = r if l is None else l
    return {"unit": unit, "top": str(t), "right": str(r), "bottom": str(b), "left": str(l),
            "isLinked": len({t, r, b, l}) == 1}


def gaps(v):
    return {"unit": "px", "size": v, "column": str(v), "row": str(v), "isLinked": True}


ZERO = dims(0)

GLOBAL = {
    "navy": "globals/colors?id=primary",
    "gold": "globals/colors?id=secondary",
    "sand": "globals/colors?id=ascsand",
    "navy2": "globals/colors?id=ascnavy2",
    "white": "globals/colors?id=ascwhite",
}
HEX = {"navy": "#14263F", "gold": "#C9A04E", "sand": "#F5F2EB", "navy2": "#0C1A2E", "white": "#FFFFFF"}


# ---------- kontajnery ----------

def con(children=None, *, cls="", direction="column", boxed=False, gap=None, pad=None, pad_t=None,
        pad_m=None, bg=None, width=None, width_t=None, width_m=None, align=None, justify=None,
        wrap=False, anchor=None, tag=None, inner=True, direction_t=None, direction_m=None,
        align_m=None, extra=None):
    s = {
        "content_width": "boxed" if boxed else "full",
        "flex_direction": direction,
    }
    if direction_t:
        s["flex_direction_tablet"] = direction_t
    if direction_m:
        s["flex_direction_mobile"] = direction_m
    if gap is not None:
        s["flex_gap"] = gaps(gap)
    s["padding"] = pad if pad is not None else ZERO
    if pad_t is not None:
        s["padding_tablet"] = pad_t
    if pad_m is not None:
        s["padding_mobile"] = pad_m
    if bg:
        s["background_background"] = "classic"
        s["background_color"] = HEX[bg]
        s["__globals__"] = {"background_color": GLOBAL[bg]}
    if width is not None:
        s["width"] = px(width, "%")
    if width_t is not None:
        s["width_tablet"] = px(width_t, "%")
    if width_m is not None:
        s["width_mobile"] = px(width_m, "%")
    if align:
        s["flex_align_items"] = align
    if align_m:
        s["flex_align_items_mobile"] = align_m
    if justify:
        s["flex_justify_content"] = justify
    if wrap:
        s["flex_wrap"] = "wrap"
    if cls:
        s["css_classes"] = cls
    if anchor:
        s["_element_id"] = anchor
    if tag:
        s["html_tag"] = tag
    if extra:
        s.update(extra)
    return {"id": eid(), "elType": "container", "isInner": inner, "settings": s,
            "elements": children or []}


def section(children, *, cls="", bg=None, pad=(96, 96), pad_m=(64, 64), anchor=None, gap=40,
            align=None):
    return con(children, cls=("asc-s " + cls).strip(), boxed=True, gap=gap, bg=bg,
               pad=dims(pad[0], 24, pad[1], 24), pad_m=dims(pad_m[0], 20, pad_m[1], 20),
               anchor=anchor, tag="section", inner=False, align=align)


def row(children, *, gap=48, align="center", cls="", direction_t="column", justify=None):
    return con(children, direction="row", gap=gap, align=align, cls=cls,
               direction_t=direction_t, direction_m="column", justify=justify)


def col(children, *, width=50, gap=20, cls="", align=None, width_t=100):
    return con(children, gap=gap, width=width, width_t=width_t, width_m=100, cls=cls, align=align)


def grid(children, *, cols=3, cols_t=2, cols_m=1, gap=24, cls=""):
    n = max(len(children), 1)
    s = {
        "container_type": "grid",
        "content_width": "full",
        "grid_columns_grid": px(cols, "fr"),
        "grid_columns_grid_tablet": px(cols_t, "fr"),
        "grid_columns_grid_mobile": px(cols_m, "fr"),
        "grid_rows_grid": px(-(-n // cols), "fr"),
        "grid_rows_grid_tablet": px(-(-n // cols_t), "fr"),
        "grid_rows_grid_mobile": px(-(-n // cols_m), "fr"),
        "grid_gaps": gaps(gap),
        "grid_auto_flow": "row",
        "padding": ZERO,
        # triedy asc-cols-* su CSS poistka v teme, keby sa grid nevyrenderoval
        "css_classes": f"asc-grid asc-cols-{cols} asc-cols-t-{cols_t} asc-cols-m-{cols_m} {cls}".strip(),
    }
    return {"id": eid(), "elType": "container", "isInner": True, "settings": s, "elements": children}


# ---------- widgety ----------

def widget(wtype, settings, cls=""):
    if cls:
        settings["_css_classes"] = cls
    return {"id": eid(), "elType": "widget", "widgetType": wtype, "isInner": False,
            "settings": settings, "elements": []}


def heading(text, tag="h2", cls="", align=None, link=None):
    s = {"title": text, "header_size": tag}
    if align:
        s["align"] = align
    if link:
        s["link"] = {"url": link, "is_external": "", "nofollow": ""}
    return widget("heading", s, cls)


def eyebrow(text, cls="", align=None):
    return heading(text, "div", ("asc-eyebrow " + cls).strip(), align)


def text(html, cls="", align=None):
    s = {"editor": html}
    if align:
        s["align"] = align
    return widget("text-editor", s, cls)


def button(label, url, variant="gold", size="lg", icon=None, align=None, cls=""):
    s = {"text": label, "link": {"url": url, "is_external": "", "nofollow": ""}, "size": size}
    if align:
        s["align"] = align
    if icon:
        s["selected_icon"] = fa(icon)
        if icon.startswith("arrow"):
            cls = (cls + " asc-ebtn--icon-end").strip()
            s["icon_align"] = "row-reverse"  # Elementor 3.24+ / 4.x: ikona za textom
    return widget("button", s, ("asc-ebtn asc-ebtn--" + variant + " " + cls).strip())


def fa(name, lib="fa-solid"):
    prefix = {"fa-solid": "fas", "fa-regular": "far", "fa-brands": "fab"}[lib]
    return {"value": f"{prefix} fa-{name}", "library": lib}


def icon_box(icon, title, desc, link=None, cls="asc-iconcard", tag="h3"):
    s = {"selected_icon": fa(icon), "title_text": title, "description_text": desc, "title_size": tag,
         "text_align": "start"}
    if "asc-benefit--row" in cls:
        s["position"] = "inline-start"  # Elementor 4: ikona vľavo od textu
    if link:
        s["link"] = {"url": link, "is_external": "", "nofollow": ""}
    return widget("icon-box", s, cls)


def icon_list(items, inline=False, cls="asc-checks", icon="check"):
    lst = []
    for it in items:
        if isinstance(it, tuple):
            label, link, ic = (list(it) + [None, None])[:3]
        else:
            label, link, ic = it, None, None
        entry = {"_id": eid(), "text": label, "selected_icon": fa(ic or icon)}
        if link:
            entry["link"] = {"url": link, "is_external": "", "nofollow": ""}
        lst.append(entry)
    s = {"icon_list": lst}
    if inline:
        s["view"] = "inline"
    return widget("icon-list", s, cls)


def counter(end, title, suffix="", sep=" ", cls="asc-counter"):
    s = {"starting_number": 0, "ending_number": end, "suffix": suffix, "title": title,
         "duration": 1800}
    if sep is None:
        s["thousand_separator"] = ""
    else:
        s["thousand_separator"] = "yes"
        s["thousand_separator_char"] = sep
    return widget("counter", s, cls)


def accordion(items, cls="asc-faq"):
    tabs = [{"_id": eid(), "tab_title": q, "tab_content": a} for q, a in items]
    return widget("accordion", {"tabs": tabs, "selected_icon": fa("plus"),
                                "selected_active_icon": fa("minus"), "title_html_tag": "h3"}, cls)


def image(url, alt="", cls="asc-img"):
    return widget("image", {"image": {"url": url, "id": "", "alt": alt, "source": "library"},
                            "image_size": "full"}, cls)


def gmap(address, height=380, cls="asc-map"):
    return widget("google_maps", {"address": address, "zoom": px(15), "height": px(height)}, cls)


def jobs(count=50, filters=True, columns="3", featured=False, button="Zobraziť ponuku"):
    return widget("asc-jobs", {"count": count, "filters": "yes" if filters else "",
                               "columns": columns, "only_featured": "yes" if featured else "",
                               "button_text": button})


def apply_form(variant="full", title="", subtitle="", button="Odoslať prihlášku", lang="sk",
               theme="light", cls=""):
    return widget("asc-apply", {"variant": variant, "title": title, "subtitle": subtitle,
                                "button_text": button, "lang": lang, "theme": theme}, cls)
