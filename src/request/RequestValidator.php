<?php
namespace Lucinda\MVC\STDOUT;

/**
 * Blueprint for class able to process request originally issued by client and extract requested page, path parameters and content type
 */
interface RequestValidator {
    /**
     * Gets request format
     *
     * @example html
     * @return string
     */
    public function getFormat();

    /**
     * Gets requested resource/page
     *
     * @example /asd/def
     * @return string
     */
    public function getPage();


    /**
     * Gets path parameters detected by optional name
     *
     * @param string $name
     * @return string[string]|NULL|string
     */
    public function parameters($name="");
}