<?php
// TODO: ? create DownloadWrapper<->FileWrapper, JsonWrapper, XmlWrapper, HtmlWrapper
// TODO: ? setView/getView + setFile/getFile @ Response 
// TODO: refactor Session class (to include options @ documentation
// TODO: make Request class support NGINX

require_once("src/FrontController.php");
require_once("exceptions/ServletException.php");
require_once("exceptions/ServletApplicationException.php");
require_once("exceptions/FileUploadException.php");