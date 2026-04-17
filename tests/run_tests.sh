#!/bin/bash
# ─────────────────────────────────────────────────────────────────
# Goat Getter Website — Unit Tests
# Validates HTML structure, CSS integrity, PHP template correctness,
# and asset references.
# Updated: 2026-04-17
# ─────────────────────────────────────────────────────────────────
set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"
PASS=0
FAIL=0
TOTAL=0

pass() { PASS=$((PASS+1)); TOTAL=$((TOTAL+1)); echo "  ✅ $1"; }
fail() { FAIL=$((FAIL+1)); TOTAL=$((TOTAL+1)); echo "  ❌ $1"; }

echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  Goat Getter Website — Unit Test Suite"
echo "═══════════════════════════════════════════════════════════"

# ── 1. Required Files Exist ───────────────────────────────────────
echo ""
echo "▸ File Existence Checks"
for f in \
    "index.html" \
    "page-goat-getter-home.php" \
    "assets/style.css" \
    "assets/global.css" \
    "assets/app.js" \
    "assets/gp-logo.svg" \
    "assets/Goat Logo Horizontal.png" \
    "assets/Goat Logo Favicon 512x512.png" \
    "assets/Goat Logo horizontal white.png" \
    "assets/hero-pipeline.png" \
    "deploy.py"; do
    if [ -f "$ROOT_DIR/$f" ]; then
        pass "File exists: $f"
    else
        fail "Missing file: $f"
    fi
done

# ── 2. HTML Structure Checks ─────────────────────────────────────
echo ""
echo "▸ HTML Structure (index.html)"

HTML="$ROOT_DIR/index.html"

# Meta description
if grep -q 'meta name="description"' "$HTML"; then
    pass "Has meta description"
else
    fail "Missing meta description"
fi

# Title tag
if grep -q '<title>Goat Getter' "$HTML"; then
    pass "Has title tag"
else
    fail "Missing title tag"
fi

# Favicon
if grep -q 'Goat Logo Favicon 512x512.png' "$HTML"; then
    pass "Favicon references colored PNG"
else
    fail "Favicon not referencing colored PNG"
fi

# Single h1
H1_COUNT=$(grep -c '<h1>' "$HTML" || true)
if [ "$H1_COUNT" -eq 1 ]; then
    pass "Exactly one <h1> tag"
else
    fail "Expected 1 <h1>, found $H1_COUNT"
fi

# Navigation
if grep -q 'id="gg-nav"' "$HTML"; then
    pass "Navigation exists"
else
    fail "Missing navigation"
fi

# Sections: plugins, why, coming-soon
for section in "plugins" "why" "coming-soon"; do
    if grep -q "id=\"$section\"" "$HTML"; then
        pass "Section found: #$section"
    else
        fail "Missing section: #$section"
    fi
done

# ── 3. Plugin Cards ──────────────────────────────────────────────
echo ""
echo "▸ Plugin Card Checks"

# Pipeline Free card
if grep -q 'pipeline-free-cta' "$HTML"; then
    pass "Free pipeline CTA exists"
else
    fail "Missing Free pipeline CTA"
fi

# Pipeline Pro card
if grep -q 'pipeline-pro-cta' "$HTML"; then
    pass "Pro pipeline CTA exists"
else
    fail "Missing Pro pipeline CTA"
fi

# Pro card gold button
if grep -q 'gg-btn-gold' "$HTML"; then
    pass "Pro card uses gold button"
else
    fail "Pro card missing gold button"
fi

# Pro card "Most Popular" badge
if grep -q 'Most Popular' "$HTML"; then
    pass "Pro card has 'Most Popular' badge"
else
    fail "Missing 'Most Popular' badge"
fi

# GP logo in cards
GP_LOGO_COUNT=$(grep -c 'gp-logo.svg' "$HTML" || true)
if [ "$GP_LOGO_COUNT" -ge 2 ]; then
    pass "GP logo used in plugin cards ($GP_LOGO_COUNT refs)"
else
    fail "GP logo not in plugin cards (found $GP_LOGO_COUNT)"
fi

# ── 4. Coming Soon Section ───────────────────────────────────────
echo ""
echo "▸ Coming Soon Checks"

for plugin in "Gravity AMS" "Gravity Reports"; do
    if grep -q "$plugin" "$HTML"; then
        pass "Coming Soon: $plugin listed"
    else
        fail "Coming Soon: missing $plugin"
    fi
done

# ── 5. CSS Checks ────────────────────────────────────────────────
echo ""
echo "▸ CSS Checks (style.css)"

CSS="$ROOT_DIR/assets/style.css"

# Logo height 68px
if grep -q 'height: 68px' "$CSS"; then
    pass "Logo height is 68px"
else
    fail "Logo height not 68px"
fi

# Pill buttons (50px radius)
if grep -q 'border-radius: 50px' "$CSS"; then
    pass "Buttons have pill radius (50px)"
else
    fail "Buttons missing pill radius"
fi

# Gold button style
if grep -q 'gg-btn-gold' "$CSS"; then
    pass "Gold button CSS defined"
else
    fail "Gold button CSS missing"
fi

# Pro card gold border
if grep -q '#F5A623' "$CSS"; then
    pass "Gold color (#F5A623) in CSS"
else
    fail "Missing gold color in CSS"
fi

# Flex column on plugin cards
if grep -q 'flex-direction: column' "$CSS"; then
    pass "Plugin cards use flex column"
else
    fail "Plugin cards missing flex column"
fi

# Plugin grid uses auto-fit for responsive layout
if grep -q 'repeat(auto-fit' "$CSS"; then
    pass "Plugin grid uses auto-fit layout"
else
    fail "Plugin grid missing auto-fit"
fi

# ── 6. Global CSS Checks ─────────────────────────────────────────
echo ""
echo "▸ CSS Checks (global.css)"

GCSS="$ROOT_DIR/assets/global.css"

# Design tokens
if grep -q '\-\-gg-navy:' "$GCSS"; then
    pass "Has --gg-navy design token"
else
    fail "Missing --gg-navy design token"
fi

if grep -q '\-\-gg-purple:' "$GCSS"; then
    pass "Has --gg-purple design token"
else
    fail "Missing --gg-purple design token"
fi

# Full-width layout (90%)
if grep -q '\-\-gg-max:.*90%' "$GCSS"; then
    pass "Layout uses 90% max-width"
else
    fail "Layout not using 90% max-width"
fi

# Sticky footer (flexbox)
if grep -q 'min-height: 100vh' "$GCSS"; then
    pass "Sticky footer: body has min-height 100vh"
else
    fail "Missing sticky footer min-height"
fi

if grep -q 'flex-direction: column' "$GCSS"; then
    pass "Sticky footer: body uses flex column"
else
    fail "Missing sticky footer flex column"
fi

if grep -q 'flex: 1' "$GCSS"; then
    pass "Sticky footer: main content uses flex: 1"
else
    fail "Missing flex: 1 on main content"
fi

# WooCommerce product card styling
if grep -q 'woocommerce ul.products li.product' "$GCSS"; then
    pass "WooCommerce product card styles defined"
else
    fail "Missing WooCommerce product card styles"
fi

# Product card borders
if grep -q 'border: 1px solid var(--gg-border)' "$GCSS"; then
    pass "Product cards have borders"
else
    fail "Product cards missing borders"
fi

# Product card hover effect
if grep -q 'translateY(-4px)' "$GCSS"; then
    pass "Product cards have hover lift effect"
else
    fail "Product cards missing hover effect"
fi

# Image contain for product thumbnails
if grep -q 'object-fit: contain' "$GCSS"; then
    pass "Product images use object-fit: contain"
else
    fail "Product images missing object-fit: contain"
fi

# Elementor section boxed override to 90%
if grep -q 'elementor-section-boxed' "$GCSS"; then
    pass "Elementor boxed sections overridden to 90%"
else
    fail "Missing Elementor boxed section override"
fi

# My Account styling
if grep -q 'woocommerce-MyAccount' "$GCSS"; then
    pass "My Account page styles defined"
else
    fail "Missing My Account styles"
fi

# ── 7. PHP Template Checks ────────────────────────────────────────
echo ""
echo "▸ PHP Template Checks"

PHP="$ROOT_DIR/page-goat-getter-home.php"

# Standalone: has its own <!DOCTYPE html>
if grep -q '<!DOCTYPE html>' "$PHP"; then
    pass "PHP has standalone <!DOCTYPE html>"
else
    fail "PHP missing standalone <!DOCTYPE html>"
fi

# Standalone: has its own </html>
if grep -q '</html>' "$PHP"; then
    pass "PHP has closing </html>"
else
    fail "PHP missing closing </html>"
fi

# Loads CSS directly
if grep -q 'style.css' "$PHP"; then
    pass "PHP loads CSS directly"
else
    fail "PHP not loading CSS"
fi

# Loads JS directly
if grep -q 'app.js' "$PHP"; then
    pass "PHP loads JS directly"
else
    fail "PHP not loading JS"
fi

# Template Name header
if grep -q 'Template Name: Goat Getter Home' "$PHP"; then
    pass "PHP has Template Name header"
else
    fail "PHP missing Template Name header"
fi

# Uses goat-getter-assets path
if grep -q 'goat-getter-assets' "$PHP"; then
    pass "PHP references goat-getter-assets/ path"
else
    fail "PHP not using goat-getter-assets/ path"
fi

# Has all the same sections as HTML
for section in "plugins" "why" "coming-soon"; do
    if grep -q "id=\"$section\"" "$PHP"; then
        pass "PHP has section: #$section"
    else
        fail "PHP missing section: #$section"
    fi
done

# Plugins nav link goes to /shop/
if grep -q 'href="/shop/">Plugins' "$PHP"; then
    pass "PHP Plugins nav links to /shop/"
else
    fail "PHP Plugins nav not linking to /shop/"
fi

# No gravitypipeline.io links (should be internal)
GP_LINKS=$(grep -c 'gravitypipeline.io' "$PHP" || true)
if [ "$GP_LINKS" -eq 0 ]; then
    pass "PHP has no gravitypipeline.io links (all internal)"
else
    fail "PHP still has $GP_LINKS gravitypipeline.io links"
fi

# Gravity Chat cards in Plugin Library
if grep -q 'chat-free-cta' "$PHP"; then
    pass "PHP has Gravity Chat Free card"
else
    fail "PHP missing Gravity Chat Free card"
fi

if grep -q 'chat-pro-cta' "$PHP"; then
    pass "PHP has Gravity Chat Pro card"
else
    fail "PHP missing Gravity Chat Pro card"
fi

# View All Plugins link
if grep -q 'View All Plugins' "$PHP"; then
    pass "PHP has 'View All Plugins' link to shop"
else
    fail "PHP missing 'View All Plugins' link"
fi

# Hero image has explicit dimensions
if grep -q 'width="540" height="354"' "$PHP"; then
    pass "Hero image has explicit width/height attributes"
else
    fail "Hero image missing explicit dimensions"
fi

# Hero image has data-no-lazy
if grep -q 'data-no-lazy="1"' "$PHP"; then
    pass "Hero image has data-no-lazy attribute"
else
    fail "Hero image missing data-no-lazy"
fi

# ── 8. JavaScript Checks ─────────────────────────────────────────
echo ""
echo "▸ JavaScript Checks"

JS="$ROOT_DIR/assets/app.js"

if grep -q 'gg-nav' "$JS"; then
    pass "JS handles navbar scroll"
else
    fail "JS missing navbar scroll handler"
fi

if grep -q 'gg-hamburger\|gg-drawer' "$JS"; then
    pass "JS handles mobile menu"
else
    fail "JS missing mobile menu handler"
fi

# ── 9. HTML ↔ PHP Parity ─────────────────────────────────────────
echo ""
echo "▸ HTML ↔ PHP Sync Checks"

# Both should have the same CTA IDs
for cta_id in "pipeline-free-cta" "pipeline-pro-cta"; do
    HTML_HAS=$(grep -c "id=\"$cta_id\"" "$HTML" || true)
    PHP_HAS=$(grep -c "id=\"$cta_id\"" "$PHP" || true)
    if [ "$HTML_HAS" -ge 1 ] && [ "$PHP_HAS" -ge 1 ]; then
        pass "Both HTML and PHP have #$cta_id"
    else
        fail "#$cta_id mismatch: HTML=$HTML_HAS, PHP=$PHP_HAS"
    fi
done

# Both should have gold button
HTML_GOLD=$(grep -c 'gg-btn-gold' "$HTML" || true)
PHP_GOLD=$(grep -c 'gg-btn-gold' "$PHP" || true)
if [ "$HTML_GOLD" -ge 1 ] && [ "$PHP_GOLD" -ge 1 ]; then
    pass "Both HTML and PHP use gold button"
else
    fail "Gold button mismatch: HTML=$HTML_GOLD, PHP=$PHP_GOLD"
fi

# Both should have Coming Soon cards
HTML_COMING=$(grep -c 'gg-coming-card' "$HTML" || true)
PHP_COMING=$(grep -c 'gg-coming-card' "$PHP" || true)
if [ "$HTML_COMING" -eq "$PHP_COMING" ]; then
    pass "Coming Soon card count matches ($HTML_COMING)"
else
    fail "Coming Soon mismatch: HTML=$HTML_COMING, PHP=$PHP_COMING"
fi

# ── 10. Deploy Script Checks ─────────────────────────────────────
echo ""
echo "▸ Deploy Script Checks"

DEPLOY="$ROOT_DIR/deploy.py"

if grep -q 'paramiko' "$DEPLOY"; then
    pass "Deploy uses paramiko for SFTP"
else
    fail "Deploy not using paramiko"
fi

if grep -q 'goat-getter.com' "$DEPLOY"; then
    pass "Deploy targets goat-getter.com"
else
    fail "Deploy not targeting goat-getter.com"
fi

if grep -q 'hello-elementor' "$DEPLOY"; then
    pass "Deploy targets hello-elementor theme"
else
    fail "Deploy not targeting hello-elementor theme"
fi

# ── Results ───────────────────────────────────────────────────────
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "  Results: $PASS passed, $FAIL failed (out of $TOTAL)"
echo "═══════════════════════════════════════════════════════════"
echo ""

if [ "$FAIL" -gt 0 ]; then
    exit 1
fi
