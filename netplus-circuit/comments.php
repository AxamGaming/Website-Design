<?php
/**
 * Comments template (blog). Product reviews are handled by WooCommerce.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div id="comments" class="np-comments comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="np-comments__title">
			<?php
			$np_comment_count = (int) get_comments_number();
			printf(
				esc_html(
					/* translators: 1: comment count, 2: title. */
					_n( '%1$s comment on “%2$s”', '%1$s comments on “%2$s”', $np_comment_count, 'netplus-circuit' )
				),
				esc_html( number_format_i18n( $np_comment_count ) ),
				esc_html( get_the_title() )
			);
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 84,
			) );
			?>
		</ol>

		<?php
		the_comments_navigation( array(
			'before' => '<nav class="np-pagination" aria-label="' . esc_attr__( 'Comment navigation', 'netplus-circuit' ) . '">',
			'after'  => '</nav>',
		) );

		if ( ! comments_open() ) :
			?>
			<p class="np-comments-closed"><?php esc_html_e( 'Comments are closed for this post.', 'netplus-circuit' ); ?></p>
			<?php
		endif;
		?>
	<?php endif; ?>

	<?php
	comment_form( array(
		'class_form'    => 'comment-form np-comment-form',
		'title_reply'   => __( 'Leave a comment', 'netplus-circuit' ),
		'label_submit'  => __( 'Post comment', 'netplus-circuit' ),
		'class_submit'  => 'np-btn np-btn--primary',
		'comment_notes_before' => '<p class="comment-notes np-muted">' . esc_html__( 'Your email address will not be published. Be kind — all comments are moderated.', 'netplus-circuit' ) . '</p>',
	) );
	?>

</div>
