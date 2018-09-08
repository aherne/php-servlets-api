<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Encapsulates file format information:
 * - format: file format / extension
 * - content type: content type that corresponds to above file format
 * - wrapper: (optional) wrapper class name. If not set, framework-defined ViewWrapper will be used.
 * Utility @ Application class.
 * 
 * @author aherne
 */
class Format {
	private $extension, $contentType, $wrapperClass, $characterEncoding;

	/**
	 * @param string $extension
	 * @param string $contentType
	 * @param string $characterEncoding
	 * @param string $wrapperClass
	 */
	public function __construct($extension, $contentType, $characterEncoding="", $wrapperClass="") {
		$this->extension = $extension;
		$this->contentType = $contentType;
		$this->characterEncoding= $characterEncoding;
		$this->wrapperClass = $wrapperClass;
	}

	/**
	 * Gets file format.
	 * 
	 * @return string
	 * @example json
	 */
	public function getExtension() {
		return $this->extension;
	}

	/**
	 * Gets content type
	 * 
	 * @return string
	 * @example application/json
	 */
	public function getContentType() {
		return $this->contentType;
	}
	
	/**
	 * Gets character encoding (charset)
	 *
	 * @return string
	 */
	public function getCharacterEncoding() {
		return $this->characterEncoding;
	}

	/**
	 * Gets view resolver class name based on file format.
	 * 
	 * @return string
	 * @example JsonWrapper
	 */
	public function getWrapper() {
		return $this->wrapperClass;
	}
}