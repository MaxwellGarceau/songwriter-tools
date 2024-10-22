<?php

declare( strict_types = 1 );

namespace Max_Garceau\Songwriter_Tools\Utilities;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\ErrorHandler;

class Logger_Init {

	protected Logger $logger;
	protected string $log_channel;
	protected \Monolog\Level $log_level;
	protected string $log_file;

	public function __construct() {
		$this->log_channel = 'songwriter_tools';

		// Log to the same file as WP debug.log
		$this->log_file = $this->is_debug_enabled() && is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';

		// Keep it simple for now
		$this->log_level = $this->get_default_log_level();

		$this->init_logger();
	}

	private function is_debug_enabled(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
	}

	protected function init_logger(): void {
		$this->logger = new Logger( $this->log_channel );

		if ( $this->is_debug_enabled() ) {

			// Create log handler with default file or WP_DEBUG_LOG path
			$handler = new StreamHandler( $this->log_file, $this->log_level );

			// Optional: Customize log formatting (you can adjust this to your needs)
			$formatter = new LineFormatter( null, null, true, true );
			$handler->setFormatter( $formatter );

			$this->logger->pushHandler( $handler );
		} else {
			// Do not log on production environment or if WP_DEBUG is off
			$this->logger->pushHandler( new NullHandler() );
		}

		// Register Monolog error handler to catch PHP errors, warnings, etc.
		ErrorHandler::register( $this->logger );
	}

	protected function get_default_log_level(): \Monolog\Level {
		// Default log level to error and above in production
		return ( defined( 'WP_ENV' ) && getenv( 'WP_ENV' ) === 'production' ) ? \Monolog\Level::Error : \Monolog\Level::Debug;
	}

	public function get_logger(): Logger {
		return $this->logger;
	}

	public static function init( $log_channel = null, $log_level = null, $log_file = null ): self {
		return new self( $log_channel, $log_level, $log_file );
	}
}
