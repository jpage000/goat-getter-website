<?php
/**
 * Plugin Name: Goat Getter Theme Setup
 * Description: Programmatically creates branded Elementor Theme Builder templates
 *              on first activation, then enqueues global CSS on every request.
 *
 * Upload to: wp-content/mu-plugins/goat-getter-theme-setup.php
 */
if (!defined('ABSPATH')) exit;

/* ────────────────────────────────────────────────────────────────────
 * 1.  ENQUEUE GLOBAL CSS ON EVERY FRONT-END REQUEST
 * ─────────────────────────────────────────────────────────────────── */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'gg-global',
        get_template_directory_uri() . '/goat-getter-assets/global.css',
        [],
        filemtime(get_template_directory() . '/goat-getter-assets/global.css')
    );
}, 999);                    // high priority — loads after theme / Elementor CSS

/* ────────────────────────────────────────────────────────────────────
 * 2.  MOBILE HAMBURGER MENU JS (tiny inline script for all pages)
 * ─────────────────────────────────────────────────────────────────── */
add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('gg-hamburger');
        var drawer = document.getElementById('gg-drawer');
        if (btn && drawer) {
            btn.addEventListener('click', function () {
                drawer.classList.toggle('open');
            });
            drawer.querySelectorAll('a').forEach(function (a) {
                a.addEventListener('click', function () { drawer.classList.remove('open'); });
            });
        }
    });
    </script>
    <?php
}, 99);

/* ────────────────────────────────────────────────────────────────────
 * 3.  ONE-TIME THEME BUILDER SETUP
 *     Creates all templates on the first admin page-load after deploy.
 *     Re-run by deleting the option:  wp option delete gg_theme_v2_done
 * ─────────────────────────────────────────────────────────────────── */
add_action('admin_init', function () {
    if (get_option('gg_theme_v2_done')) return;
    if (!current_user_can('manage_options')) return;
    if (!class_exists('\\Elementor\\Plugin')) return;

    $t = get_template_directory_uri() . '/goat-getter-assets';

    /* ── shared HTML fragments ─────────────────────────────────── */
    $nav_html = <<<HTML
<nav class="gg-nav" id="gg-nav">
    <div class="gg-nav-inner">
        <a href="/" class="gg-logo" aria-label="Home">
            <img src="{$t}/Goat Logo Horizontal.png" alt="Goat Getter" class="gg-logo-img">
        </a>
        <div class="gg-nav-links">
            <a href="/#plugins">Plugins</a>
            <a href="/#why">Why Goat Getter</a>
            <a href="/#coming-soon">Coming Soon</a>
            <a href="/my-account/" class="gg-btn gg-btn-sm gg-btn-outline">My Account</a>
        </div>
        <button class="gg-hamburger" id="gg-hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
<div class="gg-drawer" id="gg-drawer">
    <a href="/#plugins">Plugins</a>
    <a href="/#why">Why Goat Getter</a>
    <a href="/#coming-soon">Coming Soon</a>
    <a href="/my-account/" class="gg-btn gg-btn-outline">My Account</a>
</div>
HTML;

    $year = date('Y');
    $footer_html = <<<HTML
<footer class="gg-footer">
    <div class="gg-footer-inner">
        <div class="gg-footer-brand">
            <img src="{$t}/Goat Logo horizontal white.png" alt="Goat Getter" class="gg-footer-logo">
            <p>Powerful plugins for the Gravity Forms ecosystem.</p>
        </div>
        <div class="gg-footer-col">
            <h5>Plugins</h5>
            <a href="https://gravitypipeline.io">Gravity Pipeline</a>
        </div>
        <div class="gg-footer-col">
            <h5>Account</h5>
            <a href="/my-account/">My Account</a>
            <a href="/my-account/orders/">Orders</a>
            <a href="/my-account/downloads/">Downloads</a>
        </div>
        <div class="gg-footer-col">
            <h5>Company</h5>
            <a href="mailto:hello@goat-getter.com">Contact</a>
            <a href="/terms/">Terms</a>
            <a href="/privacy/">Privacy</a>
        </div>
    </div>
    <div class="gg-footer-bottom">
        <div class="gg-container">
            <p>&copy; {$year} Goat Getter. All rights reserved.</p>
        </div>
    </div>
</footer>
HTML;

    $error_404_html = <<<HTML
<div style="text-align:center; padding:120px 2rem 80px; min-height:60vh; display:flex; flex-direction:column; align-items:center; justify-content:center; font-family:'Inter',sans-serif;">
    <div style="font-size:8rem; font-weight:900; color:#1B2547; line-height:1; margin-bottom:16px;">404</div>
    <h1 style="font-size:2rem; font-weight:800; color:#1B2547; margin-bottom:12px;">Page Not Found</h1>
    <p style="color:#64748b; font-size:1.1rem; max-width:480px; margin-bottom:32px;">The page you're looking for doesn't exist or has been moved.</p>
    <a href="/" style="display:inline-flex; align-items:center; padding:14px 32px; border-radius:50px; background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff; font-weight:600; text-decoration:none; box-shadow:0 4px 20px rgba(124,58,237,.25); transition:all .25s;">← Back to Home</a>
</div>
HTML;

    $search_html = <<<HTML
<div style="padding:120px 2rem 40px; max-width:1200px; margin:0 auto; font-family:'Inter',sans-serif;">
    <h1 style="font-size:2rem; font-weight:800; color:#1B2547; margin-bottom:8px;">Search Results</h1>
    <p style="color:#64748b; margin-bottom:32px;">Showing results for your search query.</p>
</div>
HTML;

    /* ── Helper: build Elementor widget data ─────────────────── */
    function gg_id() { return substr(md5(uniqid(mt_rand(), true)), 0, 7); }

    function gg_html_widget($html) {
        return [
            'id' => gg_id(), 'elType' => 'widget',
            'widgetType' => 'html',
            'settings' => ['html' => $html],
        ];
    }

    function gg_section($elements, $settings = []) {
        $defaults = [
            'structure' => '10',
            'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
            'margin'  => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true],
        ];
        return [
            'id' => gg_id(), 'elType' => 'section',
            'settings' => array_merge($defaults, $settings),
            'elements' => [
                [
                    'id' => gg_id(), 'elType' => 'column',
                    'settings' => ['_column_size' => 100],
                    'elements' => $elements,
                ],
            ],
        ];
    }

    function gg_theme_widget($type, $settings = []) {
        return [
            'id' => gg_id(), 'elType' => 'widget',
            'widgetType' => $type,
            'settings' => $settings,
        ];
    }

    function gg_content_section($widgets) {
        return gg_section($widgets, [
            'content_width' => ['unit' => 'px', 'size' => 1200],
            'padding' => ['unit' => 'px', 'top' => '120', 'right' => '30', 'bottom' => '80', 'left' => '30', 'isLinked' => false],
        ]);
    }

    /* ── Helper: upsert Elementor template ──────────────────── */
    function gg_upsert_template($title, $type, $location, $conditions, $data) {
        // Check if template already exists by title
        $existing = get_posts([
            'post_type'   => 'elementor_library',
            'post_status' => 'any',
            'title'       => $title,
            'numberposts' => 1,
        ]);

        $post_data = [
            'post_title'  => $title,
            'post_status' => 'publish',
            'post_type'   => 'elementor_library',
            'meta_input'  => [
                '_elementor_edit_mode'     => 'builder',
                '_elementor_template_type' => $type,
                '_elementor_data'          => wp_slash(json_encode($data)),
                '_elementor_conditions'    => $conditions,
            ],
        ];

        if (!empty($existing)) {
            $post_data['ID'] = $existing[0]->ID;
            wp_update_post($post_data);
            $id = $existing[0]->ID;
        } else {
            $id = wp_insert_post($post_data);
        }

        // Set template type taxonomy
        wp_set_object_terms($id, $type, 'elementor_library_type');

        // Set location for theme builder
        if ($location) {
            update_post_meta($id, '_elementor_location', $location);
        }

        // Clear cached CSS
        delete_post_meta($id, '_elementor_css');

        return $id;
    }

    /* ══════════════════════════════════════════════════════════
     *  CREATE ALL THEME BUILDER TEMPLATES
     * ═════════════════════════════════════════════════════════ */

    // ── HEADER ─────────────────────────────────────────────────
    $header_data = [gg_section([gg_html_widget($nav_html)])];
    gg_upsert_template(
        'GG Header',
        'header',
        'header',
        ['include/general'],
        $header_data
    );

    // ── FOOTER ─────────────────────────────────────────────────
    $footer_data = [gg_section([gg_html_widget($footer_html)])];
    gg_upsert_template(
        'GG Footer',
        'footer',
        'footer',
        ['include/general'],
        $footer_data
    );

    // ── SINGLE PAGE ────────────────────────────────────────────
    $single_page_data = [
        gg_content_section([
            gg_theme_widget('theme-post-title', [
                'title'    => '',
                'header_size' => 'h1',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_weight' => '800',
                'typography_font_size' => ['unit' => 'px', 'size' => 36],
                'title_color' => '#1B2547',
                'align' => 'left',
            ]),
            gg_theme_widget('theme-post-content', [
                'align' => 'left',
            ]),
        ]),
    ];
    gg_upsert_template(
        'GG Single Page',
        'single',
        'single',
        ['include/singular/page'],
        $single_page_data
    );

    // ── SINGLE POST ────────────────────────────────────────────
    $single_post_data = [
        gg_content_section([
            gg_theme_widget('theme-post-title', [
                'header_size' => 'h1',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_weight' => '800',
                'typography_font_size' => ['unit' => 'px', 'size' => 36],
                'title_color' => '#1B2547',
            ]),
            gg_theme_widget('post-info', [
                'meta_data' => [
                    ['type' => 'author'],
                    ['type' => 'date'],
                    ['type' => 'comments'],
                ],
                'text_color' => '#64748b',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_size' => ['unit' => 'px', 'size' => 14],
            ]),
            gg_theme_widget('theme-post-featured-image', [
                'border_border' => 'solid',
                'border_width'  => ['unit' => 'px', 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1'],
                'border_color'  => '#e5e7eb',
                'border_radius' => ['unit' => 'px', 'top' => '20', 'right' => '20', 'bottom' => '20', 'left' => '20'],
                'image_spacing' => ['unit' => 'px', 'size' => 32],
            ]),
            gg_theme_widget('theme-post-content', []),
        ]),
    ];
    gg_upsert_template(
        'GG Single Post',
        'single',
        'single',
        ['include/singular/post'],
        $single_post_data
    );

    // ── SINGLE PRODUCT ─────────────────────────────────────────
    $single_product_data = [
        gg_content_section([
            gg_theme_widget('woocommerce-breadcrumb', [
                'text_color' => '#64748b',
                'link_color' => '#7c3aed',
            ]),
        ]),
        gg_section([
            gg_theme_widget('woocommerce-product-images', []),
        ], [
            'structure' => '20',   // 2-column layout
            'content_width' => ['unit' => 'px', 'size' => 1200],
            'padding' => ['unit' => 'px', 'top' => '0', 'right' => '30', 'bottom' => '40', 'left' => '30', 'isLinked' => false],
        ]),
        // NOTE: Elementor's section structure '20' means 2 columns.
        // But defining 2 columns in a section requires 2 column elements.
        // Since that's complex, let's use a simpler flat approach:
    ];
    // Rebuild single product with a simpler approach
    $single_product_data = [
        gg_content_section([
            gg_theme_widget('woocommerce-breadcrumb', [
                'text_color' => '#64748b',
                'link_color' => '#7c3aed',
            ]),
            gg_html_widget('<div style="height:20px;"></div>'),
            gg_theme_widget('wc-add-to-cart', []),
            gg_theme_widget('woocommerce-product-data-tabs', []),
            gg_html_widget('<div style="height:40px;"></div>'),
            gg_theme_widget('woocommerce-product-related', [
                'columns' => 4,
                'rows' => 1,
            ]),
        ]),
    ];
    gg_upsert_template(
        'GG Single Product',
        'single',
        'single',
        ['include/singular/product'],
        $single_product_data
    );

    // ── PRODUCT ARCHIVE / SHOP ─────────────────────────────────
    $product_archive_data = [
        gg_content_section([
            gg_theme_widget('wc-archive-products', [
                'columns' => 3,
                'rows'    => 6,
                'paginate' => 'yes',
            ]),
        ]),
    ];
    gg_upsert_template(
        'GG Product Archive',
        'archive',
        'archive',
        ['include/archive/product'],
        $product_archive_data
    );

    // ── ARCHIVE (Posts) ────────────────────────────────────────
    $archive_data = [
        gg_content_section([
            gg_theme_widget('archive-title', [
                'header_size' => 'h1',
                'typography_typography' => 'custom',
                'typography_font_family' => 'Inter',
                'typography_font_weight' => '800',
                'typography_font_size' => ['unit' => 'px', 'size' => 36],
                'title_color' => '#1B2547',
            ]),
            gg_html_widget('<div style="height:32px;"></div>'),
            gg_theme_widget('archive-posts', [
                'columns' => 3,
                'image_position' => 'top',
                'masonry' => '',
                'show_title' => 'yes',
                'show_excerpt' => 'yes',
                'show_read_more' => 'yes',
                'read_more_text' => 'Read More →',
                'meta_data' => ['date', 'author'],
                'card_border_radius' => ['unit' => 'px', 'size' => 20],
                'card_shadow_box_shadow' => ['horizontal' => 0, 'vertical' => 2, 'blur' => 16, 'spread' => 0, 'color' => 'rgba(27,37,71,0.08)'],
                'title_color' => '#1B2547',
                'excerpt_color' => '#64748b',
            ]),
        ]),
    ];
    gg_upsert_template(
        'GG Archive',
        'archive',
        'archive',
        ['include/archive'],
        $archive_data
    );

    // ── SEARCH RESULTS ─────────────────────────────────────────
    $search_data = [
        gg_section([gg_html_widget($search_html)]),
        gg_content_section([
            gg_theme_widget('search-form', [
                'skin' => 'full_screen',
                'placeholder' => 'Search plugins, guides, and more...',
            ]),
            gg_html_widget('<div style="height:24px;"></div>'),
            gg_theme_widget('archive-posts', [
                'columns' => 2,
                'show_title' => 'yes',
                'show_excerpt' => 'yes',
                'show_read_more' => 'yes',
                'read_more_text' => 'Read More →',
                'title_color' => '#1B2547',
                'excerpt_color' => '#64748b',
            ]),
        ]),
    ];
    gg_upsert_template(
        'GG Search Results',
        'search-results',
        'archive',
        ['include/search'],
        $search_data
    );

    // ── 404 PAGE ───────────────────────────────────────────────
    $error_data = [gg_section([gg_html_widget($error_404_html)])];
    gg_upsert_template(
        'GG 404',
        'error-404',
        'single',
        ['include/404'],
        $error_data
    );

    /* ── Deactivate OLD Elementor header/footer templates ────── */
    // Keep them in DB but remove their display conditions so they
    // don't conflict with our new GG Header / GG Footer.
    foreach ([309, 319] as $old_id) {
        $post = get_post($old_id);
        if ($post && $post->post_type === 'elementor_library') {
            delete_post_meta($old_id, '_elementor_conditions');
            // Rename so it's clear it's archived
            if (strpos($post->post_title, '[Old]') === false) {
                wp_update_post([
                    'ID'         => $old_id,
                    'post_title' => '[Old] ' . $post->post_title,
                ]);
            }
        }
    }

    // Clear all Elementor caches
    if (class_exists('\\Elementor\\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }

    update_option('gg_theme_v2_done', time());
    error_log('[Goat Getter] Theme Builder templates created/updated at ' . date('Y-m-d H:i:s'));
});
