<?php
namespace Lucinda\STDOUT\Request\UploadedFiles;

/**
 * Encapsulates information from $_FILES superglobal into a tree.
 */
class Parser
{
    private $contents;
    
    /**
     * Parses through $_FILES superglobal and compiles a tree.
     */
    public function __construct()
    {
        $this->setResult();
    }
    
    /**
     * Constructs tree
     */
    private function setResult(): void
    {
        $result = array();
        
        $normalize = function ($key, $value) use ($result) {
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
        };
        
        foreach ($_FILES as $key => $value) {
            if (isset($value['name'])) {
                if (is_string($value['name'])) {
                    $result[$key] = $value;
                    continue;
                }
                if (is_array($value['name'])) {
                    $result += $normalize($key, $value);
                }
            }
        }
        
        $this->contents = $result;
    }
    
    /**
     * Gets tree.
     *
     * @return array
     */
    public function getResult(): array
    {
        return $this->contents;
    }
}
