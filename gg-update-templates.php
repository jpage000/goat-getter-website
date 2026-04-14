<?php
/**
 * Goat Getter — Update Elementor Header & Footer Templates
 *
 * Upload to the site root via SFTP, run once via browser, then DELETE.
 * URL: https://goat-getter.com/gg-update-templates.php
 */
define('SHORTINIT', false);
require_once __DIR__ . '/wp-load.php';

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized. Please log in as admin first.');
}

// ─── Google Fonts enqueue (add to theme) ───
$font_css = "
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
";

// ─── HEADER TEMPLATE (ID 309) ───
$header_html = '
<nav class="gg-nav" id="gg-nav" style="
    position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
    background: rgba(255,255,255,0.95); backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(27,37,71,0.08);
    padding: 0 40px; height: 72px;
    display: flex; align-items: center; justify-content: space-between;
    font-family: Inter, sans-serif;
">
    <a href="/" style="display:flex; align-items:center; text-decoration:none;">
        <img src="' . get_template_directory_uri() . '/goat-getter-assets/Goat%20Logo%20Horizontal.png" 
             alt="Goat Getter" style="height:68px; width:auto;">
    </a>
    <div style="display:flex; align-items:center; gap:32px;">
        <a href="/#plugins" style="color:#1B2547; text-decoration:none; font-weight:500; font-size:15px;">Plugins</a>
        <a href="/#why" style="color:#1B2547; text-decoration:none; font-weight:500; font-size:15px;">Why Goat Getter</a>
        <a href="/#coming-soon" style="color:#1B2547; text-decoration:none; font-weight:500; font-size:15px;">Coming Soon</a>
        <a href="/my-account/" style="
            display:inline-flex; align-items:center; justify-content:center;
            padding: 10px 24px; border-radius: 50px;
            border: 2px solid #1B2547; color: #1B2547;
            font-weight: 600; font-size: 14px; text-decoration: none;
            transition: all 0.3s ease;
        ">My Account</a>
    </div>
</nav>
<div style="height:72px;"></div>
';

// ─── FOOTER TEMPLATE (ID 319) ───
$footer_html = '
<footer style="
    background: #1B2547; color: rgba(255,255,255,0.7);
    font-family: Inter, sans-serif; padding: 0;
">
    <div style="max-width:1200px; margin:0 auto; padding:64px 24px 32px; display:flex; flex-wrap:wrap; gap:48px;">
        <div style="flex:1; min-width:240px;">
            <img src="' . get_template_directory_uri() . '/goat-getter-assets/Goat%20Logo%20horizontal%20white.png" 
                 alt="Goat Getter" style="height:48px; margin-bottom:16px;">
            <p style="color:rgba(255,255,255,0.6); font-size:14px; line-height:1.6;">Powerful plugins for the Gravity Forms ecosystem.</p>
        </div>
        <div style="min-width:140px;">
            <h5 style="color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Plugins</h5>
            <a href="https://gravitypipeline.io" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">Gravity Pipeline</a>
        </div>
        <div style="min-width:140px;">
            <h5 style="color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Account</h5>
            <a href="/my-account/" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">My Account</a>
            <a href="/my-account/orders/" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">Orders</a>
            <a href="/my-account/downloads/" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">Downloads</a>
        </div>
        <div style="min-width:140px;">
            <h5 style="color:#fff; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:16px;">Company</h5>
            <a href="mailto:hello@goat-getter.com" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">Contact</a>
            <a href="/terms/" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">Terms</a>
            <a href="/privacy/" style="display:block; color:rgba(255,255,255,0.6); text-decoration:none; font-size:14px; margin-bottom:8px;">Privacy</a>
        </div>
    </div>
    <div style="border-top:1px solid rgba(255,255,255,0.1); padding:24px; text-align:center;">
        <p style="color:rgba(255,255,255,0.4); font-size:13px; margin:0;">&copy; ' . date('Y') . ' Goat Getter. All rights reserved.</p>
    </div>
</footer>
';

// ─── Update the Elementor templates ───
$header_id = 309;
$footer_id = 319;

// Build Elementor data for header
$header_data = [
    [
        'id' => uniqid(),
        'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'],
            'margin' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'],
        ],
        'elements' => [
            [
                'id' => uniqid(),
                'elType' => 'column',
                'settings' => ['_column_size' => 100],
                'elements' => [
                    [
                        'id' => uniqid(),
                        'elType' => 'widget',
                        'widgetType' => 'html',
                        'settings' => [
                            'html' => $header_html,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

$footer_data = [
    [
        'id' => uniqid(),
        'elType' => 'section',
        'settings' => [
            'structure' => '10',
            'padding' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'],
            'margin' => ['unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0'],
        ],
        'elements' => [
            [
                'id' => uniqid(),
                'elType' => 'column',
                'settings' => ['_column_size' => 100],
                'elements' => [
                    [
                        'id' => uniqid(),
                        'elType' => 'widget',
                        'widgetType' => 'html',
                        'settings' => [
                            'html' => $footer_html,
                        ],
                    ],
                ],
            ],
        ],
    ],
];

// Save header
update_post_meta($header_id, '_elementor_data', wp_slash(json_encode($header_data)));
update_post_meta($header_id, '_elementor_edit_mode', 'builder');
// Clear Elementor CSS cache for this template
delete_post_meta($header_id, '_elementor_css');

// Save footer
update_post_meta($footer_id, '_elementor_data', wp_slash(json_encode($footer_data)));
update_post_meta($footer_id, '_elementor_edit_mode', 'builder');
delete_post_meta($footer_id, '_elementor_css');

// Clear Elementor global cache
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}

echo '<h1>✅ Templates Updated!</h1>';
echo '<h2>Header (ID 309)</h2>';
echo '<p>Nav: Plugins | Why Goat Getter | Coming Soon | My Account</p>';
echo '<h2>Footer (ID 319)</h2>';
echo '<p>Columns: Brand, Plugins, Account, Company</p>';
echo '<p><strong>Next:</strong> Delete this file from the server, then visit <a href="/">goat-getter.com</a> to verify.</p>';

// Also flush rewrite rules
flush_rewrite_rules();
echo '<p>Permalinks flushed. ✅</p>';
