<?php
/**
 * Goat Getter — Custom Header
 * Replaces hello-elementor's default header.php
 * Renders our branded nav on all non-standalone pages.
 */
if (!defined('ABSPATH')) exit;
$t = get_template_directory_uri() . '/goat-getter-assets';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo $t; ?>/Goat Logo Favicon 512x512.png">
    <link rel="stylesheet" href="<?php echo $t; ?>/global.css?v=<?php echo time(); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <!-- ═══════════ NAVIGATION ═══════════ -->
    <nav class="gg-nav" id="gg-nav">
        <div class="gg-nav-inner">
            <a href="<?php echo home_url('/'); ?>" class="gg-logo" aria-label="Goat Getter Home">
                <img src="<?php echo $t; ?>/Goat Logo Horizontal.png" alt="Goat Getter" class="gg-logo-img">
            </a>
            <div class="gg-nav-links">
                <a href="<?php echo home_url('/#plugins'); ?>">Plugins</a>
                <a href="<?php echo home_url('/#why'); ?>">Why Goat Getter</a>
                <a href="<?php echo home_url('/#coming-soon'); ?>">Coming Soon</a>
                <a href="<?php echo home_url('/my-account/'); ?>" class="gg-btn gg-btn-sm gg-btn-outline">My Account</a>
            </div>
            <button class="gg-hamburger" id="gg-hamburger" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- ═══════════ MOBILE DRAWER ═══════════ -->
    <div class="gg-drawer" id="gg-drawer">
        <a href="<?php echo home_url('/#plugins'); ?>">Plugins</a>
        <a href="<?php echo home_url('/#why'); ?>">Why Goat Getter</a>
        <a href="<?php echo home_url('/#coming-soon'); ?>">Coming Soon</a>
        <a href="<?php echo home_url('/my-account/'); ?>" class="gg-btn gg-btn-outline">My Account</a>
    </div>

    <div class="gg-page-content">
