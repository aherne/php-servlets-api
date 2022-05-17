<?php

namespace Test\Lucinda\STDOUT\Request;

use Lucinda\STDOUT\Request\UploadedFiles;
use Lucinda\UnitTest\Result;

class UploadedFilesTest
{
    public function toArray()
    {
        $files = [
            "a"=>[
                "name"=>"2021-05-10-210425.jpg",
                "full_path"=>"2021-05-10-210425.jpg",
                "type"=>"image\/jpeg",
                "tmp_name"=>"\/tmp\/phprryscK",
                "error"=>0,
                "size"=>86181
            ],
            "b"=>[
                "name"=>[
                    "2021-05-10-210444.jpg",
                    "2021-05-10-210500.jpg"
                ],
                "full_path"=>[
                    "2021-05-10-210444.jpg",
                    "2021-05-10-210500.jpg"
                ],
                "type"=>[
                    "image\/jpeg",
                    "image\/jpeg"
                ],
                "tmp_name"=>[
                    "\/tmp\/phpz4FYfK",
                    "\/tmp\/phpt5LLTM"
                ],
                "error"=>[
                    0,
                    0
                ],
                "size"=>[
                    85973,
                    54995
                ]
            ],
            "d"=>[
                "name"=>[
                    "e"=>[
                        "f"=>"2021-05-10-211351.jpg",
                        "h"=>"2022-04-26-105639.jpg"
                    ]
                ],
                "full_path"=>[
                    "e"=>[
                        "f"=>"2021-05-10-211351.jpg",
                        "h"=>"2022-04-26-105639.jpg"
                    ]
                ],
                "type"=>[
                    "e"=>[
                        "f"=>"image\/jpeg",
                        "h"=>"image\/jpeg"
                    ]
                ],
                "tmp_name"=>[
                    "e"=>[
                        "f"=>"\/tmp\/phpiBM3hJ",
                        "h"=>"\/tmp\/phpy5kW3M"
                    ]
                ],
                "error"=>[
                    "e"=>[
                        "f"=>0,
                        "h"=>0
                    ]
                ],
                "size"=>[
                    "e"=>[
                        "f"=>86527,
                        "h"=>93095
                    ]
                ]
            ]
        ];
        $uploadedFiles = new UploadedFiles($files);
        $results = $uploadedFiles->toArray();

        $output = [];
        $output[] = new Result(isset($results["a"]) && $results["a"]->getSize()==86181);
        $output[] = new Result(isset($results["b"][1]) && $results["b"][1]->getSize()==54995);
        $output[] = new Result(isset($results["d"]["e"]["f"]) && $results["d"]["e"]["f"]->getSize()==86527);
        return $output;
    }
}
