<?php
namespace Lucinda\STDOUT\Locators;

use Lucinda\STDOUT\ConfigurationException;

/**
 * Locates and loads class on disk based on folder and name
 */
class ClassFinder
{
    private $folder;
    
    /**
     * Sets folder in which class should be searched for
     *
     * @param string $folder
     */
    public function __construct($folder)
    {
        $this->folder = $folder;
    }
    
    /**
     * Locates and loads class on disk based on source name which may include subfolder and namespace
     *
     * @param string $className
     * @throws ConfigurationException
     * @return string
     */
    public function find($className): string
    {
        $classPath = "";
        
        // get classes loaded in subfolders
        $slashPosition = strrpos($className, "/");
        if ($slashPosition!==false) {
            $classPath = substr($className, 0, $slashPosition);
            $className = substr($className, $slashPosition+1);
        }
        
        // get actual class name without namespace
        $backslashPosition = strrpos($className, "\\");
        if ($backslashPosition!==false) {
            $simpleClassName = substr($className, $backslashPosition+1);
        } else {
            $simpleClassName = $className;
        }
        
        // loads class file
        $filePath = ($this->folder?$this->folder."/":"").($classPath?$classPath."/":"").$simpleClassName.".php";
        if (!file_exists($filePath)) {
            throw new ConfigurationException("File not found: ".$simpleClassName);
        }
        require_once($filePath);
        
        // validates if class exists
        if (!class_exists($className)) {
            throw new ConfigurationException("Class not found: ".$className);
        }
        
        return $className;
    }
}
