<?php
function getSourceFileName($strTestFileName) {
	$intPosition = strrpos($strTestFileName,"/tests/");
	$strCommonFolder = substr($strTestFileName, 0, $intPosition);
	$strTestPath = str_replace(array("/tests/","Test.php"),array("/src/",".php"), substr($strTestFileName, $intPosition));
	return $strCommonFolder.$strTestPath;
}