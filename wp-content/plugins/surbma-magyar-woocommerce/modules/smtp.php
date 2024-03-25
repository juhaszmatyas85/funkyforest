<?php

// Configures WordPress PHPMailer
add_action( 'phpmailer_init', function( $phpmailer ) {
	$options = get_option( 'surbma_hc_fields' );

	// SMTP port number - likely to be 25, 465 or 587
	$smtp_port = isset( $options['smtpport'] ) ? $options['smtpport'] : 587;
	// Encryption system to use - ssl or tls
	$smtp_secure = isset( $options['smtpsecure'] ) ? $options['smtpsecure'] : 0;
	// SMTP From email address
	$smtp_from = isset( $options['smtpfrom'] ) ? $options['smtpfrom'] : 0;
	// SMTP From name
	$smtp_fromname = isset( $options['smtpfromname'] ) ? $options['smtpfromname'] : 0;
	// The hostname of the mail server
	$smtp_host = isset( $options['smtphost'] ) ? $options['smtphost'] : 0;
	// Username to use for SMTP authentication
	$smtp_user = isset( $options['smtpuser'] ) ? $options['smtpuser'] : 0;
	// Password to use for SMTP authentication
	$smtp_password = isset( $options['smtppassword'] ) ? $options['smtppassword'] : 0;

	if ( $smtp_host && $smtp_user && $smtp_password ) {
		$phpmailer->isSMTP();
		$phpmailer->Host = $smtp_host;
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = $smtp_user;
		$phpmailer->Password = $smtp_password;

		// Additional settings
		$phpmailer->Port = $smtp_port;
		if ( $smtp_secure ) {
			$phpmailer->SMTPSecure = $smtp_secure;
		}
		if ( $smtp_from ) {
			$phpmailer->From = $smtp_from;
		}
		if ( $smtp_fromname ) {
			$phpmailer->FromName = $smtp_fromname;
		}
	}
} );
