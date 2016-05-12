<?php
require_once("/var/www/html/Libraries/tests/UnitTest.php");
require_once("/var/www/html/Libraries/libraries/suites/MVC/ServletsAPI/src/Request/RequestUploadedFiles.php");

/**


Array
(
    [a] => Array
        (
            [name] => Array
                (
                    [0] => Array
                        (
                            [1] => 12644838_1728862143999505_5178290272638334520_n.jpg
                            [2] => linkedin.jpg
                        )

                )

            [type] => Array
                (
                    [0] => Array
                        (
                            [1] => image/jpeg
                            [2] => image/jpeg
                        )

                )

            [tmp_name] => Array
                (
                    [0] => Array
                        (
                            [1] => /tmp/phpo0eCgC
                            [2] => /tmp/phpWqeWlp
                        )

                )

            [error] => Array
                (
                    [0] => Array
                        (
                            [1] => 0
                            [2] => 0
                        )

                )

            [size] => Array
                (
                    [0] => Array
                        (
                            [1] => 61028
                            [2] => 16197
                        )

                )

        )

)
 */

// 3
$_FILES = unserialize('a:1:{s:1:"a";a:5:{s:4:"name";a:1:{i:0;a:2:{i:1;s:51:"12644838_1728862143999505_5178290272638334520_n.jpg";i:2;s:12:"linkedin.jpg";}}s:4:"type";a:1:{i:0;a:2:{i:1;s:10:"image/jpeg";i:2;s:10:"image/jpeg";}}s:8:"tmp_name";a:1:{i:0;a:2:{i:1;s:14:"/tmp/phpzVFQor";i:2;s:14:"/tmp/phpaRetfI";}}s:5:"error";a:1:{i:0;a:2:{i:1;i:0;i:2;i:0;}}s:4:"size";a:1:{i:0;a:2:{i:1;i:61028;i:2;i:16197;}}}}');

class RequestUploadedFileTest extends UnitTest {
	protected function service() {
		$uploadedFiles = new RequestUploadedFiles();
		print_r($uploadedFiles->toArray());
		die("OK");
	}
	
	private function parse1() {
		$tblOutput = array();
		foreach($_FILES as $k=>$v) {
			foreach($v as $name=>$value) {
				if(is_array($value)) {
					foreach($value as $k1=>$v1) {
						$tblOutput[$k][$k1] = $this->parse2($name, $v1, (isset($tblOutput[$k][$k1])?$tblOutput[$k][$k1]:array()));
					}
				} else {
					$tblOutput[$k][$name] = $value;
				}
			}
		}
		return $tblOutput;
	}
	
	private function parse2($name, $v, $oldArray=array()) {
		$tblOutput = $oldArray;
		foreach($v as $key=>$value) {
			if(is_array($value)) {
				$tblOutput[$key] = $this->parse2($name, $value, (!empty($oldArray)?$oldArray[$key]:array()));
			} else {
				$tblOutput[$key][$name] = $value;
			}
		}
		return $tblOutput;
	}
}

new RequestUploadedFileTest();