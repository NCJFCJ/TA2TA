<?php
/**
 * Handles the encryption functionality of the Events Virtual plugin.
 *
 * @since   1.4.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Encoding;

/**
 * Class Encryption
 *
 * @since   1.4.0
 *
 * @package Tribe\Events\Virtual
 */
class Encryption {

	/**
	 * The key name where the encryption key will be stored.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	protected $encrypt_key = 'tribe_events_virtual_zoom_encryption_key';

	/**
	 * Get the Encryption Key.
	 *
	 * @since 1.4.0
	 *
	 * @return string \Defuse\Crypto\Key A string to use as the encryption key of decimal digits[0-9] and characters from [a-f].
	 * @throws \Defuse\Crypto\Exception\BadFormatException
	 * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
	 */
	protected function get_encryption_key() {

		// get generated key
		$key = get_option( $this->encrypt_key, '' );

		// if the key is still empty generate backup key.
		if ( empty( $key ) || ! is_string( $key ) ) {
			$key = $this->generate_encryption_key();
		}

		/**
		 * Allow filtering of the encryption key.
		 *
		 * @since 1.4.0
		 *
		 * @param string $key The encryption key.
		 */
		$key = apply_filters( 'tribe_events_virtual_encryption_key', $key );

		if ( ! \ctype_xdigit( $key ) ) {

			do_action( 'tribe_log', 'error', __CLASS__, [
				'action'  => __METHOD__,
				'code'    => 'Key Error',
				'message' => 'Provided key is not a hexadecimal, only decimal digits[0-9] and characters from [a-f]',
			] );

			return false;
		}

		return Key::loadFromAsciiSafeString( $key );
	}

	/**
	 * Generate an encryption key.
	 *
	 * @since 1.4.0
	 *
	 * @return string The generated encryption key.
	 * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
	 */
	protected function generate_encryption_key() {
		$key_obj = Key::createNewRandomKey();
		$generated_key = $key_obj->saveToAsciiSafeString();
		add_option( $this->encrypt_key, $generated_key );

		return $generated_key;
	}

	/**
	 * Encrypt the provided string.
	 *
	 * @since 1.4.0
	 *
	 * @param string|array<string|string> $data     The string or array to encrypt.
	 * @param boolean                     $is_array If the data is expected to be an array an should be json encoded, default is false.
	 *
	 * @return string|array<string|string> The string to encrypt or if no key the data passed to the method.
	 * @throws \Defuse\Crypto\Exception\BadFormatException
	 * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
	 */
	public function encrypt( $data, $is_array = false ) {
		/** var Defuse\Crypto\Key */
		$key = $this->get_encryption_key();
		if ( empty( $key ) ) {
			return $data;
		}

		if ( $is_array ) {
			$data = wp_json_encode( $data );
		}

		return Crypto::encrypt( $data, $key );
	}

	/**
	 * Decrypt the provided string.
	 *
	 * @since 1.4.0
	 *
	 * @param string  $encrypted_data A string of encrypted text or json encoded data.
	 * @param boolean $is_array       If the data is expected to be an array once decrypted, default is false.
	 *
	 * @return string|array<string|string>  The decrypted string|array or when a failure to decrypt, the passed string.
	 * @throws \Defuse\Crypto\Exception\BadFormatException
	 * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
	 */
	public function decrypt( $encrypted_data, $is_array = false ) {
		if ( empty( $encrypted_data ) || ! is_string( $encrypted_data ) ) {
			return $encrypted_data;
		}

		$key = $this->get_encryption_key();
		if ( empty( $key ) ) {
			return $encrypted_data;
		}

		try {
			$decrypted_text = Crypto::decrypt( $encrypted_data, $key );
		} catch ( \Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $error ) {
			// Return data as sent to the method if there is a failure to decrypt.
			// This is for backwards compatibility for data that was no encrypted.
			return $encrypted_data;
		}

		if ( $is_array ) {
			$decrypted_text = json_decode( $decrypted_text, true );
		}

		return $decrypted_text;
	}
}
