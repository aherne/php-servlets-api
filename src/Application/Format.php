<?php
namespace Lucinda\STDOUT\Application;

use Lucinda\STDOUT\ConfigurationException;

/**
 * Encapsulates file format information:
 * - format: file format / name
 * - content type: content type that corresponds to above file format
 * - character encoding: charset associated to content type
 * - view resolver: (optional) view resolver class name. If not set, framework will resolve into an empty view with headers only.
 */
class Format
{
    private $name;
    private $contentType;
    private $viewResolverClass;
    private $characterEncoding;

    /**
     * Saves response format data detected from XML tag "format".
     *
     * @param \SimpleXMLElement $info
     */
    public function __construct(\SimpleXMLElement $info)
    {
        $this->name = (string) $info["name"];
        
        $this->contentType = (string) $info["content_type"];
        if (!$this->contentType) {
            throw new ConfigurationException("Attribute 'content_type' is mandatory for 'format' tag");
        }
        
        $this->characterEncoding = (string) $info["charset"];
        
        $this->viewResolverClass = (string) $info['class'];
        if (!$this->viewResolverClass) {
            throw new ConfigurationException("Attribute 'class' is mandatory for 'format' tag");
        }
    }

    /**
     * Gets response format name (extension).
     *
     * @return string
     * @example json
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Gets content type
     *
     * @return string
     * @example application/json
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
    
    /**
     * Gets character encoding (charset)
     *
     * @return string
     */
    public function getCharacterEncoding(): string
    {
        return $this->characterEncoding;
    }

    /**
     * Gets view resolver class name
     *
     * @return string
     * @example JsonResolver
     */
    public function getViewResolver(): string
    {
        return $this->viewResolverClass;
    }
}
