<?php

namespace Index\Controller;

use Thumb\Controller;

class IndexController extends Controller
{

    public function _init()
    {

    }

    public function index()
    {
        $this->display();
    }

    public function test()
    {
        $arr = array(
            1,
            3,
            1,
            2,
            array(
                1,
                1,
                array(
                    1,2,3,1
                )
            ),
            array(
                1,1
            ),
            array(
                2
            ),
            array(
                2,2
            ),
        );//4 6 8出现1次,其他出现2次
        echo '<pre>';
        print_r(multiArrUnique(multiArrUnique($arr,TRUE)));
    }
}

?>