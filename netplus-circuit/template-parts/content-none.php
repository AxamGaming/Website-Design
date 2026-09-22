<?php
/**
 * No-results template.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="np-empty no-results not-found">
	<div class="np-empty__code">🔍</div>
	<h1><?php esc_html_e( 'Nothing found', 'netplus-circuit' ); ?></h1>
	<p>
		<?php if ( is_search() ) : ?>
			<?php esc_html_e( 'Sorry, no results matched your search. Try different keywords, or tell us what you need on WhatsApp — we source hard-to-find parts!', 'netplus-circuit' ); ?>
		<?php elseif ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<?php
			printf(
				wp_kses_post(
					/* translators: %s: new post URL */
					__( 'Ready to publish your first post? <a href="%s">Get started here</a>.', 'netplus-circuit' )
				),
				esc_url( admin_url( 'post-new.php' ) )
			);
			?>
		<?php else : ?>
			<?php esc_html_e( 'It seems we can’t find what you’re looking for. Try a search below.', 'netplus-circuit' ); ?>
		<?php endif; ?>
	</p>
	<div class="np-empty__search"><?php get_search_form(); ?></div>
	<?php
	$np_wa = np_whatsapp_link( __( 'Hi NetPlus! I could not find a product on your website…', 'netplus-circuit' ) );
	if ( $np_wa ) :
		?>
		<a class="np-btn np-btn--wa" href="<?php echo esc_url( $np_wa ); ?>" target="_blank" rel="noopener">
			<?php echo np_icon( 'whatsapp', 18); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php esc_html_e( 'Ask us on WhatsApp', 'netplus-circuit' ); ?>
		</a>
	<?php endif; ?>
</section>
