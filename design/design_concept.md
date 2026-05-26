# PrintSync — Navigation Bar Design Concept

## Overview
Sticky top navigation bar for the PrintSync customer dashboard. Clean, modern, with an orange accent palette.

---

## Brand Colors

| Token            | Hex       | Usage                          |
|------------------|-----------|--------------------------------|
| `--orange`       | `#F47C3C` | Logo icon, active tab text, badges, interactive accents |
| `--orange-dark`  | `#E8654A` | Gradient partner               |
| `--orange-light` | `#FEF0E7` | Active tab bg, avatar bg       |
| `--blue`         | `#4A6CF7` | Secondary accent (not in nav)  |
| Gray scale       | `#111827` `#374151` `#6B7280` `#9CA3AF` `#E5E7EB` `#F3F4F6` `#FAFBFC` |

---

## Typography

- **Logo text**: 18px / 700 weight / `#111827`
- **Nav links**: 14px / 500 weight
- **User name**: 14px / 500 weight / `#374151`
- **Badge**: 10px / 700 weight
- **Dropdown items**: 14px / 400 weight

---

## Component Anatomy

```
┌─────────────────────────────────────────────────────────────────────┐
│  [■ PrintSync]    [🏠 Dashboard] [🛍 Store] [📋 View Orders]    [🛒2] [👤 AC ▼] │
└─────────────────────────────────────────────────────────────────────┘
```

### 1. Logo (left)
- Flex row: icon square + text
- Icon: 36×36px, rounded-lg (8px), `#F47C3C` bg, white svg (FileText)
- Text: "PrintSync", 18px bold, `#111827`

### 2. Navigation Links (center)
- Flex row, gap 4px
- Each link: button, rounded-full (9999px), px-4 (16px) py-2 (8px)
- **Default**: `#6B7280` text, transparent bg
  - Hover: `#F3F4F6` bg, `#374151` text
- **Active** (`.active`): `#FEF0E7` bg, `#F47C3C` text
- Icon: 16×16px inline svg

### 3. Cart Button (right)
- Icon-only button, 20×20px, `#6B7280`, hover `#111827`
- Badge: absolutely positioned top-right, 16×16px circle, `#F47C3C` bg, white 10px bold text

### 4. User Dropdown (right)
- Button: border `#E5E7EB`, rounded-full, px-3 py-1.5 (12px / 6px)
  - Hover: border `#D1D5DB`, bg `#F9FAFB`
- Avatar: 28×28px circle, `#FEF0E7` bg, `#F47C3C` initials (12px / 600)
- Name: visible on `sm:` (640px) breakpoint and up, hidden on mobile
- Chevron: 14×14px, rotates 180° when open
- **Dropdown**: absolute, 176px wide, border `#E5E7EB`, rounded-xl (12px), white bg, py-1.5 (6px), shadow-lg
  - Items: 14px, `#4B5563`, px-4 py-2, hover `#F9FAFB`
  - Divider: 1px `#F3F4F6`, my-1.5
  - Danger item (Log out): `#EF4444` text, hover `#FEF2F2` bg

---

## States

| Element       | Default            | Hover                  | Active                    |
|---------------|--------------------|------------------------|---------------------------|
| Nav link      | Gray-500, no bg    | Gray-100 bg, Gray-700  | Orange-light bg, Orange   |
| User btn      | Border gray-200    | Border gray-300, bg gray-50 | —                    |
| Cart btn      | Gray-500           | Gray-900               | —                         |
| Dropdown item | Gray-600           | Gray-50 bg             | —                         |

---

## Spacing & Sizing

- Navbar padding: 12px 24px (py-3 / px-6)
- Nav link padding: 8px 16px (py-2 / px-4)
- Nav link icon: 16×16px
- User button: 6px 12px with 6px left padding
- Avatar: 28×28px
- Dropdown: 176px wide, offset 8px from trigger

---

## Responsive

- **Mobile** (< 640px): User name hidden. Dropdown still functional.
- **Desktop** (≥ 640px): User name visible.

No hamburger menu — this is a dashboard top bar, not a primary mobile navigation. Mobile nav is assumed to be handled separately.

---

## Dependencies for Laravel Implementation

- **CSS**: No framework required. Use utility classes (Tailwind recommended) or plain CSS using the variables above.
- **Icons**: [Lucide Icons](https://lucide.dev) SVG set. MIT licensed.

---

## When Rebuilding in Laravel (Blade)

```blade
<nav class="sticky top-0 z-50 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-3">
  ...
</nav>
```

Active tab logic: pass `$activeTab` from controller and conditionally apply the `active` class.

Dropdown toggle: minimal Alpine.js or vanilla JS (no framework needed).

```
@if ($activeTab === 'store') class="nav-link active" @else class="nav-link" @endif
```

---

# PrintSync — Dashboard Page Design Concept

## Overview
Main dashboard landing page with welcome banner, stat cards, quick actions grid, recent orders list, and a promo banner. Clean, card-based layout with subtle shadows and hover interactions.

---

## Page Layout

```
┌──────────────────────────────────────────────────────────────┐
│  ╔══════════════════════════════════════════════════════╗    │
│  ║  Welcome back, Alice!    [● All systems operational] ║    │
│  ║  Manage your printing...                             ║    │
│  ╚══════════════════════════════════════════════════════╝    │
│                                                              │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐                   │
│  │Total Ord.│  │Completed │  │Spent     │                   │
│  │    1     │  │    0     │  │ ₱0.00   │                   │
│  └──────────┘  └──────────┘  └──────────┘                   │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐    │
│  │  Quick Actions                     ▬▬▬▬             │    │
│  │  [Browse Store] [My Orders] [Wishlist] [Support]    │    │
│  └──────────────────────────────────────────────────────┘    │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐    │
│  │  Recent Orders                        View All →    │    │
│  │  ┌──────────────────────────────────────────────┐   │    │
│  │  │ [📄] Signage  [Pending]  Order #5 • 1h ago  │   │    │
│  │  │                              ₱0.00  [Details]│   │    │
│  │  └──────────────────────────────────────────────┘   │    │
│  │  ─────── You have 1 active order ───────            │    │
│  └──────────────────────────────────────────────────────┘    │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐    │
│  │  ✨ First Order Bonus    Get 15% off...  [Claim]    │    │
│  └──────────────────────────────────────────────────────┘    │
└──────────────────────────────────────────────────────────────┘
```

---

## Sections

### 1. Welcome Banner
- Full-width rounded container (20px radius)
- Gradient: `linear-gradient(90deg, #F47C3C, #E8654A, #4A6CF7)`
- Dot pattern overlay at 15% opacity: `radial-gradient(circle at 1px 1px, white 1px, transparent 0)` at 24px grid
- Decorative blurred circles for depth (white/10 and black/10)
- **Content**: padding 36px (mobile) / 40px (desktop)
  - Title: 28px / 600 / white (32px desktop)
  - Subtitle: 15px / white at 85% opacity
- Status pill (right side, hidden < 1024px): white/15 bg, white/20 border, backdrop-blur, green pulsing dot

### 2. Stats Cards
- 3-column grid on `sm:` (640px), single column on mobile
- Card: border `#F3F4F6`, bg white, rounded-2xl (16px), shadow-sm
  - Hover: `translateY(-1px)`, shadow `0 4px 12px rgba(0,0,0,0.06)`
- Inner: 20px padding, rounded-[15px]
- Icon: 44×44px rounded-xl (12px), ring-1 black/4
  - `orange` class: `#FEF0E7` bg, `#F47C3C` icon — Total Orders
  - `green` class: `#ECFDF5` bg, `#22C55E` icon — Completed
  - `blue` class: `#EEF1FE` bg, `#4A6CF7` icon — Total Spent
- Label: 13px / 500 / `#6B7280`
- Value: 26px / 600 / `#111827`
- Trend: 12px / `#6B7280`, below the icon row

### 3. Quick Actions
- Card container: border `#F3F4F6`, rounded-2xl, bg white, p-6 (28px desktop), shadow-sm
- Header: section title (17px / 600) + gradient accent bar (32×4px, orange→blue, 60% opacity)
- 4-column grid on `lg:` (1024px), 2-col on `sm:`, 1-col on mobile
- Each action button:
  - Border `#F3F4F6`, bg gradient white→gray-50/50, rounded-xl (12px)
  - Hover: shadow `0 8px 24px rgba(0,0,0,0.08)`, `translateY(-2px)`
  - Inner: flex row, icon + text + arrow
  - Icon: 40×40px rounded-lg (8px), gradient bg matching brand color
    - Hover: scale(1.1) rotate(-3deg)
  - Label: 14px / 600 / `#111827`
  - Desc: 12.5px / `#6B7280`
  - Arrow: `#D1D5DB`, on hover `translateX(2px)` + `#9CA3AF`
- Colors per action:
  - Browse Store: `#FEF0E7` / `#F47C3C`
  - My Orders: `#E0F2FE` / `#0EA5E9`
  - Wishlist: `#F3F0FF` / `#8B5CF6`
  - Support: `#ECFDF5` / `#22C55E`

### 4. Recent Orders
- Card container: same as quick actions
- Header: border-bottom `#F3F4F6`, px-6 (28px desktop) py-4
  - Title: 17px / 600 / `#111827`
  - "View All" link: 13.5px / 500 / `#F47C3C`, hover `#E06A2A`
- Body: p-3 (16px desktop)
- Order card:
  - Border `#F3F4F6`, bg gradient gray-50/70→white, rounded-xl (12px)
  - Hover: border `#E5E7EB`, shadow `0 4px 12px rgba(0,0,0,0.05)`
  - Inner: flex row with icon (FileText, 44×44px, `#EEF1FE` bg, `#4A6CF7` color) + dot indicator (amber-400, 12px, positioned bottom-right of icon)
  - Order name: 15px / 600 / `#111827`
  - Status badge: rounded-md (6px), px-2 py-0.5, 11px / 500
    - **Pending**: `#FEF3C7` bg / `#B45309` text
    - **Processing**: `#DBEAFE` bg / `#1D4ED8` text
    - **Completed**: `#D1FAE5` bg / `#047857` text
  - Meta row: Order #, bullet, clock icon, time — 13px / `#6B7280`
  - Right side: amount (15px / 600 / `#111827`), "Pending payment" label (12px / `#6B7280`)
  - "Details" button (visible `sm:`+): border `#E5E7EB`, rounded-lg (8px), px-3 py-1.5, 13px / 500, hover gray-50 + `#D1D5DB` border
- Divider: centered text "You have 1 active order" with gradient lines on either side

### 5. Promo Banner
- Rounded-2xl (16px), gradient `#4A6CF7 → #3A5CE0`, padding 24px
- Icon: 48×48px, white/20 bg, backdrop-blur
- Title: 18px bold white
- Desc: 14px / white/85
- Code text: monospace, bold
- CTA button: white bg, `#4A6CF7` text, rounded-full, 14px / 600, hover white/90

---

## States

| Element         | Default                         | Hover                                    |
|-----------------|----------------------------------|------------------------------------------|
| Stat card       | shadow-sm, no transform          | shadow-md, translateY(-1px)              |
| Quick action btn| border gray-100, shadow-sm       | shadow-xl, translateY(-2px)              |
| Quick icon      | normal                           | scale(1.1) rotate(-3deg)                 |
| Quick arrow     | `#D1D5DB`                        | translateX(2px), `#9CA3AF`               |
| Order card      | border gray-100                  | border gray-200, shadow                  |
| View All link   | `#F47C3C`                        | `#E06A2A`                                |
| Promo CTA btn   | white bg, blue text              | white/90 bg                              |

---

## Responsive

| Breakpoint    | Behavior                                      |
|---------------|-----------------------------------------------|
| < 640px       | Stats: single column; Quick: single column; Details btn hidden |
| ≥ 640px (sm)  | Stats: 3-col grid; Quick: 2-col grid; Details btn visible |
| ≥ 1024px (lg) | Status pill visible; Quick: 4-col grid; wider padding |

---

## Key Sizes

- Page max-width: 1280px, centered
- Page padding: 32px 24px (→ 32px 32px on lg)
- Gap between sections: 32px
- Card border-radius: 16px (2xl)
- Button border-radius: 12px (xl) or 9999px (full)
- Inner card padding: 20px (stats), 24px (quick actions), 12-16px (orders)
- Icon containers: 44×44px (stats), 40×40px (quick), 44×44px (orders)
- Font sizes: title 28-32px, stat value 26px, section title 17px, body 14-15px, labels 12-13px

---

## Blade Integration Notes

```blade
{{-- Welcome banner --}}
<div class="welcome-banner">
  <div class="welcome-bg"></div>
  <div class="welcome-dots"></div>
  <div class="welcome-glow1"></div>
  <div class="welcome-glow2"></div>
  <div class="welcome-content">
    <h1>Welcome back, {{ $user->name }}!</h1>
    <p>...</p>
  </div>
</div>
```

- Stats and orders should pull from `$user` relationship data (e.g., `$user->orders()->count()`)
- Quick action links should map to named routes via `route('store')`, `route('orders')`, etc.
- Promo banner can be controlled with a `$promo` flag in session or config
```

---

# PrintSync — Store Page Design Concept

## Overview
Full product catalog page with hero search, trust badges, services highlight, category sidebar, product grid with pagination, recommendations carousel, CTA banner, and footer.

---

## Page Layout

```
┌───────────────────────────────────────────────────────────────┐
│  ╔═════════════════════════════════════════════════════════╗  │
│  ║  ✨ Welcome back, Alice                                ║  │
│  ║  Print, repair, and support for your next project.     ║  │
│  ║  [🔍 Search services, products...] [Search]            ║  │
│  ║                    [1 Active] [4 Wishlist] [Gold]      ║  │
│  ╚═════════════════════════════════════════════════════════╝  │
│                                                               │
│  ┌──────────┬──────────┬──────────┬──────────┐                │
│  │🚚Free Del│🛡Quality │🔧Repairs │📦Fast    │                │
│  └──────────┴──────────┴──────────┴──────────┘                │
│                                                               │
│  ┌───────────────────────────────────────────────────────┐    │
│  │ 🔧 Technical Services Available                       │    │
│  │ Need repairs too? We've got your tech covered.        │    │
│  │ [Printer repair] [Laptop repair] ... [Browse Repair →]│    │
│  └───────────────────────────────────────────────────────┘    │
│                                                               │
│  Browse Products & Services                   [Sort: Featured]│
│  ┌─────────┬────────────────────────────────────────────┐     │
│  │Categories│  ┌──────────┐┌──────────┐┌──────────┐    │     │
│  │All (34)  │  │Biz Cards ││Brochures ││Stickers  │    │     │
│  │Paper (8) │  │  ★5.0    ││  ★4.8    ││  ★4.9    │    │     │
│  │Marketing │  │ ₱499     ││ ₱1,250   ││ ₱299     │    │     │
│  │...       │  └──────────┘└──────────┘└──────────┘    │     │
│  │Quick Filt│  ┌──────────┐┌──────────┐┌──────────┐    │     │
│  │★ New Arrv│  │Mugs      ││Banners   ││Printer   │    │     │
│  │★ Best Sel│  │  ★4.7    ││  ★5.0    ││  ★4.9    │    │     │
│  │🔧 Repairs│  │ ₱350     ││ ₱1,800   ││ ₱900     │    │     │
│  │          │  └──────────┘└──────────┘└──────────┘    │     │
│  │Need a    │          ‹ 1 2 3 ... 6 ›                 │     │
│  │Quick Rpr?│                                           │     │
│  └─────────┴────────────────────────────────────────────┘     │
│                                                               │
│  Recommended for you                            [‹] [›]      │
│  ┌──────────┐┌──────────┐┌──────────┐┌──────────┐            │
│  │CCTV Setup││T-Shirts  ││PC Tune-Up││Laptop    │            │
│  └──────────┘└──────────┘└──────────┘└──────────┘            │
│                                                               │
│  ┌───────────────────────────────────────────────────────┐    │
│  │  Need something custom?    [Request a Quote]          │    │
│  │  Let's bring it to life.   [Talk to a Designer]       │    │
│  └───────────────────────────────────────────────────────┘    │
│                                                               │
│  ┌─────────┬──────────┬──────────┬──────────┐                │
│  │PrintSync│Shop      │Account   │Support   │                │
│  │(c) 2026 │          │          │          │                │
│  └─────────┴──────────┴──────────┴──────────┘                │
└───────────────────────────────────────────────────────────────┘
```

---

## Sections

### 1. Hero Banner
- Gradient: `linear-gradient(135deg, #F47C3C, #E8654A, #4A6CF7)`
- Rounded-3xl (24px), padding 40px, white text
- Decorative blurred circles for depth
- **Chip**: "Welcome back, Alice" — white/10 bg, white/20 border, backdrop-blur
- **Title**: 36px / 700 (→ 48px on md), letter-spacing -0.02em
- **Subtitle**: 15px / white/85 opacity
- **Search bar**: white rounded-full, max-w 448px, inner padding 6px, shadow
  - Search icon: `#9CA3AF`
  - Input: 14px, placeholder `#9CA3AF`
  - Button: `#F47C3C` bg, white text, 14px / 600, rounded-full
- **Stat cards** (desktop: column on right; mobile: 3-col grid below):
  - White/10 bg, white/15 border, backdrop-blur, rounded-2xl (16px)
  - Icon + label row (13px / white/80)
  - Value: 24px / 700

### 2. Trust Strip
- 4-col grid (→ 2-col on mobile), border `#E5E7EB`, rounded-2xl, bg white, p-5
- Each item: icon (40×40px, `#FEF0E7` bg, `#F47C3C` icon) + label (14px / 600) + sub (12px / `#6B7280`)
- Icons: Truck (delivery), Shield (quality), Wrench (repairs), Package (turnaround)

### 3. Services Highlight
- Two-column split on lg (1.25fr / 0.75fr), stacked on mobile
- **Left panel**: p-7 (→ p-8 on md)
  - Chip: `#EEF1FE` bg, `#4A6CF7` text, 12px / 600
  - Title: 24px / 700 / `#111827`
  - Desc: 14px / `#6B7280`
  - Tags: `#E5E7EB` border, `#F9FAFB` bg, 12px / 500 / `#374151`
- **Right panel**: gradient `#4A6CF7 → #3A5CE0`, white text
  - Label: 14px / 600 / white/85
  - Title: 22px / 700
  - CTA button: white bg, `#4A6CF7` text, rounded-full

### 4. Category Sidebar
- Fixed width 240px on lg, full width on mobile
- Card: p-5, border `#E5E7EB`, rounded-2xl, bg white
- **Categories heading**: 12px / 700 / uppercase / `#9CA3AF`
- Category items: flex row, justify-between, rounded-xl (12px), px-4 py-3
  - Default: `#4B5563` text, hover `#F9FAFB`
  - Active: `#FEF0E7` bg, `#F47C3C` text
  - Count badge: 20px tall, rounded-full, px-1.5
    - Active: `#F47C3C` bg, white text
    - Default: `#F3F4F6` bg, `#6B7280` text
- Divider: `#F3F4F6`, my-5
- **Quick Filters heading**: same as categories
- Filter items: same hover as category, icon + text
  - New Arrivals / Best Sellers: `#F47C3C` icon
  - Repair Services: `#4A6CF7` icon
- **Sidebar CTA** (below card): gradient `#4A6CF7 → #3A5CE0`, p-5, rounded-2xl
  - Icon: 36×36px, white/20 bg, backdrop-blur
  - Title: 16px / 700 white
  - Desc: 12px / white/80
  - Button: white bg, `#4A6CF7` text, rounded-full, 12px / 600

### 5. Product Card
- Stacked column layout
- **Image area**: aspect-ratio 1/1, rounded-2xl (16px), overflow hidden
  - Product type: `#F3F4F6` bg, hover slightly darker
  - Service type: `rgba(238,241,254,0.5)` bg, hover `#EEF1FE`
  - Image: cover, hover scale(1.05) in 0.5s
- **Category tag**: absolute top-left (16px), 10px / 700 / uppercase
  - Product: `rgba(255,255,255,0.95)` bg, `#374151` text
  - Service: `rgba(74,108,247,0.9)` bg, white text
- **Popular badge**: below category tag, `#F47C3C` bg, white, 10px / 700, sparkle icon
- **Like button**: absolute top-right (16px), 32×32px, white/95 bg, rounded-full
  - Default: `#6B7280` icon; hover: scale(1.1), `#F47C3C`
  - Liked state: `#F47C3C` fill + stroke
- **Info section**: px-1
  - Name: 16px / 700 / `#111827`
  - Price: 16px / 700 / `#F47C3C` (products) or `#4A6CF7` (services)
  - Service "from" label: 10px / 500 / `#9CA3AF`
  - Service detail: 12px / `#6B7280`
  - Rating: star (13px, fill `#F47C3C`) + value (14px / 600) + count (14px / `#9CA3AF`)
- **Action buttons**: flex row, gap 2, mt auto
  - Outline: border-2 `#E5E7EB`, 14px / 600, hover `#F47C3C` (service hover: `#4A6CF7`)
  - Primary: `#4A6CF7` bg, white, 14px / 600, hover brightness(1.1)

### 6. Pagination
- Border-top `#E5E7EB`, pt-6, mt-12
- Previous: `#9CA3AF`, hover `#374151`
- Page numbers: 36×36px, rounded-lg (8px), 14px / 500
  - Active: `#F47C3C` bg, white, 700 weight
  - Default: hover `#F3F4F6`
- Next: `#111827`, hover `#F47C3C`
- Ellipsis: `#9CA3AF`

### 7. Recommendations
- Section heading + nav arrows (40×40px circle, border `#E5E7EB`, hover `#F47C3C`)
- 4-col grid (→ 2-col sm, 1-col default)
- Reuses product card component

### 8. CTA Banner
- Gradient `#4A6CF7 → #3A5CE0`, rounded-3xl (24px), p-12 (→ p-16 on md)
- Title: 30px (→ 36px) / 700, highlight in `#FDE3C8`
- Desc: 15px / white/85
- Two buttons:
  - Primary (Request Quote): `#F47C3C` bg, white, shadow, rounded-full
  - Secondary (Talk to Designer): white/10 bg, white/30 border, backdrop-blur

### 9. Footer
- pt-10, border-top `#E5E7EB`, mt-16
- 4-col grid (→ 2-col mobile)
- Brand column: icon (32×32px, `#F47C3C` bg) + "PrintSync" (16px / 700) + desc (14px / `#6B7280`)
- Link columns: heading 14px / 700, links 14px / `#6B7280`, hover `#F47C3C`
- Bottom bar: `© 2026 PrintSync` + Terms + Privacy — 12px / `#9CA3AF`

---

## Product Data Variants

| Type      | Price Color | Tag Style       | Action Buttons      | Extras               |
|-----------|-------------|-----------------|---------------------|----------------------|
| Product   | `#F47C3C`   | White bg        | "Add to Cart" + "Order Now" | —           |
| Service   | `#4A6CF7`   | Blue bg/white   | "Get Quote" + "Book Now"    | "from" label, service detail row |

---

## States

| Element           | Default                          | Hover                                        | Active                  |
|-------------------|----------------------------------|----------------------------------------------|-------------------------|
| Product image bg  | Gray-100 / blue-light/50         | Gray-200/70 / blue-light                     | —                       |
| Product image     | scale(1)                         | scale(1.05)                                  | —                       |
| Category item     | `#4B5563`, no bg                 | `#F9FAFB` bg                                 | `#FEF0E7`, `#F47C3C`   |
| Like button       | `#6B7280`                        | scale(1.1), `#F47C3C`                        | fill `#F47C3C`          |
| Outline btn       | border `#E5E7EB`, `#374151`      | border `#F47C3C`, `#F47C3C`                  | —                       |
| Primary btn       | `#4A6CF7` bg                     | brightness(1.1)                              | —                       |
| Pagination num    | no bg, `#374151`                 | `#F3F4F6` bg                                 | `#F47C3C`, white        |
| Page nav arrows   | `#E5E7EB` border, `#6B7280`      | `#F47C3C` border, `#F47C3C`                  | —                       |

---

## Responsive

| Breakpoint    | Behavior                                               |
|---------------|--------------------------------------------------------|
| < 640px       | Product: 1-col; Trust: 2-col; Hero stats: 3-col grid   |
| ≥ 640px (sm)  | Product: 2-col; Trust: 4-col; Rec: 2-col               |
| ≥ 768px (md)  | Hero: row layout (text left, stats right column)        |
| ≥ 1024px (lg) | Sidebar visible (240px) + Product 3-col; Rec: 4-col; Services: 2-col split; Sort btn visible; Hero stats: column |

---

## Key Sizes

- Hero: 24px radius, 40px padding
- Product image: aspect-ratio 1/1, 16px radius
- Category sidebar: 240px wide on lg
- Product grid gap: 24px
- Category items: 12px radius, px-4 py-3
- Icon containers: 40×40px (trust), 36×36px (sidebar cta)
- Font sizes: hero title 36-48px, product name 16px, price 16px, rating 14px, buttons 14px
- CTA banner: 24px radius, 48-64px padding
- Footer: 4-col grid, 14px body text

---

## Blade Integration Notes

```blade
@foreach ($products as $product)
  <div class="product-card">
    <div class="product-img-wrap {{ $product->type === 'service' ? 'service' : 'product' }}">
      <span class="product-cat-tag {{ $product->type === 'service' ? 'service-tag' : 'product-tag' }}">
        {{ $product->category }}
      </span>
      @if ($product->popular)
        <span class="product-popular">✨ Popular</span>
      @endif
      <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-img" />
    </div>
    <div class="product-info">
      {{-- name, price, rating, actions --}}
    </div>
  </div>
@endforeach
```

- Categories should loop from `$categories` collection
- Sort dropdown toggles `?sort=` query parameter
- Pagination uses `$products->links()` with custom view
- Sidebar CTA can link to `route('repairs')`
- Hero stats pull from `$user->orders()->where('status', 'active')->count()`, etc.
- Trust strip content best managed as configurable site settings
```

---

# PrintSync — Orders Page Design Concept

## Overview
Order listing page with filter tabs (All/Active/Completed), search, order cards showing status, products, totals, and contextual actions per status. Includes a help/support CTA at the bottom.

---

## Page Layout

```
  ← Back    My Orders                    [🔍 Search by order ID or product...]

  [All Orders]  [Active]  [Completed]

  ┌─────────────────────────────────────────────────────────────┐
  │ [📄] ORD-005    Placed 1 hour ago    [🕐 Pending] [Track →] │
  │ ─────────────────────────────────────────────────────────── │
  │ [🖼] 1 item · Signage               Total: Pending  [Pay Now]│
  │ 🚚 Estimated: 3-5 business days                            │
  └─────────────────────────────────────────────────────────────┘

  ┌─────────────────────────────────────────────────────────────┐
  │ [📄] ORD-004    Placed 2 days ago    [✓ Delivered] [Track →]│
  │ ─────────────────────────────────────────────────────────── │
  │ [🖼🖼] 3 items · Business Cards, Flyers  Total: ₱1,547     │
  │ [★ Rate] [💬 Reorder]                                      │
  │ 🚚 Estimated: Delivered on Jan 15 • Tracking: PH1234567890 │
  └─────────────────────────────────────────────────────────────┘

  ╔═══════════════════════════════════════════════════════════╗
  ║  Need help with your order?           [💬 Contact Support]║
  ║  Our support team is here to assist you.                 ║
  ╚═══════════════════════════════════════════════════════════╝
```

---

## Sections

### 1. Page Header
- Back button (arrow left + "Back"): 14px / 500 / `#6B7280`, hover `#F47C3C`
- Title: "My Orders" — 24px / 700 / `#111827`
- Search bar: max-w 320px, rounded-full, bg white, ring `#E5E7EB`
  - Focus: ring `#F47C3C`
  - Placeholder: "Search by order ID or product..."

### 2. Filter Tabs
- Flex row, gap 8px, 14px / 500, rounded-full, px-4 py-2
- **All Orders**: `#111827` bg / white text
- **Active**: `#F47C3C` bg / white text
- **Completed**: `#22C55E` bg / white text
- **Default** (inactive): white bg / `#4B5563` text, hover `#F3F4F6`

### 3. Order Card
- Border `#E5E7EB`, rounded-2xl (16px), bg white
- Hover: border `rgba(244,124,60,0.5)`, shadow `0 8px 24px rgba(0,0,0,0.08)`

#### Order Header
- `rgba(249,250,251,0.5)` bg, border-bottom `#F3F4F6`, px-6 py-4
- Left: icon (40×40px, `#FEF0E7` bg, `#F47C3C` FileText icon) + ID (14px / 700) + date (12px / `#6B7280`)
- Right: status badge + "Track Order" link

#### Status Badges
| Status    | Badge Colors                    | Icon          |
|-----------|---------------------------------|---------------|
| Pending   | `#FEF3C7` bg / `#B45309` text  | Clock         |
| Processing| `#DBEAFE` bg / `#1D4ED8` text  | Package       |
| Shipped   | `#F3F0FF` bg / `#6D28D9` text  | Truck         |
| Delivered | `#D1FAE5` bg / `#047857` text  | CheckCircle   |
| Cancelled | `#FEE2E2` bg / `#B91C1C` text  | FileText      |

Badge: inline-flex, gap 6px, px-3 py-1.5, 12px / 600, rounded-full

#### Order Body
- p-6 (24px)
- **Products**: stacked thumbnails (56×56px, rounded-lg, border-2 white, shadow) with negative margin (-8px) for overlap
  - Left: stack + item count (14px / 500) + product names (12px / `#6B7280`)
- **Total**: label (12px / `#6B7280`) + value (18px / 700 / `#111827`)
  - Pending total: `#F47C3C` color, text "Pending"
- **Actions**: pills (btn-pill), 14px, rounded-full, px-4 py-2
  - Outline: border `#E5E7EB`, hover border + text `#F47C3C`
  - Solid (Pay Now): `#F47C3C` bg, white, 600 weight

#### Contextual Actions per Status
| Status     | Action Buttons                        |
|------------|---------------------------------------|
| Pending    | **Pay Now** (solid orange)            |
| Delivered  | Rate (outline, star) + Reorder (outline, message) |
| Processing | Invoice (outline, download icon)      |
| Shipped    | Invoice (outline, download icon)      |

#### Delivery Info
- `#F9FAFB` bg, rounded-xl (12px), px-4 py-3, mt-4
- Truck icon (`#9CA3AF`) + labels (14px / `#4B5563`)
- Strong labels: `#111827` / 600
- Separator: `#D1D5DB` bullet
- Shows estimated delivery and tracking number when available

### 4. Empty State
- Border `#E5E7EB`, rounded-2xl, bg white, py-20
- Icon: 80×80px circle, `#F3F4F6` bg, `#9CA3AF` Package icon (32px)
- Title: 20px / 700 / `#111827`
- Desc: 14px / `#6B7280` — contextual based on filter/search

### 5. Help Section
- Gradient `#4A6CF7 → #3A5CE0`, rounded-2xl, p-6, white text
- Title: 18px / 700
- Desc: 14px / white/85
- CTA: white bg, `#4A6CF7` text, rounded-full, px-6 py-3, 14px / 600, MessageSquare icon
  - Hover: white/90 bg

---

## Responsive

| Breakpoint    | Behavior                                               |
|---------------|--------------------------------------------------------|
| < 640px       | Header: stacked; Order header: stacked; Order main: stacked; Help: stacked |
| ≥ 640px (sm)  | Header: row; Order header: row; Order main: row; Help: row |

---

## Key Sizes

- Page max-width: 1280px, centered
- Order card: 16px radius, 24px body padding
- Product thumbnail: 56×56px, 8px radius
- Status badges: 12px / 600, px-3 py-1.5
- Filter tabs: 14px / 500, px-4 py-2
- Action buttons: 14px, px-4 py-2, rounded-full
- Page title: 24px / 700
- Help section icon + text: 16px icon, 14px body, 18px title

---

## Blade Integration Notes

```blade
@foreach ($orders as $order)
  <div class="order-card">
    <div class="order-header">
      {{-- order ID, date, status badge, track link --}}
    </div>
    <div class="order-body">
      <div class="order-main">
        {{-- product thumbnails, item count, total, contextual actions --}}
      </div>
      @if ($order->estimated_delivery)
        <div class="delivery-info">
          {{-- delivery estimate + tracking --}}
        </div>
      @endif
    </div>
  </div>
@endforeach
```

- Orders passed from controller via `$user->orders()->latest()->get()`
- Filter tabs toggle `?status=` query parameter or use livewire/alpine
- Search filters client-side or via backend `WHERE` clause
- Status badge uses a helper or `@switch` to pick color classes
- Contextual action buttons use `@if` / `@elseif` per status
```
