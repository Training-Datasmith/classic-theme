# Architecture: classic-theme

## Purpose

The default "Classic" theme for PrestaShop. It provides the complete front-office storefront UI — HTML templates, CSS, JavaScript, and companion modules — out of the box when PrestaShop is installed.

## Directory Structure

```
templates/                     — Smarty `.tpl` files for all storefront pages and partials
  catalog/                     — Product listing, product page, category pages
  checkout/                    — Cart, checkout steps, order confirmation
  customer/                    — Account pages (login, register, order history, etc.)
  errors/                      — 404, 500, maintenance templates
  layouts/                     — Base layout wrapping all pages
  _partials/                   — Reusable partial templates (header, footer, breadcrumb, etc.)

assets/
  css/                         — Compiled CSS (Bootstrap-based)
  js/                          — Frontend JavaScript
  img/                         — Theme image assets

modules/
  blockreassurance/            — Reassurance block module (included with theme)
  ps_advertising/              — Advertising banner module
  ps_bestsellers/              — Best-sellers list module
  ps_brandlist/                — Brand list module
  ps_categoryproducts/         — Category products widget
  ps_crossselling/             — Cross-selling widget
  ps_emailalerts/              — Email alert subscriptions
  ps_newproducts/              — New products widget
  ps_specials/                 — Special offers widget
  ...

config/                        — theme.yml (metadata, supported parents, layouts, assets)
preview.jpg                    — Theme preview image shown in Back Office
```

## Key Design Decisions

- **Smarty 3 templates** — all HTML output uses PrestaShop's Smarty integration; templates inherit from base layouts using `{extends}` and `{block}`.
- **Bootstrap-based CSS** — uses Bootstrap grid and components customised with PrestaShop's SCSS variables.
- **Bundled modules** — companion display modules are shipped with the theme to ensure a cohesive out-of-box experience; they register in PrestaShop hook positions.
- **hook system** — theme output is extensible via PrestaShop's hook system; third-party modules can inject content into `displayTop`, `displayFooter`, `displayProductAdditionalInfo`, etc.

## Extension Points

- Create a child theme that extends Classic (`parent: classic` in `theme.yml`) to override specific templates without forking the entire theme.
- Override individual `.tpl` files in a child theme's `templates/` directory.
- Add custom CSS via `assets/css/custom.css` or a child theme.
