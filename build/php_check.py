"""Hruba kontrola PHP suborov bez PHP interpretera.

Kontroluje: parovanie (), [], {} v PHP kode, alternativnu syntax (if:/endif; ...),
neuzavrete retazce/komentare a volania funkcii temy, ktore nie su nikde definovane.
Nenahradza `php -l`, ale zachyti typicke preklepy.
"""
import os
import re
import sys

ROOT = os.path.join(os.path.dirname(__file__), "..", "ascensio-theme")


def php_segments(src):
    """Vrati PHP kod (HTML mimo <?php ?> nahradi medzerou)."""
    out, i = [], 0
    while True:
        a = src.find("<?php", i)
        if a < 0:
            break
        b = src.find("?>", a + 5)
        seg = src[a + 5: b if b >= 0 else len(src)]
        out.append(seg)
        if b < 0:
            break
        i = b + 2
    return "\n;\n".join(out)


def strip(code, fname):
    res, i, n, errors = [], 0, len(code), []
    while i < n:
        c = code[i]
        if code.startswith("//", i) or c == "#":
            j = code.find("\n", i)
            i = n if j < 0 else j
            continue
        if code.startswith("/*", i):
            j = code.find("*/", i + 2)
            if j < 0:
                errors.append("neuzavrety komentar")
                break
            i = j + 2
            continue
        if c in "'\"":
            q, j = c, i + 1
            while j < n and code[j] != q:
                j += 2 if code[j] == "\\" else 1
            if j >= n:
                errors.append(f"neuzavrety retazec od znaku {i}: {code[i:i + 60]!r}")
                break
            res.append('""')
            i = j + 1
            continue
        res.append(c)
        i += 1
    return "".join(res), errors


def check(path):
    src = open(path, encoding="utf-8").read()
    code, errors = strip(php_segments(src), path)
    pairs = {")": "(", "]": "[", "}": "{"}
    stack = []
    for k, ch in enumerate(code):
        if ch in "([{":
            stack.append((ch, k))
        elif ch in ")]}":
            if not stack or stack[-1][0] != pairs[ch]:
                ctx = code[max(0, k - 80):k + 20].replace("\n", " ")
                errors.append(f"nesparovana zatvorka '{ch}' pri: …{ctx}…")
                break
            stack.pop()
    if stack and not errors:
        ch, k = stack[-1]
        errors.append(f"neuzavreta '{ch}' pri: …{code[k:k + 80]!r}…")
    for kw in ("if", "foreach", "while", "for"):
        opens = len(re.findall(r"\b" + kw + r"\s*\([^;{]*\)\s*:", code))
        closes = len(re.findall(r"\bend" + kw + r"\s*;", code))
        if opens != closes:
            errors.append(f"{kw}: {opens}× otvorene alternativnou syntaxou, {closes}× end{kw};")
    return code, errors


def main():
    defined, called = set(), {}
    all_ok = True
    for dirpath, _, files in os.walk(ROOT):
        for f in files:
            if not f.endswith(".php"):
                continue
            p = os.path.join(dirpath, f)
            code, errs = check(p)
            rel = os.path.relpath(p, ROOT)
            for m in re.finditer(r"\bfunction\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\(", code):
                defined.add(m.group(1))
            for m in re.finditer(r"(?<![>:$\w])(asc_[a-zA-Z0-9_]+)\s*\(", code):
                called.setdefault(m.group(1), set()).add(rel)
            if errs:
                all_ok = False
                for e in errs:
                    print(f"CHYBA {rel}: {e}")
    # funkcie pouzite ako callback v retazcoch (add_action, sanitize_callback...)
    src_all = ""
    for dirpath, _, files in os.walk(ROOT):
        for f in files:
            if f.endswith(".php"):
                src_all += open(os.path.join(dirpath, f), encoding="utf-8").read()
    for m in re.finditer(r"'(asc_[a-z0-9_]+)'", src_all):
        name = m.group(1)
        if re.search(r"add_(action|filter|shortcode|theme_page|meta_box)|callback|fallback_cb", src_all[max(0, m.start() - 120):m.start()]):
            called.setdefault(name, set()).add("(callback)")
    # identifikatory (typy obsahu, meta boxy), ktore nie su funkcie
    ids = set(re.findall(r"add_meta_box\(\s*'([a-z0-9_]+)'", src_all))
    ids |= set(re.findall(r"register_post_type\(\s*'([a-z0-9_]+)'", src_all))
    missing = {k: v for k, v in called.items() if k not in defined and k not in ids}
    for k, v in sorted(missing.items()):
        all_ok = False
        print(f"CHYBA: funkcia {k}() sa pouziva ({', '.join(sorted(v))}), ale nie je definovana")
    print("OK – bez nalezov" if all_ok else "Najdene problemy")
    return 0 if all_ok else 1


if __name__ == "__main__":
    sys.exit(main())
