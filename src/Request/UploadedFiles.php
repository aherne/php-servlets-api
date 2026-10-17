<?php

namespace Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\UploadedFiles\File;
use Lucinda\STDOUT\Request\UploadedFiles\Parser;

/**
 * Normalizes PHP multipart upload data into a tree of File objects
 *
 * PHP stores each upload attribute in a separate parallel array for nested form
 * fields. This converter restores the original field hierarchy, combines each
 * leaf's attributes, and replaces uploaded leaves with immutable File values.
 * UPLOAD_ERR_NO_FILE is represented by an empty array rather than an exception.
 */
final class UploadedFiles
{
    /**
     * @var array<string,mixed> Normalized upload tree preserving form field structure
     */
    protected array $attributes =  array();

    /**
     * Converts a $_FILES snapshot into typed upload values
     *
     * @param array<string,mixed> $files Raw PHP upload structure
     * @throws Exception If PHP reports an upload error for any file
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
     * Converts top-level upload fields into a consistent nested representation
     *
     * @param array<string,mixed> $files Raw PHP upload structure
     * @return array<string,mixed> Upload attributes grouped by form field path
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
     * Reassembles parallel nested upload attributes for one form field
     *
     * @param mixed $key Top-level form field key
     * @param array<mixed> $value Parallel upload attribute arrays
     * @return array<mixed> Attributes grouped into individual uploaded files
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
     * Recursively replaces upload attribute leaves with File objects
     *
     * @param array<string,mixed> $array Normalized upload subtree or attribute leaf
     * @return array<string,mixed>|File Converted subtree or uploaded file
     * @throws Exception If PHP reports an upload error
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
     * Gets the normalized upload tree
     *
     * @return array<string,mixed> File objects and nested arrays keyed by form field
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
