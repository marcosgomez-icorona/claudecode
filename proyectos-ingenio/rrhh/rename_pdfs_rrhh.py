#!/usr/bin/env python3
"""
rename_pdfs_rrhh.py — Renombra recibos de RRHH quitando el prefijo "60".

Patrón origen: ^60\\d{4}-.*\\.pdf$    (ej: 600387-202603-CAT-...pdf)
Patrón destino: 0XXXX-...pdf           (ej: 0387-202603-CAT-...pdf)

Reglas:
- Solo renombra archivos que matcheen `^60\\d{4}-.*\\.pdf$` (case-insensitive).
- Si el archivo destino ya existe, se saltea con warning y NO se sobrescribe.
- Errores de permisos / SO se capturan y el proceso continúa.

Uso:
    python rename_pdfs_rrhh.py [--dry-run] [--folder /ruta] [--no-recursive]

Logs:
    ./logs/rename_rrhh_YYYYMMDD_HHMMSS.log  (JSON lines, uuid_operacion por corrida).
"""
from __future__ import annotations

import argparse
import json
import logging
import re
import sys
import uuid
from dataclasses import asdict, dataclass
from datetime import datetime, timezone
from pathlib import Path
from typing import Iterable, Optional

DEFAULT_FOLDER = Path("/mnt/c/claudecode/proyectos-ingenio/rrhh")
PREFIX_REGEX = re.compile(r"^60(\d{4}-.+\.pdf)$", re.IGNORECASE)
STRIP_LEN = 2


@dataclass
class Summary:
    scanned: int = 0
    renamed: int = 0
    skipped_no_match: int = 0
    skipped_dest_exists: int = 0
    errors: int = 0
    dry_run: bool = False

    def as_dict(self) -> dict:
        return asdict(self)


def parse_new_name(filename: str) -> Optional[str]:
    """Devuelve el nombre renombrado o None si el archivo no aplica."""
    if not PREFIX_REGEX.match(filename):
        return None
    return filename[STRIP_LEN:]


def iter_pdfs(folder: Path, recursive: bool) -> Iterable[Path]:
    pattern = "**/*.pdf" if recursive else "*.pdf"
    yield from sorted(p for p in folder.glob(pattern) if p.is_file())


def setup_logger(log_path: Path, uuid_op: str) -> logging.Logger:
    log_path.parent.mkdir(parents=True, exist_ok=True)

    class JsonFormatter(logging.Formatter):
        def format(self, record: logging.LogRecord) -> str:
            payload = {
                "ts": datetime.now(timezone.utc).isoformat(timespec="seconds"),
                "uuid_operacion": uuid_op,
                "level": record.levelname,
                "event": record.getMessage(),
            }
            extra = getattr(record, "extra_data", None)
            if isinstance(extra, dict):
                payload.update(extra)
            return json.dumps(payload, ensure_ascii=False)

    logger = logging.getLogger(f"rename_rrhh.{uuid_op}")
    logger.setLevel(logging.INFO)
    logger.handlers.clear()
    logger.propagate = False

    fh = logging.FileHandler(log_path, encoding="utf-8")
    fh.setFormatter(JsonFormatter())
    logger.addHandler(fh)

    sh = logging.StreamHandler(sys.stdout)
    sh.setFormatter(logging.Formatter("%(levelname)s %(message)s"))
    logger.addHandler(sh)
    return logger


def _log(logger: logging.Logger, level: int, event: str, **extra: object) -> None:
    logger.log(level, event, extra={"extra_data": extra})


def rename_in_folder(
    folder: Path,
    *,
    dry_run: bool,
    recursive: bool,
    logger: logging.Logger,
) -> Summary:
    summary = Summary(dry_run=dry_run)

    for pdf in iter_pdfs(folder, recursive):
        summary.scanned += 1
        new_name = parse_new_name(pdf.name)

        if new_name is None:
            summary.skipped_no_match += 1
            _log(logger, logging.INFO, "skip_no_match", archivo=str(pdf))
            continue

        target = pdf.with_name(new_name)
        if target.exists():
            summary.skipped_dest_exists += 1
            _log(
                logger,
                logging.WARNING,
                "skip_dest_exists",
                origen=str(pdf),
                destino=str(target),
            )
            continue

        if dry_run:
            summary.renamed += 1
            _log(
                logger,
                logging.INFO,
                "dry_run_rename",
                origen=str(pdf),
                destino=str(target),
            )
            continue

        try:
            pdf.rename(target)
            summary.renamed += 1
            _log(logger, logging.INFO, "renamed", origen=str(pdf), destino=str(target))
        except (PermissionError, OSError) as exc:
            summary.errors += 1
            _log(
                logger,
                logging.ERROR,
                "rename_error",
                origen=str(pdf),
                destino=str(target),
                error=str(exc),
                error_type=type(exc).__name__,
            )

    return summary


def main(argv: Optional[list[str]] = None) -> int:
    parser = argparse.ArgumentParser(
        description="Renombra PDFs de RRHH quitando el prefijo '60'."
    )
    parser.add_argument(
        "--folder",
        type=Path,
        default=DEFAULT_FOLDER,
        help=f"Carpeta a procesar (default: {DEFAULT_FOLDER})",
    )
    parser.add_argument(
        "--dry-run",
        action="store_true",
        help="Solo simula, no renombra nada",
    )
    parser.add_argument(
        "--no-recursive",
        action="store_true",
        help="No descender a subcarpetas",
    )
    args = parser.parse_args(argv)

    if not args.folder.is_dir():
        print(f"ERROR: la carpeta no existe o no es un directorio: {args.folder}", file=sys.stderr)
        return 2

    uuid_op = str(uuid.uuid4())
    ts = datetime.now().strftime("%Y%m%d_%H%M%S")
    log_path = Path(__file__).resolve().parent / "logs" / f"rename_rrhh_{ts}.log"
    logger = setup_logger(log_path, uuid_op)

    _log(
        logger,
        logging.INFO,
        "start",
        folder=str(args.folder),
        dry_run=args.dry_run,
        recursive=not args.no_recursive,
    )
    summary = rename_in_folder(
        args.folder,
        dry_run=args.dry_run,
        recursive=not args.no_recursive,
        logger=logger,
    )
    _log(logger, logging.INFO, "summary", **summary.as_dict())

    print("\n--- Resumen ---")
    for key, value in summary.as_dict().items():
        print(f"  {key}: {value}")
    print(f"\nLog: {log_path}")
    return 0 if summary.errors == 0 else 1


if __name__ == "__main__":
    raise SystemExit(main())
