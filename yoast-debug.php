<?php
/*
  Plugin Name: Yoast Debug
  Plugin URI: http://www.yoast.com/
  Description: Debugging the night away
  Version: 1.0
  Author: Barry Kooij
  Author URI: http://www.barrykooij.com/
	License: GPL v3

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( WP_DEBUG ) {

	/**
	 * Turn on error logging and show errors on-screen if in debugging mode
	 */
	@error_reporting( E_ALL );
	@ini_set( 'log_errors', true );
	@ini_set( 'log_errors_max_len', '0' );

	/**
	 * Change the path to one on your webserver, the directory does not have to be in the web root
	 * Don't forget to CHMOD this dir+file and add an .htaccess file denying access to all
	 * For an example .htaccess file, see https://gist.github.com/jrfnl/5953256
	 */
	//@ini_set( 'error_log', '/path/to/writable/file/logs/error.log' );


	// Ini sets
	@ini_set( 'display_errors', true ); // Show errors on screen
	@ini_set( 'html_errors', true );
	@ini_set( 'docref_root', 'http://php.net/manual/' );
	@ini_set( 'docref_ext', '.php' );
	@ini_set( 'error_prepend_string', '<span style="color: #ff0000; background-color: transparent;">' );
	@ini_set( 'error_append_string', '</span>' );

	if ( ! defined( 'SAVEQUERIES' ) ) {
		define( 'SAVEQUERIES', true );
	}

	if ( ! defined( 'WP_CACHE' ) ) {
		define( 'WP_CACHE', false );
	}

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', true );
	}

	if ( ! defined( 'WP_DEBUG_LOG' ) ) {
		define( 'WP_DEBUG_LOG', true );
	}

	if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
		define( 'WP_DEBUG_DISPLAY', true );
	}

	/**
	 * Adds a backtrace to PHP errors
	 *
	 * Copied from: https://gist.github.com/625769
	 * Forked from: http://stackoverflow.com/questions/1159216/how-can-i-get-php-to-produce-a-backtrace-upon-errors/1159235#1159235
	 * Adjusted by jrfnl
	 */
	function process_error_backtrace( $errno, $errstr, $errfile, $errline ) {
		if ( ! ( error_reporting() & $errno ) ) {
			return;
		}
		switch ( $errno ) {
			case E_WARNING      :
			case E_USER_WARNING :
			case E_STRICT      :
			case E_NOTICE      :
			case ( defined( 'E_DEPRECATED' ) ? E_DEPRECATED : 8192 )   :
			case E_USER_NOTICE  :
				$type  = 'warning';
				$fatal = false;
				break;
			default       :
				$type  = 'fatal error';
				$fatal = true;
				break;
		}
		$trace = debug_backtrace();
		array_shift( $trace );
		if ( php_sapi_name() == 'cli' && ini_get( 'display_errors' ) ) {
			echo 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
			foreach ( $trace as $item ) {
				echo '  ' . ( isset( $item['file'] ) ? $item['file'] : '<unknown file>' ) . ' ' . ( isset( $item['line'] ) ? $item['line'] : '<unknown line>' ) . ' calling ' . $item['function'] . '()' . "\n";
			}

			flush();
		} else if ( ini_get( 'display_errors' ) ) {
			echo '<p class="error_backtrace">' . "\n";
			echo '  Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ':' . "\n";
			echo '  <ol>' . "\n";
			foreach ( $trace as $item ) {
				echo '	<li>' . ( isset( $item['file'] ) ? $item['file'] : '<unknown file>' ) . ' ' . ( isset( $item['line'] ) ? $item['line'] : '<unknown line>' ) . ' calling ' . $item['function'] . '()</li>' . "\n";
			}
			echo '  </ol>' . "\n";
			echo '</p>' . "\n";

			flush();
		}
		if ( ini_get( 'log_errors' ) ) {
			$items = array();
			foreach ( $trace as $item ) {
				$items[] = ( isset( $item['file'] ) ? $item['file'] : '<unknown file>' ) . ' ' . ( isset( $item['line'] ) ? $item['line'] : '<unknown line>' ) . ' calling ' . $item['function'] . '()';
			}
			$message = 'Backtrace from ' . $type . ' \'' . $errstr . '\' at ' . $errfile . ' ' . $errline . ': ' . join( ' | ', $items );
			error_log( $message );
		}

		if ( $fatal ) {
			exit( 1 );
		}
	}

	set_error_handler( 'process_error_backtrace' );

	/**
	 * Now test whether it all works by uncommenting the below line
	 *
	 * If all is well:
	 * - With WP_DEBUG set to true: You should see a red error notice on your screen
	 * - Independently of the WP_DEBUG setting, the below 'error'-message should have been written to your log file. *Do* check whether it has been....
	 */
	//trigger_error( 'Testing 1..2..3.. Debugging code is working!', E_USER_NOTICE );

}