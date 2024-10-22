<?php

declare( strict_types = 1 );

namespace Max_Garceau\Songwriter_Tools\Includes;

use DI\Container;
use DI\ContainerBuilder;
use Max_Garceau\Songwriter_Tools\Utilities\Logger_Init;
use Monolog\Logger;

/**
 * Configure PHP DI
 *
 * TODO: Not sure this file should live here, but it's a start
 *
 * Use the singleton pattern to create a DI container to avoid
 * instantiating multiple containers.
 */
class DI_Container {

	private static ?Container $container = null;

	// Initialize and return the container
	public static function get_container(): Container {
		if ( self::$container === null ) {
			self::$container = self::build_container();
		}
		return self::$container;
	}

	public static function build_container(): Container {
		$containerBuilder = new ContainerBuilder();

		// Add custom logger definition
		$containerBuilder->addDefinitions(
			array(
				Logger::class => function () {
					// Initialize Logger_Init and return the logger instance
					$logger_init = Logger_Init::init();
					return $logger_init->get_logger();
				},
			)
		);

		// Build and return the container
		return $containerBuilder->build();
	}
}
