<?php
namespace Directorist;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use SplFileObject;

class Listings_Importer {

	protected $file = null;

	protected $file_object = null;

	public function __construct( $file_id ) {
		$this->set_file( $file_id );
	}

	public function set_file( $file_id ) {
		if ( ! $file_id || ! get_attached_file( $file_id ) ) {
			return throw new Exception('Invalid attachment file id.' );
		}

		$file_path = get_attached_file( $file_id );
		$mime_type = mime_content_type( $file_path );
		if ( ! in_array( $mime_type, array( 'text/csv','text/plain' ), true ) ) {
			throw new Exception(
				sprintf(
					'Invalid mime type. Only text/csv and text/plain are supported, given "%s".',
					$mime_type
				)
			);
		}

		$this->file = $file_path;
	}

	public function get_file_object() {
		if ( ! $this->file_exists() ) {
			return null;
		}

		if ( ! $this->file_object ) {
			$this->file_object = new SplFileObject( $this->file );
			$this->file_object->setCsvControl( ',', '"', '\\' );
		}

		return $this->file_object;
	}

	public function get_total_items() {
		if ( ! $this->file_exists() ) {
			return 0;
		}

		// Total items excluding header.
		$this->get_file_object()?->seek(PHP_INT_MAX); // Move to last line
		return $this->get_file_object()?->key();
	}

	public function get_header() {
		if (! $this->file_exists() ) {
			return [];
		}

		$this->get_file_object()?->rewind();
		$header_row = $this->get_file_object()?->fgetcsv(',', '"', '\\');

		$this->get_file_object()?->next();
		$first_row = $this->get_file_object()?->fgetcsv(',', '"', '\\');

		return array_combine( $header_row, $first_row );
	}

	public function file_exists() {
		if ( is_null( $this->file ) ) {
			return false;
		}

		return is_readable( $this->file );
	}
}
