<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Spexo
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( post_password_required() ) {
	return;
}

if ( ! post_type_supports( get_post_type(), 'comments' ) ) {
	return;
}

if ( ! have_comments() && ! comments_open() ) {
	return;
}

// Comment Reply Script.
if ( comments_open() && get_option( 'thread_comments' ) ) {
	wp_enqueue_script( 'comment-reply' );
}
?>
<section id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h3 class="title-comments">
			<?php
			$comments_number = get_comments_number();
			if ( '1' === $comments_number ) {
				printf( esc_html_x( 'One Response', 'comments title','spexo' ) );
			} else {
				printf(
					esc_html( /* translators: 1: number of comments */
						_nx(
							'%1$s Response',
							'%1$s Responses',
							$comments_number,
							'comments title',
							'spexo'
						)
					),
					esc_html( number_format_i18n( $comments_number ) )
				);
			}
			?>
		</h3>

		<?php the_comments_navigation(); ?>

	<ol class="comment-list">
		<?php
		wp_list_comments(
			array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 42,
            )
		);
		?>
	</ol><!-- .comment-list -->

	<?php the_comments_navigation(); ?>

<?php endif; // Check for have_comments(). ?>

<?php
comment_form(
	array(
		'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
		'title_reply_after'  => '</h3>',
	)
);
?>

</section><!-- .comments-area -->
