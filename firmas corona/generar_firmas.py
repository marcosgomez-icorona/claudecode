"""
Generador de firmas de email — Ingenio La Corona
Produce JPG con QR por persona
"""

import os
import fitz  # PyMuPDF — renderiza el .ai para íconos
import qrcode
import openpyxl
from PIL import Image, ImageDraw, ImageFont
from reportlab.lib.pagesizes import letter
from reportlab.pdfgen import canvas as rl_canvas
from reportlab.lib.units import inch

# ─── Rutas ────────────────────────────────────────────────────────────────────
BASE         = '/mnt/c/claudecode/firmas corona'
OUTPUT       = os.path.join(BASE, 'output')
AI_PATH      = os.path.join(BASE, 'firma_lacorona.ai')
LOGO_PATH    = os.path.join(BASE, 'logo corona.jpeg')
LISTADO_PATH = os.path.join(BASE, 'listado_firmas.xlsx')
AI_RENDER    = '/tmp/firma_ai_render.png'

FONT_PATH_REG  = '/usr/share/fonts/truetype/ubuntu/UbuntuSans[wdth,wght].ttf'
FONT_PATH_BOLD = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'

# ─── Diseño ───────────────────────────────────────────────────────────────────
W, H  = 1280, 354
RED   = (197, 21, 32)
WHITE = (255, 255, 255)

X_TOP  = 715
X_BOT  = 497
SLOPE  = (X_TOP - X_BOT) / H

QR_X_START = 955
QR_SIZE    = 285
QR_MARGIN  = (H - QR_SIZE) // 2

LOGO_CX = 835
LOGO_CY = H // 2

ICON_X        = 194
TEXT_X        = 234
FIELD_SPACING = 35

# ─── Arcos (curvatura hacia la izquierda, forma «(») ─────────────────────────
ARC_CX    = 300
ARC_CY    = H // 2
ARC_RADII = [230, 265, 300]
ARC_WIDTH = 5
ARC_START = 110
ARC_END   = 250


# ─── Renderizar .ai para íconos ───────────────────────────────────────────────
def ensure_ai_render():
    if not os.path.exists(AI_RENDER):
        print("  Renderizando .ai...")
        doc = fitz.open(AI_PATH)
        pix = doc[0].get_pixmap(matrix=fitz.Matrix(3, 3))  # → 2575×986
        pix.save(AI_RENDER)


# ─── Cargar fuentes ───────────────────────────────────────────────────────────
def load_fonts():
    f_field = ImageFont.truetype(FONT_PATH_REG, 20)
    return f_field


# ─── Cargar íconos ────────────────────────────────────────────────────────────
def load_icons():
    ai = Image.open(AI_RENDER)
    # Radio 48 < mitad del spacing mínimo (100px) → sin solapamiento entre crops
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
        pixels = list(rgba.getdata())
        clean = []
        for rv, gv, bv, av in pixels:
            clean.append((rv, gv, bv, 0) if rv > 130 and gv < 80 and bv < 80 else (rv, gv, bv, av))
        rgba.putdata(clean)
        icons[name] = rgba.resize((33, 33), Image.LANCZOS)
    return icons


# ─── Cargar logo ──────────────────────────────────────────────────────────────
def load_logo():
    return Image.open(LOGO_PATH).convert('RGB')


# ─── Leer personas desde xlsx ─────────────────────────────────────────────────
def cargar_datos():
    wb = openpyxl.load_workbook(LISTADO_PATH, read_only=True)
    ws = wb.active
    personas = []
    for r in list(ws.iter_rows(values_only=True))[1:]:
        if not r[0] or not r[1] or '@' not in str(r[1]):
            continue
        personas.append({
            'nombre':   str(r[0]).strip(),
            'email':    str(r[1]).strip().lower(),
            'cargo':    str(r[2]).strip() if r[2] else '',
            'telefono': str(r[3]).strip() if r[3] else '',
        })
    return personas


# ─── QR vCard con nombre, email y teléfono ───────────────────────────────────
def gen_qr(nombre, email, telefono=''):
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
    for r in ARC_RADII:
        bbox = [ARC_CX - r, ARC_CY - r, ARC_CX + r, ARC_CY + r]
        draw.arc(bbox, start=ARC_START, end=ARC_END, fill=WHITE, width=ARC_WIDTH)


# ─── Logo en área blanca ──────────────────────────────────────────────────────
def place_logo(img, logo_raw, size):
    logo = logo_raw.resize((size, size), Image.LANCZOS)
    img.paste(logo, (LOGO_CX - size // 2, LOGO_CY - size // 2))


# ─── Render de firma ─────────────────────────────────────────────────────────
def render_firma(persona, icons, logo_raw, f_field) -> Image.Image:
    nombre   = persona['nombre']
    cargo    = persona['cargo']
    email    = persona['email']
    telefono = persona['telefono']

    img  = Image.new('RGB', (W, H), RED)
    draw = ImageDraw.Draw(img)

    # Polígono blanco
    draw.polygon([(X_TOP, 0), (QR_X_START, 0), (QR_X_START, H), (X_BOT, H)], fill=WHITE)

    # Arcos decorativos
    draw_arcs(draw)

    # Logo
    place_logo(img, logo_raw, size=230)

    # QR
    qr_img = gen_qr(nombre, email, telefono).resize((QR_SIZE, QR_SIZE), Image.LANCZOS)
    qr_x   = QR_X_START + (W - QR_X_START - QR_SIZE) // 2
    img.paste(qr_img, (qr_x, QR_MARGIN))

    # Lista de campos (nombre primero, uniforme con el resto)
    fields = [('persona', nombre)]
    if cargo:
        fields.append(('cargo', cargo))
    fields.append(('email', email))
    if telefono:
        fields.append(('whatsapp', telefono))
    fields.append(('instagram', 'ingenio.la.corona'))
    fields.append(('facebook',  'ingenio-lc'))
    fields.append(('web',       'www.ingeniolacorona.com'))

    # Espaciado auto-centrado
    icon_h    = 33
    content_h = (len(fields) - 1) * FIELD_SPACING + icon_h
    y_start   = max(18, (H - content_h) // 2)

    for i, (icon_key, text) in enumerate(fields):
        fy        = y_start + i * FIELD_SPACING
        max_txt_w = int(X_TOP - SLOPE * (fy + icon_h // 2)) - TEXT_X - 8

        icon_img = icons.get(icon_key)
        if icon_img:
            img.paste(icon_img, (ICON_X, fy + (icon_h - icon_img.height) // 2),
                      mask=icon_img.split()[3])

        txt = text
        if (f_field.getbbox(txt)[2] - f_field.getbbox(txt)[0]) > max_txt_w:
            while txt and (f_field.getbbox(txt + '…')[2] - f_field.getbbox(txt + '…')[0]) > max_txt_w:
                txt = txt[:-1]
            txt += '…'

        txt_h = f_field.getbbox(txt)[3] - f_field.getbbox(txt)[1]
        draw.text((TEXT_X, fy + (icon_h - txt_h) // 2), txt, fill=WHITE, font=f_field)

    return img


# ─── Main ─────────────────────────────────────────────────────────────────────
def main():
    print("Cargando recursos...")
    ensure_ai_render()
    f_field  = load_fonts()
    icons    = load_icons()
    logo_raw = load_logo()
    personas = cargar_datos()
    print(f"Personas: {len(personas)}")

    os.makedirs(OUTPUT, exist_ok=True)
    ok = err = 0

    for p in personas:
        nombre      = p['nombre']
        folder_name = nombre.replace(' ', '_').replace('/', '-')
        folder      = os.path.join(OUTPUT, folder_name)
        os.makedirs(folder, exist_ok=True)
        jpg_path = os.path.join(folder, f'{folder_name}_con_qr.jpg')
        try:
            img = render_firma(p, icons, logo_raw, f_field)
            img.save(jpg_path, 'JPEG', quality=95)
            print(f"  ✓ {nombre}")
            ok += 1
        except Exception as e:
            print(f"  ✗ {nombre}: {e}")
            err += 1

    print(f"\nListo: {ok}/{len(personas)} firmas → {OUTPUT}")
    if err:
        print(f"  Errores: {err}")


if __name__ == '__main__':
    main()
