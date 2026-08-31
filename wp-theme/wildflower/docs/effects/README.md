# Effects reference & status

Source: client-sent zip (`effects-reference.zip`, 31 React/TS snippets `Эф1…Эф32`,
each with a 1-line RU description). We re-implement chosen ones **natively**
(vanilla JS + GSAP + CSS, theme tokens, pult-aware) — never drop React into WP.

Curation is for a **premium local Boston florist** (editorial restraint; flowers
are the colour; avoid SaaS/gadget tropes).

## Status
- ✅ **Эф4** — interactive "Shop by occasion" (hover swaps the image) — DONE (front-page).
- ✅ **Эф28** — cinematic scroll-story video (zoom on scroll) — DONE (front-page, GSAP).
- ⏭️ **Эф29 / Эф6** — rotating last word in the hero headline — NEXT.
- ⏭️ **Эф14 / Эф15** — testimonials with customer photos (E-E-A-T) — planned.
- 🔵 **Эф11** — animated photo grid (gallery already has tile-assemble) — optional.
- 🔵 **Эф5** — fancier button hover (we have sheen + magnetic + pulse) — optional.
- 🔵 **Эф26** — more scroll parallax (some already via GSAP) — optional.

## 🟢 Good fit (ideas worth using)
Эф4 (occasion hover-image), Эф28 (scroll-video), Эф29/Эф6 (headline word swap),
Эф14/Эф15 (reviews w/ photos), Эф11 (photo grid), Эф5 (button hover),
Эф26 (tasteful parallax).

## 🟡 Situational
Эф2 (3D photo sphere — gimmick risk), Эф13 (text particles), Эф3 (vertical
slider), Эф18/Эф19 (3D grid bg), Эф20 (before/after slider — weak for flowers).

## 🔴 Off-brand for a florist (skip)
Эф1 (globe office network), Эф7/Эф23 (3D robot), Эф25 (Rubik's cube),
Эф27 (tower build), Эф30 (brain), Эф16/Эф17 (circuit/signals),
Эф12/Эф22/Эф32 (Apple-style device/laptop/earth reveal), Эф24 (too bright).
Reason: spinning Earth / world map / gadgets signal "global tech co.", which
fights the local same-day-Boston positioning.

> Raw snippets kept as `ЭфN.txt` for reference. Implement natively, in our palette.
