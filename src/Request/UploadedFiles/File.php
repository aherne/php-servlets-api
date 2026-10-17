<?php

namespace Lucinda\STDOUT\Request\UploadedFiles;

/**
 * Represents one successfully received multipart upload
 *
 * The object retains PHP's client-supplied file name and content type together
 * with the temporary server location and byte size. Moving or deleting the
 * temporary file is explicit and updates no stored metadata.
 */
final class File
{
    /**
     * Client-supplied file name
     */
    private string $name;
    /**
     * Temporary server-side upload path
     */
    private string $location;
    /**
     * Client-supplied media type
     */
    private string $contentType;
    /**
     * Uploaded byte count reported by PHP
     */
    private int $size;

    /**
     * Validates and captures one PHP upload entry
     *
     * @param array<string,mixed> $values Upload attributes from $_FILES
     * @throws Exception If the upload error code is not UPLOAD_ERR_OK
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
     * Translates a PHP upload error code into a caller-facing message
     *
     * @param int $errorCode UPLOAD_ERR_* value reported by PHP
     * @param string $fileName Client-supplied file name
     * @return string Human-readable upload failure
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
     * Stores the client-supplied file name
     *
     * @param string $name File name reported by PHP
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Gets the client-supplied file name
     *
     * @return string File name reported by PHP
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Stores the temporary upload path
     *
     * @param string $location Temporary server-side file path
     */
    private function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * Gets the temporary upload path
     *
     * @return string Server-side path reported by PHP
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * Stores the client-supplied media type
     *
     * @param string $contentType Media type reported by PHP
     */
    private function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Gets the client-supplied media type
     *
     * @return string Media type reported by PHP
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Stores the reported upload size
     *
     * @param int $size Uploaded byte count
     */
    private function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * Gets the reported upload size
     *
     * @return int Uploaded byte count
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Moves the PHP upload to a permanent destination
     *
     * @param string $destination Destination filesystem path
     * @return bool True when PHP moved the uploaded file successfully
     */
    public function move(string $destination): bool
    {
        return move_uploaded_file($this->location, $destination);
    }

    /**
     * Deletes the file at its current temporary location
     *
     * @return bool True when the file was removed successfully
     */
    public function delete(): bool
    {
        return unlink($this->location);
    }
}
