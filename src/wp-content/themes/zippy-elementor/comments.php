 <?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments and the comment
 * form. The actual display of comments is handled by a callback to
 * bakerfresh_comment() which is located at functions/comments-callback.php
 *
 * @package Bakerfresh WordPress theme
 */

// Return if password is required
if ( post_password_required() ) {
	return;
}

// Add classes to the comments main wrapper
$classes = 'comments-area';

if ( get_comments_number() != 0 ) {
	$classes .= ' has-comments';
}

if ( ! comments_open() && get_comments_number() < 1 ) {
	$classes .= ' empty-closed-comments';
	return;
}

?>

<section id="comments" class="<?php echo esc_attr( $classes ); ?>">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) :

		// Get comments title
		$comments_number = number_format_i18n( get_comments_number() );
		if ( '1' == $comments_number ) {
			$comments_title = esc_html__( 'This post has one comment', 'bakerfresh' );
		} else {
			$comments_title = sprintf( esc_html__( 'This post has %s comments', 'bakerfresh' ), $comments_number );
		}
		$comments_title = apply_filters( 'bakerfresh_comments_title', $comments_title ); ?>

		<h3 class="comments-title">
			<span class="text"><?php echo esc_html( $comments_title ); ?></span>
		</h3>

		<div class="comment-list">
			<?php
			// List comments
			wp_list_comments( array(
				'callback' 	=> 'bakerfresh_comment',
				'style'     => 'div',
				'format'    => 'html5',
			) ); ?>
		</div><!-- .comment-list -->

		<?php

        the_comments_navigation( array(
            'prev_text' => '<i class="lastudioicon-left-arrow"></i>'. esc_html__( 'Previous', 'bakerfresh' ),
            'next_text' => esc_html__( 'Next', 'bakerfresh' ) .'<i class="lastudioicon-right-arrow"></i>',
        ) );

		?>

		<?php
		// Display comments closed message
		if ( ! comments_open() && get_comments_number() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.' , 'bakerfresh' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php
	comment_form(
		array(
			'must_log_in'			=> '<p class="must-log-in">'.  sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a comment.', 'bakerfresh' ), '<a href="'. wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) .'">', '</a>' ) .'</p>',
			'logged_in_as'			=> '<p class="logged-in-as">'. esc_html__( 'Logged in as', 'bakerfresh' ) .' <a href="'. admin_url( 'profile.php' ) .'">'. esc_html($user_identity) .'</a>. <a href="' . wp_logout_url( get_permalink() ) .'" title="'. esc_attr__( 'Log out of this account', 'bakerfresh' ) .'">'. esc_html__( 'Log out &raquo;', 'bakerfresh' ) .'</a></p>',
			'comment_notes_before'	=> false,
			'comment_notes_after'	=> false,
			'comment_field'			=> '<div class="comment-textarea"><textarea name="comment" id="comment" cols="39" rows="4" tabindex="100" class="textarea-comment" placeholder="'. esc_attr__( 'Your Comment Here...', 'bakerfresh' ) .'"></textarea></div>',
			'id_submit'				=> 'comment-submit',
			'label_submit'			=> esc_html__( 'Post Comment', 'bakerfresh' ),
		)
	); ?>

</section><!-- #comments -->