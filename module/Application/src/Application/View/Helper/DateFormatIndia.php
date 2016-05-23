<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class DateFormatIndia extends AbstractHelper
{
    public function __invoke($date)
    {
        $dareArr = explode('-',$date);
        if($dareArr[1] < 10)
        {
            $dareArr[1] = '0'.$dareArr[1];
        }
        if($dareArr[2] < 10)
        {
            $dareArr[2] = '0'.$dareArr[2];
        }
        return $dareArr[2].'/'.$dareArr[1].'/'.$dareArr[0];
    }
}