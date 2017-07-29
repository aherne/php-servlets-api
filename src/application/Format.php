<?php
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
	private $strExtension, $strContentType, $strWrapperClass, $strCharacterEncoding;

	/**
	 * @param string $strExtension
	 * @param string $strContentType
	 * @param string $strCharacterEncoding
	 * @param string $strWrapperClass
	 */
	public function __construct($strExtension, $strContentType, $strCharacterEncoding="", $strWrapperClass="") {
		$this->strExtension = $strExtension;
		$this->strContentType = $strContentType;
		$this->strCharacterEncoding= $strCharacterEncoding;
		$this->strWrapperClass = $strWrapperClass;
	}

	/**
	 * Gets file format.
	 * 
	 * @return string
	 * @example json
	 */
	public function getExtension() {
		return $this->strExtension;
	}

	/**
	 * Gets content type
	 * 
	 * @return string
	 * @example application/json
	 */
	public function getContentType() {
		return $this->strContentType;
	}
	
	/**
	 * Gets character encoding (charset)
	 *
	 * @return string
	 */
	public function getCharacterEncoding() {
		return $this->strCharacterEncoding;
	}

	/**
	 * Gets view resolver class name based on file format.
	 * 
	 * @return string
	 * @example JsonWrapper
	 */
	public function getWrapper() {
		return $this->strWrapperClass;
	}
}