<?php
/**
 * Single blog post layout.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'np-single' ); ?>>

	<header class="np-single__header">
		<?php
		$np_cats = get_the_category();
		if ( $np_cats ) :
			?>
			<div class="np-single__cats">
				<?php foreach ( $np_cats as $np_cat ) : ?>
					<a class="np-badge np-badge--info" href="<?php echo esc_url( get_category_link( $np_cat ) ); ?>"><?php echo esc_html( $np_cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<h1 class="np-single__title entry-title"><?php the_title(); ?></h1>

		<div class="np-single__meta">
			<?php np_posted_by(); ?>
			<?php np_posted_on(); ?>
			<?php np_comment_count_text(); ?>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="np-single__thumb">
			<?php the_post_thumbnail( 'large', array( 'class' => 'entry-thumb' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="entry-content np-entry-content">
		<?php
		the_content();

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'netplus-circuit' ),
			'after'  => '</div>',
		) );
		?>
	</div>

	<footer class="np-entry-footer">
		<?php
		$np_tags = get_the_tag_list( '<div class="np-tags"><span class="np-muted">' . esc_html__( 'Tags:', 'netplus-circuit' ) . '</span> ', ' ', '</div>' );
		echo $np_tags ? wp_kses_post( $np_tags ) : '<span></span>';
		?>
		<div class="np-flex" style="gap:8px;">
			<span class="np-muted" style="font-size:.82rem;"><?php esc_html_e( 'Share:', 'netplus-circuit' ); ?></span>
			<?php
			$np_share_url   = rawurlencode( get_permalink() );
			$np_share_title = rawurlencode( get_the_title() );
			?>
			<a class="np-icon-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $np_share_url ); ?>" aria-label="<?php esc_attr_e( 'Share on Facebook', 'netplus-circuit' ); ?>"><?php echo np_icon( 'fb', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php
			$np_wa_share = np_whatsapp_link();
			if ( $np_wa_share ) :
				?>
				<a class="np-icon-btn np-icon-btn--wa" target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo esc_attr( $np_share_title . '%20' . $np_share_url ); ?>" aria-label="<?php esc_attr_e( 'Share on WhatsApp', 'netplus-circuit' ); ?>"><?php echo np_icon( 'whatsapp', 16); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
			<?php endif; ?>
		</div>
	</footer>

	<?php
	// Author box.
	if ( get_the_author_meta( 'description' ) ) :
		?>
		<div class="np-author-box">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 128 ); ?>
			<div>
				<div class="np-author-box__name"><?php the_author(); ?></div>
				<p class="np-author-box__bio"><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
				<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" style="font-size:.84rem;font-weight:700;">
					<?php esc_html_e( 'View all posts', 'netplus-circuit' ); ?> →
				</a>
			</div>
		</div>
	<?php endif; ?>

	<?php
	// Prev/next navigation.
	$np_prev = get_previous_post();
	$np_next = get_next_post();
	if ( $np_prev || $np_next ) :
		?>
		<nav class="np-post-nav" aria-label="<?php esc_attr_e( 'Post navigation', 'netplus-circuit' ); ?>">
			<?php if ( $np_prev ) : ?>
				<a class="nav-previous" href="<?php echo esc_url( get_permalink( $np_prev ) ); ?>">
					<span class="np-post-nav__label">← <?php esc_html_e( 'Previous', 'netplus-circuit' ); ?></span>
					<span class="np-post-nav__title"><?php echo esc_html( get_the_title( $np_prev ) ); ?></span>
				</a>
			<?php else : ?><span></span><?php endif; ?>
			<?php if ( $np_next ) : ?>
				<a class="nav-next" href="<?php echo esc_url( get_permalink( $np_next ) ); ?>">
					<span class="np-post-nav__label"><?php esc_html_e( 'Next', 'netplus-circuit' ); ?> →</span>
					<span class="np-post-nav__title"><?php echo esc_html( get_the_title( $np_next ) ); ?></span>
				</a>
			<?php endif; ?>
		</nav>
	<?php endif; ?>

</article>

<?php
// Related posts.
$np_related = get_posts( array(
	'category__in'   => wp_get_post_categories( get_the_ID() ),
	'numberposts'    => 3,
	'post__not_in'   => array( get_the_ID() ),
	'no_found_rows'  => true,
) );
if ( $np_related ) :
	?>
	<section class="np-section--tight" style="padding-top:36px;" aria-label="<?php esc_attr_e( 'Related articles', 'netplus-circuit' ); ?>">
		<?php np_section_heading( __( 'You may also like', 'netplus-circuit' ) ); ?>
		<div class="np-posts">
			<?php foreach ( $np_related as $np_r ) :
				$np_rcats = get_the_category( $np_r->ID );
				?>
				<article class="np-post-card">
					<a class="np-post-card__media" href="<?php echo esc_url( get_permalink( $np_r ) ); ?>" aria-hidden="true" tabindex="-1">
						<?php if ( has_post_thumbnail( $np_r ) ) : ?>
							<?php echo get_the_post_thumbnail( $np_r, 'np-post-card', array( 'loading' => 'lazy' ) ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $np_rcats ) ) : ?>
							<span class="np-post-card__cat"><?php echo esc_html( $np_rcats[0]->name ); ?></span>
						<?php endif; ?>
					</a>
					<div class="np-post-card__body">
						<div class="np-post-card__meta"><span><?php echo esc_html( get_the_date( '', $np_r ) ); ?></span></div>
						<h3 class="np-post-card__title"><a href="<?php echo esc_url( get_permalink( $np_r ) ); ?>"><?php echo esc_html( get_the_title( $np_r ) ); ?></a></h3>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>
