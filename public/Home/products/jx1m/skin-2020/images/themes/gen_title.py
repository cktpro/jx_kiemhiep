from PIL import Image, ImageDraw, ImageFont

W, H = 2160, 202  # 2x of 1080x101

# ---------- gradient helper ----------
def vgrad(w, h, top, bottom):
    img = Image.new('RGB', (1, h))
    for y in range(h):
        t = y / (h - 1)
        r = round(top[0] + (bottom[0] - top[0]) * t)
        g = round(top[1] + (bottom[1] - top[1]) * t)
        b = round(top[2] + (bottom[2] - top[2]) * t)
        img.putpixel((0, y), (r, g, b))
    return img.resize((w, h))

base = Image.new('RGBA', (W, H), (0, 0, 0, 0))

# ---------- ribbon shape (pennant ends) ----------
ribbon_pts = [
    (304, 20), (1856, 20), (1928, 101), (1856, 182),
    (304, 182), (376, 101)
]

mask = Image.new('L', (W, H), 0)
ImageDraw.Draw(mask).polygon(ribbon_pts, fill=255)

grad = vgrad(W, H, (162, 48, 48), (86, 12, 12))  # #a23030 -> #560c0c
ribbon_layer = Image.new('RGBA', (W, H), (0, 0, 0, 0))
ribbon_layer.paste(grad.convert('RGBA'), (0, 0), mask)
base.alpha_composite(ribbon_layer)

draw = ImageDraw.Draw(base)

GOLD = (255, 216, 119, 255)
GOLD_LT = (255, 246, 216, 255)
DARKRED = (74, 12, 12, 255)

# outer border
draw.polygon(ribbon_pts, outline=GOLD, width=5)

# inner thin border (slightly inset)
inner_pts = [
    (320, 33), (1840, 33), (1900, 101), (1840, 169),
    (320, 169), (376, 101)
]
draw.polygon(inner_pts, outline=(*GOLD[:3], 140), width=2)

# corner accents (L shapes) near both inner top/bottom corners
def corner(x, y, dx, dy):
    draw.line([(x, y), (x + dx, y)], fill=GOLD, width=4)
    draw.line([(x, y), (x, y + dy)], fill=GOLD, width=4)

corner(340, 48, 28, 0)
corner(340, 48, 0, 28)
corner(1820, 48, -28, 0)
corner(1820, 48, 0, 28)
corner(340, 154, 28, 0)
corner(340, 154, 0, -28)
corner(1820, 154, -28, 0)
corner(1820, 154, 0, -28)

# ---------- ornaments (medallion + cloud swirl) ----------
def cloud(cx, cy, mirror=False):
    s = 1 if not mirror else -1
    # cloud puff outline (series of circles)
    puffs = [(-92, 0, 26), (-60, -14, 22), (-26, -10, 18), (0, 0, 14)]
    for dx, dy, r in puffs:
        x = cx + s * dx
        draw.ellipse([x - r, cy + dy - r, x + r, cy + dy + r], outline=GOLD, width=3)
    # medallion circle
    draw.ellipse([cx - 28, cy - 28, cx + 28, cy + 28], fill=(122, 20, 20, 255), outline=GOLD, width=5)
    # diamond inside
    d = 16
    draw.polygon([(cx, cy - d), (cx + d, cy), (cx, cy + d), (cx - d, cy)], fill=GOLD)

cloud(152, 101, mirror=False)
cloud(2008, 101, mirror=True)

# decorative underline below text
draw.rectangle([660, 138, 1500, 141], fill=(*GOLD[:3], 160))

# ---------- title text ----------
text = "HỆ THỐNG MÔN PHÁI"
font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf"

# fit font size to available width
max_w = 1500 - 660  # match underline span roughly, with margin
size = 90
font = ImageFont.truetype(font_path, size)
while True:
    bbox = draw.textbbox((0, 0), text, font=font)
    tw = bbox[2] - bbox[0]
    if tw <= max_w or size <= 30:
        break
    size -= 2
    font = ImageFont.truetype(font_path, size)

bbox = draw.textbbox((0, 0), text, font=font)
tw, th = bbox[2] - bbox[0], bbox[3] - bbox[1]
tx = (W - tw) / 2 - bbox[0]
ty = (H - th) / 2 - bbox[1] - 6

draw.text((tx, ty), text, font=font, fill=GOLD, stroke_width=4, stroke_fill=DARKRED)

base.save("title-3-v2.png")
print("done", size, tw, th)
