<?php
/**
 * Template Name: Contact Page
 * Template Post Type: page
 *
 * Contact layout: WhatsApp / phone cards + protected contact form + map.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

get_header();

$np_phone   = get_theme_mod( 'np_phone', '' );
$np_email   = get_theme_mod( 'np_email', '' );
$np_address = get_theme_mod( 'np_address', '' );
$np_hours   = get_theme_mod( 'np_topbar_hours', '' );
$np_wa      = np_whatsapp_link();
$np_map     = get_theme_mod( 'np_map_embed', '' );
?>

<main id="primary" class="site-main">

	<section class="np-page-hero">
		<div class="np-container">
			<h1 class="np-page-hero__title"><?php the_title(); ?></h1>
			<?php np_breadcrumbs(); ?>
		</div>
	</section>

	<div class="np-container np-section">
		<div class="np-contact-grid">

			<!-- Left: contact channels -->
			<div>
				<div class="np-contact-card">
					<h2 style="font-size:1.35rem;"><?php esc_html_e( 'Talk to a human, fast', 'netplus-circuit' ); ?></h2>
					<p class="np-muted" style="font-size:.92rem;"><?php esc_html_e( 'WhatsApp is the quickest way to reach our team — send a model number or photo and we will confirm compatibility.', 'netplus-circuit' ); ?></p>

					<div class="np-contact-info">
						<?php if ( $np_wa ) : ?>
							<div class="np-contact-info__item">
								<span class="np-contact-info__icon" style="background:#e6fbef;color:var(--np-wa);"><?php echo np_icon( 'whatsapp', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span>
									<span class="np-contact-info__label"><?php esc_html_e( 'WhatsApp', 'netplus-circuit' ); ?></span>
									<a class="np-contact-info__value" href="<?php echo esc_url( $np_wa ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Start a chat →', 'netplus-circuit' ); ?></a>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( $np_phone ) : ?>
							<div class="np-contact-info__item">
								<span class="np-contact-info__icon"><?php echo np_icon( 'phone', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span>
									<span class="np-contact-info__label"><?php esc_html_e( 'Hotline', 'netplus-circuit' ); ?></span>
									<a class="np-contact-info__value" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $np_phone ) ); ?>"><?php echo esc_html( $np_phone ); ?></a>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( $np_email ) : ?>
							<div class="np-contact-info__item">
								<span class="np-contact-info__icon"><?php echo np_icon( 'mail', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span>
									<span class="np-contact-info__label"><?php esc_html_e( 'Email', 'netplus-circuit' ); ?></span>
									<a class="np-contact-info__value" href="mailto:<?php echo esc_attr( antispambot( $np_email ) ); ?>"><?php echo esc_html( antispambot( $np_email ) ); ?></a>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( $np_address ) : ?>
							<div class="np-contact-info__item">
								<span class="np-contact-info__icon"><?php echo np_icon( 'pin', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span>
									<span class="np-contact-info__label"><?php esc_html_e( 'Store address', 'netplus-circuit' ); ?></span>
									<span class="np-contact-info__value"><?php echo esc_html( $np_address ); ?></span>
								</span>
							</div>
						<?php endif; ?>

						<?php if ( $np_hours ) : ?>
							<div class="np-contact-info__item">
								<span class="np-contact-info__icon"><?php echo np_icon( 'clock', 20); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
								<span>
									<span class="np-contact-info__label"><?php esc_html_e( 'Opening hours', 'netplus-circuit' ); ?></span>
									<span class="np-contact-info__value"><?php echo esc_html( $np_hours ); ?></span>
								</span>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $np_map ) : ?>
						<div class="np-map">
							<iframe src="<?php echo esc_url( $np_map ); ?>" loading="lazy" title="<?php esc_attr_e( 'Store location map', 'netplus-circuit' ); ?>" referrerpolicy="no-referrer-when-downgrade"></iframe>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Right: form -->
			<div class="np-contact-card">
				<?php
				while ( have_posts() ) :
					the_post();
					if ( trim( (string) get_the_content() ) ) {
						echo '<div class="np-entry-content">' . wp_kses_post( apply_filters( 'the_content', get_the_content() ) ) . '</div>';
					}
				endwhile;

				if ( ! trim( (string) get_post()->post_content ) ) {
					echo do_shortcode( '[np_contact_form]' );
				}
				?>
			</div>

		</div>
	</div>

</main>

<?php
get_footer();
