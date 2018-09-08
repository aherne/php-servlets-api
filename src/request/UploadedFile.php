<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Bean holding information about an uploaded file
 */
final class UploadedFile {
	private $name;
	private $location;
	private $contentType;
	private $size;
	
	public function __construct($values) {
		if($values['error']!=0) {
			switch($values['error']) {
				case UPLOAD_ERR_INI_SIZE:
					throw new FileUploadException("The uploaded file exceeds the upload_max_filesize directive in php.ini: ".$values['name']);
					break;
				case UPLOAD_ERR_FORM_SIZE:
					throw new FileUploadException("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form: ".$values['name']);
					break;
				case UPLOAD_ERR_PARTIAL:
					throw new FileUploadException("The uploaded file was only partially uploaded: ".$values['name']);
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					throw new FileUploadException("Missing a temporary folder!");
					break;
				case UPLOAD_ERR_CANT_WRITE:
					throw new FileUploadException("Failed to write file to disk: ".$values['name']);
					break;
				case UPLOAD_ERR_EXTENSION:
					throw new FileUploadException("A PHP extension stopped the file upload: ".$values['name']);
					break;
			}
		}
		
		$this->setName($values['name']);
		$this->setContentType($values['type']);
		$this->setLocation($values['tmp_name']);
		$this->setSize($values['size']);
	}
	
	/**
	 * Sets uploaded file name.
	 * 
	 * @param string $name
	 * @return void
	 */
	private function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * Gets uploaded file name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets location for the file uploaded.
	 * 
	 * @param string $location
	 * @return void
	 */
	private function setLocation($location) {
		$this->location = $location;
	}
		
	/**
	 * Gets location for the file uploaded
	 * 
	 * @param string $location
	 * @return void
	 */
	public function getLocation() {
		return $this->location;
	}
	
	/**
	 * Sets file mime type.
	 * 
	 * @param string $contentType
	 * @return void
	 */
	private function setContentType($contentType) {
		$this->contentType = $contentType;
	}
		
	/**
	 * Gets file mime type.
	 * 
	 * @return string
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * Sets file size.
	 * 
	 * @param integer $size
	 * @return void
	 */
	private function setSize($size) {
		$this->size = $size;
	}	
		
	/**
	 * Gets file size.
	 * 
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}
	
	/**
	 * Moves uploaded file to destination.
	 * 
	 * @param string $destination
	 * @return boolean
	 */
	public function move($destination) {
		return move_uploaded_file($this->location, $destination);
	}
	
	/**
	 * Deletes uploaded file.
	 * 
	 * @return boolean
	 */
	public function delete() {
		return unlink($this->location);
	}
}