"""Tests unitarios para rename_pdfs_rrhh."""
from __future__ import annotations

import logging
from pathlib import Path

import pytest

from rename_pdfs_rrhh import parse_new_name, rename_in_folder


class TestParseNewName:
    def test_patron_estandar(self) -> None:
        assert parse_new_name("600387-202603-CAT-MENSUAL.pdf") == "0387-202603-CAT-MENSUAL.pdf"

    def test_varias_variantes_numericas(self) -> None:
        assert parse_new_name("609011-202603-X.pdf") == "9011-202603-X.pdf"
        assert parse_new_name("605143-202603-Y.pdf") == "5143-202603-Y.pdf"

    def test_extension_mayusculas(self) -> None:
        assert parse_new_name("600387-202603-X.PDF") == "0387-202603-X.PDF"

    def test_no_aplica_si_no_es_pdf(self) -> None:
        assert parse_new_name("600387-202603-CAT.txt") is None

    def test_no_aplica_si_no_empieza_en_60(self) -> None:
        assert parse_new_name("700387-202603-CAT.pdf") is None
        assert parse_new_name("060387-202603-CAT.pdf") is None

    def test_no_aplica_con_pocos_digitos(self) -> None:
        assert parse_new_name("60038-202603-CAT.pdf") is None

    def test_no_aplica_sin_guion_luego_de_digitos(self) -> None:
        assert parse_new_name("600387_202603.pdf") is None

    def test_no_aplica_si_solo_60_y_extension(self) -> None:
        assert parse_new_name("60.pdf") is None


@pytest.fixture
def logger() -> logging.Logger:
    lg = logging.getLogger("test_rename_rrhh")
    lg.handlers.clear()
    lg.addHandler(logging.NullHandler())
    lg.propagate = False
    return lg


class TestRenameInFolder:
    def test_renombra_solo_los_que_matchean(self, tmp_path: Path, logger: logging.Logger) -> None:
        (tmp_path / "600387-202603-X.pdf").write_bytes(b"%PDF")
        (tmp_path / "700999-202603-X.pdf").write_bytes(b"%PDF")
        (tmp_path / "otro.pdf").write_bytes(b"%PDF")

        summary = rename_in_folder(tmp_path, dry_run=False, recursive=False, logger=logger)

        assert summary.scanned == 3
        assert summary.renamed == 1
        assert summary.skipped_no_match == 2
        assert summary.errors == 0
        assert (tmp_path / "0387-202603-X.pdf").exists()
        assert not (tmp_path / "600387-202603-X.pdf").exists()
        assert (tmp_path / "700999-202603-X.pdf").exists()

    def test_dry_run_no_modifica_filesystem(
        self, tmp_path: Path, logger: logging.Logger
    ) -> None:
        src = tmp_path / "600387-202603-X.pdf"
        src.write_bytes(b"%PDF")

        summary = rename_in_folder(tmp_path, dry_run=True, recursive=False, logger=logger)

        assert summary.renamed == 1
        assert summary.dry_run is True
        assert src.exists()
        assert not (tmp_path / "0387-202603-X.pdf").exists()

    def test_saltea_si_destino_existe(self, tmp_path: Path, logger: logging.Logger) -> None:
        (tmp_path / "600387-202603-X.pdf").write_bytes(b"origen")
        (tmp_path / "0387-202603-X.pdf").write_bytes(b"destino")

        summary = rename_in_folder(tmp_path, dry_run=False, recursive=False, logger=logger)

        assert summary.renamed == 0
        assert summary.skipped_dest_exists == 1
        assert (tmp_path / "600387-202603-X.pdf").read_bytes() == b"origen"
        assert (tmp_path / "0387-202603-X.pdf").read_bytes() == b"destino"

    def test_recursivo_procesa_subcarpetas(
        self, tmp_path: Path, logger: logging.Logger
    ) -> None:
        sub = tmp_path / "sub"
        sub.mkdir()
        (sub / "600387-202603-X.pdf").write_bytes(b"%PDF")

        summary = rename_in_folder(tmp_path, dry_run=False, recursive=True, logger=logger)

        assert summary.renamed == 1
        assert (sub / "0387-202603-X.pdf").exists()

    def test_no_recursivo_ignora_subcarpetas(
        self, tmp_path: Path, logger: logging.Logger
    ) -> None:
        sub = tmp_path / "sub"
        sub.mkdir()
        (sub / "600387-202603-X.pdf").write_bytes(b"%PDF")

        summary = rename_in_folder(tmp_path, dry_run=False, recursive=False, logger=logger)

        assert summary.scanned == 0
        assert (sub / "600387-202603-X.pdf").exists()

    def test_carpeta_vacia(self, tmp_path: Path, logger: logging.Logger) -> None:
        summary = rename_in_folder(tmp_path, dry_run=False, recursive=True, logger=logger)
        assert summary.scanned == 0
        assert summary.renamed == 0
