<?php
/**
 * Petals Message email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/petals-message.php.
 *
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php ?>
<p><?php printf( __( 'You\'ve received a message from Petals regarding an order you sent - please see below.', 'bakkbone-florist-companion' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<p><?php printf( $email->message->comment_content ); ?></p>
<?php

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );