<?php
/**
 * Petals Note email (plain text)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/plain/petals-note.php.
 *
 */

defined( 'ABSPATH' ) || exit;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo printf( __( 'You\'ve received a message from Petals regarding an order you received - please see below.', 'bakkbone-florist-companion' ) ) . "\n\n";
echo printf( $email->message->comment_content ) . "\n\n";
_e('You can view the order and respond at this link: ', 'bakkbone-florist-companion') . $email->object->get_edit_order_url();

echo "\n\n----------------------------------------\n\n";

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo esc_html( wp_strip_all_tags( wptexturize( $additional_content ) ) );
	echo "\n\n----------------------------------------\n\n";
}

echo wp_kses_post( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
