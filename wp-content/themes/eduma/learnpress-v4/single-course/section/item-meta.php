<?php
/**
 * Template for displaying item section meta in single course.
 *
 * This template can be overridden by copying it to yourtheme/learnpress/single-course/section/item-meta.php.
 *
 * @author   ThimPress
 * @package  Learnpress/Templates
 * @version  4.0.0
 */

/**
 * Prevent loading this file directly
 */
defined( 'ABSPATH' ) || exit();
?>


<div class="course-item-meta">
	<?php if ( $item->is_preview() ) : ?>
		<a title="<?php esc_html_e( 'Previews', 'eduma' ); ?>" class="lesson-preview button-load-item" href="<?php echo $item->get_permalink(); ?>" data-id="<?php echo $item->get_id(); ?>" data-complete-nonce="<?php echo wp_create_nonce( 'learn-press-complete-' . $item->get_item_type() . '-' . $item->get_id() ); ?>">
			<i class="fa fa-eye"
			   data-preview="<?php esc_html_e( 'Preview', 'eduma' ); ?>"></i>
		</a>
 	<?php else : ?>
		<span class="item-meta course-item-status" title="<?php echo esc_attr( $item->get_status_title() ); ?>"></span>
	<?php endif; ?>

</div>
<?php do_action( 'learn-press/course-section-item/before-' . $item->get_item_type() . '-meta', $item ); ?>
<?php do_action( 'learn-press/course-section-item/after-' . $item->get_item_type() . '-meta', $item ); ?>

