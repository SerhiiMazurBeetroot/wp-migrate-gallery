<?php

// nice dump function
if ( ! function_exists( 'wp_dump' ) ) {
	function wp_dump( ...$params ) {
		echo '<pre style="text-align: left; font-family: \'Courier New\'; font-size: 12px;line-height: 20px;background: #efefef;border: 1px solid #777;border-radius: 5px;color: #333;padding: 10px;margin:0;overflow: auto;overflow-y: hidden;">';
		var_dump( $params );
		echo '</pre>';
	}
}

// helper function to console.log php variables
function console_log( $output, $with_script_tags = true ) {
	 $js_code = 'console.log(' . json_encode( $output, JSON_HEX_TAG ) .
	');';
	if ( $with_script_tags ) {
		$js_code = '<script>' . $js_code . '</script>';
	}
	echo $js_code;
}

// die after dump function
if ( ! function_exists( 'wp_dd' ) ) {
	function wp_dd( ...$params ) {
		wp_dump( ...$params );
		die();
	}
}

// write custom debug.log file
if ( ! function_exists( 'wlog' ) ) {
	function wlog( $var, $desc = ' >> ', $clear_log = false ) {
		 $upload    = wp_upload_dir();
		$upload_dir = $upload['basedir'];
		$log_dir    = $upload_dir . '/debug';

		// resolve dir
		if ( ! is_dir( $log_dir ) ) {
			@mkdir( $log_dir, 0775, true );
		}

		// resolve htaccess
		if ( ! is_dir( $log_dir ) ) {
			return null;
		}

		$htaccess_path = trailingslashit( $log_dir ) . '.htaccess';

		if ( ! is_file( $htaccess_path ) ) {
			$content = <<<EOT
order deny,allow
deny from all
EOT;
			file_put_contents( $htaccess_path, $content, LOCK_EX );
		}

		// logging
		$log_file_destination = $log_dir . '/debug.log';

		if ( $clear_log || ! file_exists( $log_file_destination ) ) {
			file_put_contents( $log_file_destination, '' );
		}
		error_log( '[' . date( 'H:i:s' ) . ']' . '-------------------------' . PHP_EOL, 3, $log_file_destination );
		error_log( '[' . date( 'H:i:s' ) . ']' . $desc . ' : ' . print_r( $var, true ) . PHP_EOL, 3, $log_file_destination );
	}
}

// write to debug.log 
if (!function_exists('write_log')) {
	function write_log($log)
	{
		if (true === WP_DEBUG) {
			if (is_array($log) || is_object($log)) {
				error_log(print_r($log, true));
			} else {
				error_log($log);
			}
		}
	}
}

// functions for collecting code execution statistic
if ( ! function_exists( 'stGetTime' ) ) {
	function stGetTime( $time = false ) {
		$timeResult = ( $time === false ) ? microtime( true ) : ( microtime( true ) - $time );

		return $timeResult;
	}
}

if ( ! function_exists( 'stGetMemory' ) ) {
	function stGetMemory( $memory = false ) {
		$memoryResult = ( $memory === false ) ? memory_get_usage() : ( memory_get_usage() - $memory );

		return $memoryResult;
	}
}

if ( ! function_exists( 'stMemoryFormatted' ) ) {
	function stMemoryFormatted( $size ) {
		$unit = array( 'b', 'kb', 'mb', 'gb', 'tb', 'pb' );

		return @round( $size / pow( 1024, ( $i = floor( log( $size, 1024 ) ) ) ), 2 ) . ' ' . $unit[ $i ];
	}
}

if ( ! function_exists( 'stTimeFormatted' ) ) {
	function stTimeFormatted( $size ) {
		 return 'Duration: ' . number_format( $size, 3, '.', ' ' ) . ' sec';
	}
}

// usage
/*
$begin_memory = stGetMemory();
$begin_time   = stGetTime();

some_func_to_test();

$time_diff   = stTimeFormatted( stGetTime( $begin_time ) );
$memory_diff = stMemoryFormatted( stGetMemory( $begin_memory ) );
*/
