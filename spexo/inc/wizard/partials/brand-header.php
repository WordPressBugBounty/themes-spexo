<?php
/**
 * Spexo Theme Wizard — top bar (reference mark + theme logo + title).
 *
 * @package Spexo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_version = wp_get_theme()->get( 'Version' );
if ( ! is_string( $theme_version ) || '' === $theme_version ) {
	$theme_version = '1.0.0';
}
$favicon_url = add_query_arg(
	'ver',
	rawurlencode( $theme_version ),
	get_template_directory_uri() . '/assets/images/favicon.png'
);
?>
<header class="spexo-wizard-header" role="banner">
	<div class="spexo-wizard-header__brand">
		<img src="<?php echo esc_url( $favicon_url ); ?>" alt="" class="spexo-wizard-header__mark-img" loading="eager" />
		<span class="spexo-wizard-header__title"><?php esc_html_e( 'Spexo', 'spexo' ); ?></span>
	</div>
	<a href="#" class="skip-theme-wizard spexo-wizard-header__skip"><?php esc_html_e( 'Skip Setup', 'spexo' ); ?></a>
</header>
