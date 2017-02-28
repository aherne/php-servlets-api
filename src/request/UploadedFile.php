<?php
/**
 * Bean holding information about an uploaded file
 */
final class UploadedFile {
	private $strName;
	private $strLocation;
	private $strContentType;
	private $intSize;
	
	public function __construct($tblValues) {
		if($tblValues['error']!=0) {
			switch($tblValues['error']) {
				case UPLOAD_ERR_INI_SIZE:
					throw new FileUploadException("The uploaded file exceeds the upload_max_filesize directive in php.ini: ".$tblValues['name']);
					break;
				case UPLOAD_ERR_FORM_SIZE:
					throw new FileUploadException("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form: ".$tblValues['name']);
					break;
				case UPLOAD_ERR_PARTIAL:
					throw new FileUploadException("The uploaded file was only partially uploaded: ".$tblValues['name']);
					break;
				case UPLOAD_ERR_NO_FILE:
					// it is allowed to opload no file 
					return;
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					throw new FileUploadException("Missing a temporary folder!");
					break;
				case UPLOAD_ERR_CANT_WRITE:
					throw new FileUploadException("Failed to write file to disk: ".$tblValues['name']);
					break;
				case UPLOAD_ERR_EXTENSION:
					throw new FileUploadException("A PHP extension stopped the file upload: ".$tblValues['name']);
					break;
			}
		}
		
		$this->setName($tblValues['name']);
		$this->setContentType($tblValues['type']);
		$this->setLocation($tblValues['tmp_name']);
		$this->setSize($tblValues['size']);
	}
	
	/**
	 * Sets uploaded file name.
	 * 
	 * @param string $strName
	 * @return void
	 */
	private function setName($strName) {
		$this->strName = $strName;
	}
	
	/**
	 * Gets uploaded file name.
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->strName;
	}
	
	/**
	 * Sets location for the file uploaded.
	 * 
	 * @param string $strLocation
	 * @return void
	 */
	private function setLocation($strLocation) {
		$this->strLocation = $strLocation;
	}
		
	/**
	 * Gets location for the file uploaded
	 * 
	 * @param string $strLocation
	 * @return void
	 */
	public function getLocation() {
		return $this->strLocation;
	}
	
	/**
	 * Sets file mime type.
	 * 
	 * @param string $strContentType
	 * @return void
	 */
	private function setContentType($strContentType) {
		$this->strContentType = $strContentType;
	}
		
	/**
	 * Gets file mime type.
	 * 
	 * @return string
	 */
	public function getContentType() {
		return $this->strContentType;
	}

	/**
	 * Sets file size.
	 * 
	 * @param integer $intSize
	 * @return void
	 */
	private function setSize($intSize) {
		$this->intSize = $intSize;
	}	
		
	/**
	 * Gets file size.
	 * 
	 * @return int
	 */
	public function getSize() {
		return $this->intSize;
	}
	
	/**
	 * Moves uploaded file to destination.
	 * 
	 * @param string $strDestination
	 * @return boolean
	 */
	public function move($strDestination) {
		return move_uploaded_file($this->strLocation, $strDestination);
	}
	
	/**
	 * Deletes uploaded file.
	 * 
	 * @return boolean
	 */
	public function delete() {
		return unlink($this->strLocation);
	}
}