"""
Build the portal's app icons from the shop's own mark.

Usage:  python3 deploy/make-icons.py SOURCE.png OUTPUT_DIR
        python3 deploy/make-icons.py public/portal_assets/icons/icon-source.png public/portal_assets/icons

Needs Pillow. The source is the file the shop uploaded as its browser
icon; it is committed beside the icons made from it so the next person
has something to regenerate from.

The source is the brand mark in evergreen on transparency. Every icon here is
the reverse of that — ivory mark on an evergreen field — because that is what
the portal is: ivory on evergreen on every screen, and a dark mark on a dark
field would be a square of nothing on a home screen.

Sizes and insets:
  any (192, 512)   mark fills 0.70 of the square. Android rounds the corners
                   of a non-maskable icon only slightly, so this is close to
                   what is drawn.
  maskable (512)   the platform may crop anything outside a circle 80% of the
                   width. The largest square inside that circle is 0.8/sqrt(2)
                   = 0.566 of the width, so the mark fits inside THAT, not
                   inside the icon.
  apple (180)      iOS squircles the corners and does not honour transparency,
                   so the background is painted and the inset left generous.
"""
import sys
from PIL import Image

SRC, OUT = sys.argv[1], sys.argv[2]

EVERGREEN = (40, 51, 38, 255)   # #283326
IVORY = (248, 237, 210)         # #F8EDD2

src = Image.open(SRC).convert('RGBA')

# Keep the shape, throw away the colour: the mark arrives in evergreen, which
# is exactly the colour it is about to be placed on.
# The uploaded file is clipped on its right edge: its last column carries a
# tall sliver that belongs to the next letter of the wordmark it was cut out
# of, not to the X. Left in, it renders as a bar floating beside the mark.
# One column is all of it — x=388 holds only the serif foot the arm really has.
src = src.crop((0, 0, src.width - 1, src.height))

alpha = src.split()[3]
mark = Image.new('RGBA', src.size, IVORY + (0,))
mark.putalpha(alpha)
mark = mark.crop(alpha.getbbox())


def icon(size, fill):
    """One square icon: the mark scaled to `fill` of the side, centred."""
    box = size * fill
    scale = min(box / mark.width, box / mark.height)
    w, h = max(1, round(mark.width * scale)), max(1, round(mark.height * scale))

    # Resized in RGBA with a constant RGB, so only the alpha channel is
    # resampled and no colour bleeds into the edge pixels.
    small = mark.resize((w, h), Image.LANCZOS)

    canvas = Image.new('RGBA', (size, size), EVERGREEN)
    canvas.alpha_composite(small, ((size - w) // 2, (size - h) // 2))

    return canvas.convert('RGB')


for name, size, fill in (
    ('icon-192.png', 192, 0.70),
    ('icon-512.png', 512, 0.70),
    ('icon-maskable-512.png', 512, 0.566),
    ('apple-touch-icon.png', 180, 0.66),
):
    icon(size, fill).save(f'{OUT}/{name}', optimize=True)
    print('wrote', name, size)
