"""Prida prefix `body ` k selektorom temy, ktore ciela na Elementor prvky.

Dovod: Elementor 4 nacitava CSS widgetov az po CSS temy, takze pri rovnakej
specifickosti vyhrava Elementor. Prefix `body` prida jeden typovy selektor
(0,0,1) – tema prebije zakladne styly Elementoru, ale nastavenia konkretneho
prvku z panelu Elementoru (.elementor-N .elementor-element-xyz …) maju stale prednost.
Skript je idempotentny.
"""
import os
import re

CSS = os.path.join(os.path.dirname(__file__), "..", "ascensio-theme", "assets", "css", "main.css")
TARGET = re.compile(r"\.(elementor-widget|e-con|elementor-heading-title|elementor-icon|elementor-button)")

src = open(CSS, encoding="utf-8").read()
out_lines, stack, changed = [], [], 0
pending_selector = False

for line in src.split("\n"):
    stripped = line.strip()
    at_selector_level = not stack or stack[-1] != "rule"
    if at_selector_level and stripped and not stripped.startswith(("@", "/*", "*", "}")) and (
            "{" in stripped or stripped.endswith(",")):
        sel, sep, rest = line.partition("{")
        indent = sel[: len(sel) - len(sel.lstrip())]
        parts = sel.strip().split(",")
        new_parts = []
        for p in parts:
            ps = p.strip()
            if ps and TARGET.search(ps) and not ps.startswith(("body", "html", ":root")):
                ps = "body " + ps
                changed += 1
            new_parts.append(ps)
        joined = ", ".join(x for x in new_parts if x) + ("," if sel.strip().endswith(",") else "")
        line = indent + joined + (" " + sep + rest if sep else "")
    # sledovanie zatvoriek
    for ch in line:
        if ch == "{":
            stack.append("media" if stripped.startswith("@") and not stack or (stripped.startswith("@") and stack and stack[-1] != "rule") else "rule")
        elif ch == "}":
            if stack:
                stack.pop()
    out_lines.append(line)

open(CSS, "w", encoding="utf-8", newline="\n").write("\n".join(out_lines))
print("prefixovanych selektorov:", changed, "| zostatok zatvoriek:", len(stack))
