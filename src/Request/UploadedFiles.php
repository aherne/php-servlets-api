<?php

namespace Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\UploadedFiles\File;
use Lucinda\STDOUT\Request\UploadedFiles\Parser;

/**
 * Encapsulates information of files sent via multipart forms into UploadedFile objects.
 */
class UploadedFiles
{
    /**
     * @var array<mixed>
     */
    protected array $attributes =  array();

    /**
     * Converts information in $_FILES super-global into a tree of UploadedFile objects
     *
     * @param array<string,mixed> $files
     * @throws UploadedFiles\Exception
     */
    public function __construct(array $files)
    {
        if (sizeof($files)==0) {
            return;
        }
        $rawArray = $this->makeArray($files);
        $this->attributes = $this->makeObjects($rawArray);
    }

    /**
     * Reads file array at surface level
     *
     * @param array<string,mixed> $files
     * @return array<string,mixed>
     */
    private function makeArray(array $files): array
    {
        $result = [];
        foreach ($files as $key => $value) {
            if (isset($value['name'])) {
                if (is_string($value['name'])) {
                    $result[$key] = $value;
                    continue;
                }
                if (is_array($value['name'])) {
                    $result += $this->normalize($key, $value);
                }
            }
        }
        return $result;
    }

    /**
     * Reads file array at lower level
     *
     * @param mixed $key
     * @param array<mixed> $value
     * @return array<mixed>
     */
    private function normalize(mixed $key, array $value): array
    {
        $result = [];
        foreach ($value as $param => $content) {
            foreach ($content as $num => $val) {
                if (is_numeric($num)) {
                    $result[$key][$num][$param] = $val;
                    continue;
                }
                if (is_array($val)) {
                    foreach ($val as $next => $one) {
                        $result[$key][$num][$next][$param] = $one;
                    }
                    continue;
                }
                $result[$key][$num][$param] = $val;
            }
        }
        return $result;
    }

    /**
     * Performs recursive conversion between array of properties and UploadedFile
     *
     * @param array<string, mixed> $array
     * @return array<string, mixed>|File
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
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
