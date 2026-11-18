"""Zabali temu do ascensio-theme.zip (korenovy priecinok v ZIPe = 'ascensio')."""
import os
import zipfile

ROOT = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
SRC = os.path.join(ROOT, "ascensio-theme")
DST = os.path.join(ROOT, "ascensio-theme.zip")

count = 0
with zipfile.ZipFile(DST, "w", zipfile.ZIP_DEFLATED) as z:
    for dirpath, _, files in os.walk(SRC):
        for f in sorted(files):
            full = os.path.join(dirpath, f)
            rel = os.path.relpath(full, SRC).replace(os.sep, "/")
            z.write(full, "ascensio/" + rel)
            count += 1
print(f"{DST} – {count} suborov, {os.path.getsize(DST) / 1024:.0f} kB")
