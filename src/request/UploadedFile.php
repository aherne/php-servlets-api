<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Bean holding information about an uploaded file
 */
class UploadedFile
{
    private $name;
    private $location;
    private $contentType;
    private $size;
    
    /**
     * Detects info based on values in $_SERVER superglobal
     *
     * @param array $values
     */
    public function __construct($values)
    {
        if ($values['error']!=0) {
            throw new FileUploadException($this->getErrorMessage($values['error'], $values['name']));
        }
        
        $this->setName($values['name']);
        $this->setContentType($values['type']);
        $this->setLocation($values['tmp_name']);
        $this->setSize($values['size']);
    }
    
    /**
     * Checks if file uploaded with error
     *
     * @param integer $errorCode
     * @param string $fileName
     * @return string
     */
    private function getErrorMessage($errorCode, $fileName)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return "The uploaded file exceeds the upload_max_filesize directive in php.ini: ".$fileName;
                break;
            case UPLOAD_ERR_FORM_SIZE:
                return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form: ".$fileName;
                break;
            case UPLOAD_ERR_PARTIAL:
                return "The uploaded file was only partially uploaded: ".$fileName;
                break;
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded!";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing a temporary folder!";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk: ".$fileName;
                break;
            case UPLOAD_ERR_EXTENSION:
                return "A PHP extension stopped the file upload: ".$fileName;
                break;
        }
    }
    
    /**
     * Sets uploaded file name.
     *
     * @param string $name
     */
    private function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Gets uploaded file name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Sets location for the file uploaded.
     *
     * @param string $location
     */
    private function setLocation($location)
    {
        $this->location = $location;
    }
        
    /**
     * Gets location for the file uploaded
     *
     * @param string $location
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
    
    /**
     * Sets file mime type.
     *
     * @param string $contentType
     * @return void
     */
    private function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
        
    /**
     * Gets file mime type.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Sets file size.
     *
     * @param integer $size
     * @return void
     */
    private function setSize($size)
    {
        $this->size = $size;
    }
        
    /**
     * Gets file size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * Moves uploaded file to destination.
     *
     * @param string $destination
     * @return boolean
     */
    public function move($destination)
    {
        return move_uploaded_file($this->location, $destination);
    }
    
    /**
     * Deletes uploaded file.
     *
     * @return boolean
     */
    public function delete()
    {
        return unlink($this->location);
    }
}
