from PIL import Image, ImageDraw, ImageFont

W, H = 2160, 202


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

GOLD = (255, 216, 119, 255)
GOLD_DK = (201, 162, 39, 255)
DARKRED = (74, 12, 12, 255)
RED = (122, 20, 20, 255)

ribbon_pts = [
    (304, 20), (1856, 20), (1928, 101), (1856, 182),
    (304, 182), (232, 101)
]

mask = Image.new('L', (W, H), 0)
ImageDraw.Draw(mask).polygon(ribbon_pts, fill=255)

grad = vgrad(W, H, (170, 50, 50), (84, 10, 10))
ribbon_layer = Image.new('RGBA', (W, H), (0, 0, 0, 0))
ribbon_layer.paste(grad.convert('RGBA'), (0, 0), mask)

sheen = Image.new('RGBA', (W, H), (0, 0, 0, 0))
sd = ImageDraw.Draw(sheen)
sd.polygon([(420, 20), (560, 20), (460, 182), (320, 182)], fill=(255, 255, 255, 28))
sheen_masked = Image.new('RGBA', (W, H), (0, 0, 0, 0))
sheen_masked.paste(sheen, (0, 0), mask)

base.alpha_composite(ribbon_layer)
base.alpha_composite(sheen_masked)

draw = ImageDraw.Draw(base)

draw.polygon(ribbon_pts, outline=GOLD, width=5)
inner_pts = [
    (322, 33), (1838, 33), (1898, 101), (1838, 169),
    (322, 169), (262, 101)
]
draw.polygon(inner_pts, outline=(*GOLD_DK[:3], 160), width=2)


def corner(x, y, dx, dy):
    draw.line([(x, y), (x + dx, y)], fill=GOLD, width=4)
    draw.line([(x, y), (x, y + dy)], fill=GOLD, width=4)
    draw.ellipse([x - 4, y - 4, x + 4, y + 4], fill=GOLD)


corner(346, 50, 30, 0)
corner(346, 50, 0, 30)
corner(1814, 50, -30, 0)
corner(1814, 50, 0, 30)
corner(346, 152, 30, 0)
corner(346, 152, 0, -30)
corner(1814, 152, -30, 0)
corner(1814, 152, 0, -30)


def ornament(cloud_cx, cy, tip_x, mirror):
    s = -1 if mirror else 1

    puffs = [(-30, -10, 24), (-60, 4, 22), (-88, -8, 18), (-110, 6, 13)]
    for dx, dy, r in puffs:
        x = cloud_cx + s * dx
        draw.ellipse([x - r, cy + dy - r, x + r, cy + dy + r], fill=GOLD)
    for dx, dy, r in puffs:
        x = cloud_cx + s * dx
        draw.ellipse([x - r, cy + dy - r, x + r, cy + dy + r], outline=GOLD_DK, width=2)

    rad = 32
    draw.ellipse([cloud_cx - rad, cy - rad, cloud_cx + rad, cy + rad], fill=RED, outline=GOLD, width=5)
    d = 17
    draw.polygon([(cloud_cx, cy - d), (cloud_cx + d, cy), (cloud_cx, cy + d), (cloud_cx - d, cy)], fill=GOLD)
    draw.ellipse([cloud_cx - 5, cy - 5, cloud_cx + 5, cy + 5], fill=RED)

    bar_y0, bar_y1 = cy - 5, cy + 5
    x0 = cloud_cx + (rad if not mirror else -rad)
    x1 = tip_x
    draw.rectangle([min(x0, x1), bar_y0, max(x0, x1), bar_y1], fill=GOLD)
    draw.ellipse([tip_x - 6, cy - 6, tip_x + 6, cy + 6], fill=GOLD_DK)


ornament(180, 101, 232, mirror=False)
ornament(1980, 101, 1928, mirror=True)

text = "HỆ THỐNG MÔN PHÁI"
font_path = "/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf"

max_w = 1500 - 660
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
base.resize((1080, 101), Image.LANCZOS).save("title-3-v2-1x.png")
print("done", size, tw, th)
