"""Deterministic Pelican theme ZIP builder. Never installs or modifies Panel."""
import argparse
import hashlib
import json
import re
from pathlib import Path
from zipfile import ZIP_DEFLATED, ZipFile, ZipInfo

ROOT = Path(__file__).resolve().parents[1]
RUNTIME = [
    "plugin.json", "src/AuroraGlassPlugin.php",
    "config/aurora-glass.php", "resources/css/theme.css",
    "README.md", "CHANGELOG.md", "LICENSE",
]
SOURCE_FOLDERS = ("docs", "tests", "tools", ".github")
SOURCE_FILES = (".gitattributes", ".gitignore")

def checksum(path: Path) -> str:
    return hashlib.sha256(path.read_bytes()).hexdigest()

def write_zip(path: Path, files: dict[str, bytes]) -> None:
    with ZipFile(path, "w", compression=ZIP_DEFLATED, compresslevel=9) as archive:
        for relative, content in sorted(files.items()):
            info = ZipInfo("aurora-glass/" + relative, (2026, 11, 14, 0, 0, 0))
            info.compress_type = ZIP_DEFLATED
            info.create_system = 3
            info.external_attr = 0o100644 << 16
            archive.writestr(info, content, compress_type=ZIP_DEFLATED, compresslevel=9)
    with ZipFile(path) as archive:
        names = archive.namelist()
        assert archive.testzip() is None
        assert all(name.startswith("aurora-glass/") and ".." not in name for name in names)
        assert "aurora-glass/plugin.json" in names
        assert "meta" not in json.loads(archive.read("aurora-glass/plugin.json"))

def build(repository: str | None) -> None:
    plugin = json.loads((ROOT / "plugin.json").read_text(encoding="utf8"))
    assert plugin["id"] == "aurora-glass"
    assert plugin["category"] == "theme"
    assert plugin["api_version"] == 1
    assert plugin["version"] == "0.5.0"
    assert plugin["panel_version"] == "1.0.0-rc.3"
    assert "meta" not in plugin
    assert (ROOT / "resources/css/theme.css").is_file()

    runtime = {name: (ROOT / name).read_bytes() for name in RUNTIME}
    source = dict(runtime)
    for folder in SOURCE_FOLDERS:
        for file in (ROOT / folder).rglob("*"):
            if file.is_file() and not file.is_symlink() and "__pycache__" not in file.parts:
                source[file.relative_to(ROOT).as_posix()] = file.read_bytes()
    for name in SOURCE_FILES:
        if (ROOT / name).is_file():
            source[name] = (ROOT / name).read_bytes()
    output = ROOT / "build"
    output.mkdir(exist_ok=True)
    version = plugin["version"]
    archives = []
    for suffix, files in (("", runtime), ("-source", source)):
        archive = output / f"Aurora-Glass-{version}{suffix}.zip"
        write_zip(archive, files)
        archives.append(archive)
        print(f"{archive.name}: {len(files)} files; SHA256 {checksum(archive)}")
    (output / "SHA256SUMS.txt").write_text(
        "".join(f"{checksum(path)}  {path.name}\n" for path in archives), encoding="ascii"
    )
    if repository:
        assert re.fullmatch(r"[a-zA-Z0-9_.-]+/[a-zA-Z0-9_.-]+", repository)
        feed = {
            plugin["panel_version"]: {
                "version": version,
                "download_url": f"https://github.com/{repository}/releases/download/v{version}/{archives[0].name}",
                "sha256": checksum(archives[0]),
            }
        }
        (output / "update.json").write_text(json.dumps(feed, indent=2) + "\n", encoding="utf8")

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--repository")
    build(parser.parse_args().repository)
