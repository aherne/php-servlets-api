<?php
namespace Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\UploadedFiles\File;
use Lucinda\STDOUT\Request\UploadedFiles\Parser;

/**
 * Encapsulates information of files sent via multipart forms into UploadedFile objects.
 */
class UploadedFiles
{
    protected array $attributes =  array();

    /**
     * Converts information in $_FILES super-global into a tree of UploadedFile objects
     * @throws UploadedFiles\Exception
     */
    public function __construct()
    {
        if (sizeof($_FILES)==0) {
            return;
        }
        $requestParser = new Parser();
        $this->attributes = $this->makeObjects($requestParser->getResult());
    }

    /**
     * Performs recursive conversion between array of properties and UploadedFile
     *
     * @param array $array
     * @return array|File
     * @throws UploadedFiles\Exception
     */
    private function makeObjects(array $array): array|File
    {
        $ret = array();
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $ret[$k] = $this->makeObjects($v);
            } else {
                if ($array["error"]!=UPLOAD_ERR_NO_FILE) {
                    return new File($array);
                } else {
                    return []; // having no file uploaded is a non-error situation
                }
            }
        }
        return $ret;
    }
    
    /**
     * Gets attributes as array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
