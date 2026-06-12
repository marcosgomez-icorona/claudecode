"""
ajuste_firma.py — Versión de ajuste de diseño
Solo genera Marcos Javier Gomez → /ajuste firma/
"""

import os
import math
import fitz  # PyMuPDF — renderiza el .ai
import qrcode
from PIL import Image, ImageDraw, ImageFont
from reportlab.pdfgen import canvas as rl_canvas
from reportlab.lib.units import inch

BASE         = '/mnt/c/claudecode/firmas corona'
OUTPUT       = os.path.join(BASE, 'ajuste firma')
AI_PATH      = os.path.join(BASE, 'firma_lacorona.ai')
LOGO_PATH    = os.path.join(BASE, 'logo corona.jpeg')
LISTADO_PATH = os.path.join(BASE, 'listado_firmas.xlsx')

FONT_PATH_REG  = '/usr/share/fonts/truetype/ubuntu/UbuntuSans[wdth,wght].ttf'
FONT_PATH_BOLD = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'

# ─── Dimensiones ──────────────────────────────────────────────────────────────
W, H  = 1280, 354
RED   = (197, 21, 32)
WHITE = (255, 255, 255)

# ─── Diagonal roja/blanca ─────────────────────────────────────────────────────
X_TOP  = 715   # x de la diagonal en y=0
X_BOT  = 497   # x de la diagonal en y=354
SLOPE  = (X_TOP - X_BOT) / H

# ─── QR ───────────────────────────────────────────────────────────────────────
QR_X_START = 955
QR_SIZE    = 285
QR_MARGIN  = (H - QR_SIZE) // 2

# ─── Logo ─────────────────────────────────────────────────────────────────────
LOGO_CX_SIN_QR = 1000
LOGO_CX_CON_QR = 835
LOGO_CY        = H // 2

# ─── Texto ────────────────────────────────────────────────────────────────────
NAME_X, NAME_Y = 228, 48
ICON_X         = 194
TEXT_X         = 234
FIELD_Y_START  = 128
FIELD_SPACING  = 35

# ─── Arcos (AJUSTADOS) ────────────────────────────────────────────────────────
# Centro a la DERECHA del arco visible → arcos con curvatura hacia la izquierda
# Forma "(" en lugar de ")" → no se superponen con el texto
ARC_CX     = 300
ARC_CY     = H // 2   # 177
ARC_RADII  = [230, 265, 300]
ARC_WIDTH  = 5
# Ángulos: dibuja el lado izquierdo del círculo, PIL clockwise desde la derecha (0°)
# 110° a 250° captura el arco izquierdo completo; PIL recorta en los bordes de la imagen
ARC_START  = 110
ARC_END    = 250


# ─── Renderizar .ai para íconos ───────────────────────────────────────────────
AI_RENDER_PATH = '/tmp/firma_ai_render.png'

def ensure_ai_render():
    if not os.path.exists(AI_RENDER_PATH):
        print("  Renderizando .ai para íconos...")
        doc  = fitz.open(AI_PATH)
        page = doc[0]
        pix  = page.get_pixmap(matrix=fitz.Matrix(3, 3))  # → 2575×986
        pix.save(AI_RENDER_PATH)
        print(f"  Render: {pix.width}×{pix.height}")


# ─── Cargar fuentes ───────────────────────────────────────────────────────────
def load_fonts():
    f_bold  = ImageFont.truetype(FONT_PATH_BOLD, 40)
    f_name  = ImageFont.truetype(FONT_PATH_BOLD, 38)
    f_field = ImageFont.truetype(FONT_PATH_REG,  20)
    return f_bold, f_name, f_field


# ─── Cargar íconos desde el render del .ai ───────────────────────────────────
def load_icons():
    ai = Image.open(AI_RENDER_PATH)
    # Centros de íconos en el render 2575×986
    # Radio 48 (< mitad del spacing mínimo de 100px) → sin solapamiento entre crops
    icon_defs = {
        'persona':   (476, 172, 48),
        'cargo':     (476, 272, 48),
        'email':     (476, 380, 48),
        'whatsapp':  (476, 487, 48),
        'instagram': (476, 600, 48),
        'facebook':  (476, 702, 48),
        'web':       (476, 805, 48),
    }
    icons = {}
    for name, (cx, cy, r) in icon_defs.items():
        crop = ai.crop((cx - r, cy - r, cx + r, cy + r))
        rgba = crop.convert('RGBA')
        data = rgba.getdata()
        new_data = []
        for rv, gv, bv, av in data:
            if rv > 130 and gv < 80 and bv < 80:
                new_data.append((rv, gv, bv, 0))   # fondo rojo → transparente
            else:
                new_data.append((rv, gv, bv, av))
        rgba.putdata(new_data)
        icons[name] = rgba.resize((33, 33), Image.LANCZOS)
    return icons


# ─── Cargar logo ──────────────────────────────────────────────────────────────
def load_logo():
    return Image.open(LOGO_PATH).convert('RGB')


# ─── QR vCard ─────────────────────────────────────────────────────────────────
def gen_qr_image(nombre, email, telefono=''):
    tel_line = f"TEL;TYPE=CELL:{telefono}\n" if telefono else ""
    vcard = (
        "BEGIN:VCARD\nVERSION:3.0\n"
        f"FN:{nombre}\nEMAIL:{email}\n"
        f"{tel_line}"
        "ORG:Ingenio La Corona\nEND:VCARD"
    )
    qr = qrcode.QRCode(box_size=9, border=2,
                       error_correction=qrcode.constants.ERROR_CORRECT_M)
    qr.add_data(vcard)
    qr.make(fit=True)
    return qr.make_image(fill_color='black', back_color='white').convert('RGB')


# ─── Arcos decorativos ────────────────────────────────────────────────────────
def draw_arcs(draw):
    """Arcos con curvatura hacia la izquierda (forma «(»), borde izquierdo."""
    for r in ARC_RADII:
        bbox = [ARC_CX - r, ARC_CY - r, ARC_CX + r, ARC_CY + r]
        draw.arc(bbox, start=ARC_START, end=ARC_END, fill=WHITE, width=ARC_WIDTH)


# ─── Logo en área blanca ──────────────────────────────────────────────────────
def place_logo(img, logo_raw, cx, size):
    logo = logo_raw.resize((size, size), Image.LANCZOS)
    x = cx - size // 2
    y = LOGO_CY - size // 2
    img.paste(logo, (x, y))


# ─── Auto-tamaño de nombre ────────────────────────────────────────────────────
def auto_font_size(name, max_width, max_size=38, min_size=22):
    size = max_size
    while size >= min_size:
        fnt  = ImageFont.truetype(FONT_PATH_BOLD, size)
        bbox = fnt.getbbox(name)
        if (bbox[2] - bbox[0]) <= max_width:
            return fnt
        size -= 1
    return ImageFont.truetype(FONT_PATH_BOLD, min_size)


def diagonal_x_at(y):
    return X_TOP - SLOPE * y


# ─── Render principal ─────────────────────────────────────────────────────────
def render_firma(persona, icons, logo_raw, fonts, with_qr: bool) -> Image.Image:
    f_bold, f_name, f_field = fonts
    nombre   = persona['nombre']
    cargo    = persona['cargo']
    email    = persona['email']
    telefono = persona['telefono']

    img  = Image.new('RGB', (W, H), RED)
    draw = ImageDraw.Draw(img)

    # Polígono blanco
    if with_qr:
        white_poly = [(X_TOP, 0), (QR_X_START, 0), (QR_X_START, H), (X_BOT, H)]
        logo_size  = 230
        logo_cx    = LOGO_CX_CON_QR
    else:
        white_poly = [(X_TOP, 0), (W, 0), (W, H), (X_BOT, H)]
        logo_size  = 270
        logo_cx    = LOGO_CX_SIN_QR

    draw.polygon(white_poly, fill=WHITE)

    # Arcos decorativos
    draw_arcs(draw)

    # Logo
    place_logo(img, logo_raw, logo_cx, logo_size)

    # QR — incluye nombre, email y teléfono en el vCard
    if with_qr:
        qr_img = gen_qr_image(nombre, email, telefono).resize((QR_SIZE, QR_SIZE), Image.LANCZOS)
        qr_x   = QR_X_START + (W - QR_X_START - QR_SIZE) // 2
        img.paste(qr_img, (qr_x, QR_MARGIN))

    # Todos los campos con ícono + texto uniform (mismo font y tamaño de ícono)
    # El nombre va primero como primer ítem de la lista
    fields = [('persona', nombre)]
    if cargo:
        fields.append(('cargo', cargo))
    fields.append(('email', email))
    if telefono:
        fields.append(('whatsapp', telefono))
    fields.append(('instagram', 'ingenio.la.corona'))
    fields.append(('facebook',  'ingenio-lc'))
    fields.append(('web',       'www.ingeniolacorona.com'))

    # Espaciado auto-centrado verticalmente
    n_fields   = len(fields)
    icon_h     = 33
    content_h  = (n_fields - 1) * FIELD_SPACING + icon_h
    y_start    = max(18, (H - content_h) // 2)
    spacing    = FIELD_SPACING

    for i, (icon_key, text) in enumerate(fields):
        fy        = y_start + i * spacing
        max_txt_w = int(diagonal_x_at(fy + icon_h // 2)) - TEXT_X - 8

        icon_img = icons.get(icon_key)
        if icon_img:
            iy = fy + (icon_h - icon_img.height) // 2
            img.paste(icon_img, (ICON_X, iy), mask=icon_img.split()[3])

        bbox = f_field.getbbox(text)
        txt_w = bbox[2] - bbox[0]
        display_text = text
        if txt_w > max_txt_w:
            while display_text and f_field.getbbox(display_text + '…')[2] > max_txt_w:
                display_text = display_text[:-1]
            display_text += '…'

        # Centrar texto verticalmente con el ícono
        txt_bbox = f_field.getbbox(display_text)
        txt_h    = txt_bbox[3] - txt_bbox[1]
        ty       = fy + (icon_h - txt_h) // 2
        draw.text((TEXT_X, ty), display_text, fill=WHITE, font=f_field)

    return img


# ─── Exportar PDF ─────────────────────────────────────────────────────────────
def save_pdf(img_pil: Image.Image, pdf_path: str):
    tmp_jpg = pdf_path.replace('.pdf', '_tmp.jpg')
    img_pil.save(tmp_jpg, 'JPEG', quality=95)
    dpi = 96
    pw  = W / dpi * inch
    ph  = H / dpi * inch
    c   = rl_canvas.Canvas(pdf_path, pagesize=(pw, ph))
    c.drawImage(tmp_jpg, 0, 0, width=pw, height=ph)
    c.save()
    os.remove(tmp_jpg)


# ─── Main ─────────────────────────────────────────────────────────────────────
def main():
    import openpyxl

    print("Cargando recursos...")
    ensure_ai_render()
    fonts    = load_fonts()
    icons    = load_icons()
    logo_raw = load_logo()

    # Leer solo Marcos Javier Gomez
    wb = openpyxl.load_workbook(LISTADO_PATH, read_only=True)
    ws = wb.active
    persona = None
    for r in list(ws.iter_rows(values_only=True))[1:]:
        if not r[0] or not r[1] or '@' not in str(r[1]):
            continue
        nombre = str(r[0]).strip()
        if 'Marcos' in nombre and 'Gomez' in nombre:
            persona = {
                'nombre':   nombre,
                'email':    str(r[1]).strip().lower(),
                'cargo':    str(r[2]).strip() if r[2] else '',
                'telefono': str(r[3]).strip() if r[3] else '',
            }
            break

    if not persona:
        print("ERROR: No se encontró Marcos Javier Gomez en el xlsx")
        return

    print(f"Persona: {persona['nombre']}")
    os.makedirs(OUTPUT, exist_ok=True)
    base = persona['nombre'].replace(' ', '_')

    # Solo con_qr en JPG para esta iteración de ajuste
    img      = render_firma(persona, icons, logo_raw, fonts, with_qr=True)
    jpg_path = os.path.join(OUTPUT, f'{base}_con_qr.jpg')
    img.save(jpg_path, 'JPEG', quality=95)
    print(f"  ✓ con_qr → {jpg_path}")

    print(f"\nListo → {OUTPUT}")


if __name__ == '__main__':
    main()
