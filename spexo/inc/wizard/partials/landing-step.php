<?php
/**
 * Spexo Theme Wizard — landing step (reference layout).
 *
 * @package Spexo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="spexo-wizard-landing">
	<div class="spexo-wizard-landing__icon" aria-hidden="true">
		<svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="30" height="30" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 30 30">
			<defs>
				<linearGradient id="linear-gradient" x1="0" y1="12.4" x2="7.6" y2="12.4" gradientUnits="userSpaceOnUse">
				<stop offset="0" stop-color="#5729d9"/>
				<stop offset="1" stop-color="#bf1864"/>
				</linearGradient>
				<linearGradient id="linear-gradient1" x1="13.2" y1="26.2" x2="21.9" y2="26.2" xlink:href="#linear-gradient"/>
				<linearGradient id="linear-gradient2" x1="5.4" y1="19.2" x2="16.2" y2="19.2" xlink:href="#linear-gradient"/>
				<linearGradient id="linear-gradient3" x1="7.2" y1="11.4" x2="30" y2="11.4" xlink:href="#linear-gradient"/>
				<linearGradient id="linear-gradient4" x1="17.9" y1="9.4" x2="23.2" y2="9.4" xlink:href="#linear-gradient"/>
				<linearGradient id="linear-gradient5" x1="0" y1="22.3" x2="4.8" y2="22.3" xlink:href="#linear-gradient"/>
				<linearGradient id="linear-gradient6" x1="0" y1="26.3" x2="7.3" y2="26.3" xlink:href="#linear-gradient"/>
				<linearGradient id="linear-gradient7" x1="5.3" y1="27.7" x2="9.8" y2="27.7" xlink:href="#linear-gradient"/>
			</defs>
			<path d="M7.6,8.1l-2.6.2c-.7,0-1.4.4-1.8,1L.3,13.8c-.3.5-.4,1-.1,1.5.2.5.7.9,1.2,1l2.4.4c.6-3.1,2-6.1,3.9-8.6h0Z" fill="url(#linear-gradient)"/>
			<path d="M13.3,26.2l.4,2.4c0,.5.4,1,.9,1.2.2,0,.4.1.7.1.3,0,.6,0,.9-.3l4.5-3c.6-.4,1-1.1,1-1.8l.2-2.6c-2.5,1.9-5.5,3.3-8.6,3.9h0Z" fill="url(#linear-gradient1)"/>
			<path d="M12.4,24.6c0,0,.2,0,.2,0,1.2-.2,2.4-.5,3.6-1L6.4,13.8c-.5,1.1-.8,2.3-1,3.5,0,.5,0,1,.4,1.3l5.5,5.5c.3.3.7.4,1.1.5h0Z" fill="url(#linear-gradient2)"/>
			<path d="M27.6,13.3c1.8-3.7,2.6-7.7,2.4-11.8,0-.8-.7-1.4-1.5-1.5h-1.3c-3.6,0-7.2.7-10.5,2.3-4,2.2-7.3,5.6-9.5,9.7h0c0,0,10.5,10.6,10.5,10.6,0,0,0,0,0,0,4.1-2.2,7.5-5.5,9.7-9.5ZM17.4,6.3c1.7-1.7,4.5-1.7,6.2,0,1.7,1.7,1.7,4.5,0,6.2-1.7,1.7-4.5,1.7-6.2,0-.8-.8-1.3-1.9-1.3-3.1,0-1.2.5-2.3,1.3-3.1Z" fill="url(#linear-gradient3)"/>
			<path d="M18.7,11.3c1,1,2.7,1,3.7,0,.5-.5.8-1.2.8-1.9,0-.7-.3-1.4-.8-1.9-1-1-2.7-1-3.7,0-1,1-1,2.7,0,3.7h0Z" fill="url(#linear-gradient4)"/>
			<path d="M.9,24.7c.2,0,.5,0,.6-.3l2.9-2.9c.4-.3.5-.8.2-1.2-.3-.4-.8-.5-1.2-.2,0,0-.2.1-.2.2l-2.9,2.8c-.3.3-.3.9,0,1.2.2.2.4.3.6.3Z" fill="url(#linear-gradient5)"/>
			<path d="M7,23c-.3-.3-.9-.4-1.2,0,0,0,0,0,0,0L.3,28.5c-.3.3-.3.9,0,1.2.2.2.4.3.6.3.2,0,.5,0,.6-.3l5.5-5.5c.3-.3.4-.9,0-1.2,0,0,0,0,0,0Z" fill="url(#linear-gradient6)"/>
			<path d="M8.4,25.6l-2.9,2.9c-.3.3-.3.9,0,1.2.2.2.4.3.6.3.2,0,.5,0,.6-.3l2.9-2.9c.3-.4.3-.9-.1-1.2-.3-.3-.8-.3-1.1,0Z" fill="url(#linear-gradient7)"/>
		</svg>
	</div>
	<h1 class="spexo-wizard-landing__heading"><?php esc_html_e( 'Welcome to Spexo Theme', 'spexo' ); ?></h1>
	<p class="spexo-wizard-landing__lead">
		<?php esc_html_e( "Let's get your website up and running. Choose how you'd like to start your journey with Spexo.", 'spexo' ); ?>
	</p>
	<div class="spexo-wizard-landing__actions">
		<button type="button" class="button button-primary next-step-btn spexo-wizard-btn spexo-wizard-btn--primary">
			<span class="spexo-wizard-btn__label"><?php esc_html_e( "Let's Get Started", 'spexo' ); ?></span>
			<span class="spexo-wizard-btn__chevron" aria-hidden="true">&rsaquo;</span>
		</button>
	</div>
	<p class="spexo-wizard-landing__consent-note">
		<?php esc_html_e( 'By clicking “Let’s Get Started,” you agree to install and activate Elementor, Redux Framework, and Spexo Addon for Elementor.', 'spexo' ); ?>
	</p>
</div>
