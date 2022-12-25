<?php

namespace Lucinda\STDOUT\Request\UploadedFiles;

/**
 * Bean holding information about an uploaded file
 */
class File
{
    private string $name;
    private string $location;
    private string $contentType;
    private int $size;

    /**
     * Detects info based on values in $_SERVER superglobal
     *
     * @param array<string,mixed> $values
     */
    public function __construct(array $values)
    {
        if ($values['error']!=0) {
            throw new Exception($this->getErrorMessage($values['error'], $values['name']));
        }

        $this->setName($values['name']);
        $this->setContentType($values['type']);
        $this->setLocation($values['tmp_name']);
        $this->setSize($values['size']);
    }

    /**
     * Checks if file uploaded with error
     *
     * @param  integer $errorCode
     * @param  string  $fileName
     * @return string
     */
    private function getErrorMessage(int $errorCode, string $fileName): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini: " . $fileName,
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form: " . $fileName,
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded: " . $fileName,
            UPLOAD_ERR_NO_FILE => "No file was uploaded!",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder!",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk: " . $fileName,
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload: " . $fileName,
            default => "Unknown error!",
        };
    }

    /**
     * Sets uploaded file name.
     *
     * @param string $name
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets uploaded file name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets location for the file uploaded.
     *
     * @param string $location
     */
    private function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * Gets location for the file uploaded
     *
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Sets file mime type.
     *
     * @param string $contentType
     */
    private function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Gets file mime type.
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Sets file size.
     *
     * @param integer $size
     */
    private function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Gets file size.
     *
     * @return integer
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Moves uploaded file to destination.
     *
     * @param  string $destination
     * @return boolean
     */
    public function move(string $destination): bool
    {
        return move_uploaded_file($this->location, $destination);
    }

    /**
     * Deletes uploaded file.
     *
     * @return boolean
     */
    public function delete(): bool
    {
        return unlink($this->location);
    }
}
