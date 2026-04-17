---
description: How to deploy and manage the Goat Getter website
---

# Goat Getter Website Deployment Workflow

// turbo-all

## Prerequisites
- Python 3 with `paramiko` installed (`pip3 install paramiko`)
- SSH access to goat-getter.com (user: `goatg`)

## Deploy Website Changes

1. Make your changes to files in `/Users/jpage000/.gemini/antigravity/scratch/goat-getter-website/`
   - `page-goat-getter-home.php` — Homepage template
   - `assets/global.css` — Global styles (WooCommerce, layout, design tokens)
   - `assets/style.css` — Homepage-specific styles
   - `assets/app.js` — JavaScript interactions

2. Run the unit tests:
```bash
bash /Users/jpage000/.gemini/antigravity/scratch/goat-getter-website/tests/run_tests.sh
```

3. Deploy to production:
```bash
python3 /Users/jpage000/.gemini/antigravity/scratch/goat-getter-website/deploy.py
```

4. Hard refresh the site (Cmd+Shift+R) to verify changes.

## Modify Elementor Templates (Header/Footer/Product/etc.)

Elementor templates are stored in the `wp_postmeta` table. To modify them:

1. Upload a PHP script via SFTP to `/home/goatg/goat-getter.com/gg-fix.php`
2. Execute it via `curl -sL https://goat-getter.com/gg-fix.php`
3. **Always clean up**: delete `gg-fix.php` after use

### Key Template IDs
| Template | Post ID | Conditions |
|----------|---------|------------|
| GG Header | 1785 | All pages |
| GG Footer | 1787 | All pages |
| GG Single Product | 1790 | Product pages |
| GG Product Archive | 1784 | Shop/product archive |
| GG Single Post | 1783 | Blog posts |
| GG Single Page | 1786 | Regular pages |
| GG 404 | 1788 | 404 page |
| GG Search Results | 1789 | Search page |
| GG Archive | 1791 | Archive pages |

### WooCommerce Product IDs
| Product | ID | Type |
|---------|-----|------|
| Gravity Pipeline Pro (parent) | 1756 | Variable |
| → Starter Monthly | 1763 | Variation ($9/mo) |
| → Starter Yearly | 1766 | Variation ($79/yr) |
| → Pro Monthly | 1764 | Variation ($19/mo) |
| → Pro Yearly | 1767 | Variation ($199/yr) |
| → Enterprise Monthly | 1765 | Variation ($49/mo) |
| → Enterprise Yearly | 1768 | Variation ($399/yr) |
| Gravity Pipeline (Free) | 1773 | Simple ($0) |
| Gravity Chat Pro | 1733 | Simple ($49) |
| Gravity Chat (Free) | 1807 | Simple ($0) |

## Server Access
- **Host**: goat-getter.com
- **User**: goatg
- **Port**: 22 (SSH/SFTP)
- **Theme path**: `/home/goatg/goat-getter.com/wp-content/themes/hello-elementor`
- **Assets path**: `/home/goatg/goat-getter.com/wp-content/themes/hello-elementor/goat-getter-assets/`
- **MU-plugins**: `/home/goatg/goat-getter.com/wp-content/mu-plugins/`
- **DB Host**: mysql.goat-getter.com
- **DB Name**: goatget_wpdb

## Coming Soon Products
To mark a product as "Coming Soon":
1. Go to WP Admin → Products → Edit the product
2. Add the tag `coming-soon`
3. Update — the button will automatically show "Coming Soon" and prevent purchase

This is powered by the `gg-coming-soon.php` mu-plugin.

## Commit & Push
```bash
cd /Users/jpage000/.gemini/antigravity/scratch/goat-getter-website
git add -A && git commit -m "your message" && git push
```
