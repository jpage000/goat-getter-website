#!/bin/bash
# ─────────────────────────────────────────────────────────────────
# Goat Getter Website — Unit Tests
# Validates HTML structure, CSS integrity, PHP template correctness,
# and asset references.
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
    "assets/app.js" \
    "assets/gp-logo.svg" \
    "assets/Goat Logo Horizontal.png" \
    "assets/Goat Logo Favicon 512x512.png" \
    "assets/Goat Logo horizontal white.png" \
    "assets/hero-pipeline.png"; do
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

# Free card
if grep -q 'pipeline-free-cta' "$HTML"; then
    pass "Free pipeline CTA exists"
else
    fail "Missing Free pipeline CTA"
fi

# Pro card
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

# GP logo in cards (not generic SVG)
GP_LOGO_COUNT=$(grep -c 'gp-logo.svg' "$HTML" || true)
if [ "$GP_LOGO_COUNT" -ge 2 ]; then
    pass "GP logo used in both cards ($GP_LOGO_COUNT refs)"
else
    fail "GP logo not in both cards (found $GP_LOGO_COUNT)"
fi

# ── 4. Coming Soon Section ───────────────────────────────────────
echo ""
echo "▸ Coming Soon Checks"

for plugin in "Gravity Chat" "Gravity AMS" "Gravity Reports"; do
    if grep -q "$plugin" "$HTML"; then
        pass "Coming Soon: $plugin listed"
    else
        fail "Coming Soon: missing $plugin"
    fi
done

# Gravity Chat NOT in plugins section (should only be in coming-soon)
CHAT_IN_PLUGINS=$(sed -n '/<section id="plugins"/,/<\/section>/p' "$HTML" | grep -c 'Gravity Chat' || true)
if [ "$CHAT_IN_PLUGINS" -eq 0 ]; then
    pass "Gravity Chat NOT in active plugins (correctly in Coming Soon)"
else
    fail "Gravity Chat incorrectly in active plugins section"
fi

# ── 5. CSS Checks ────────────────────────────────────────────────
echo ""
echo "▸ CSS Checks"

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

# ── 6. PHP Template Checks ────────────────────────────────────────
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

# ── 7. JavaScript Checks ─────────────────────────────────────────
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

# ── 8. HTML ↔ PHP Parity ─────────────────────────────────────────
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

# Both should have same number of Coming Soon cards
HTML_COMING=$(grep -c 'gg-coming-card' "$HTML" || true)
PHP_COMING=$(grep -c 'gg-coming-card' "$PHP" || true)
if [ "$HTML_COMING" -eq "$PHP_COMING" ]; then
    pass "Coming Soon card count matches ($HTML_COMING)"
else
    fail "Coming Soon mismatch: HTML=$HTML_COMING, PHP=$PHP_COMING"
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
