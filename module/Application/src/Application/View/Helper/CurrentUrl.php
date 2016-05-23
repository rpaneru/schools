<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class CurrentUrl extends AbstractHelper
{
    public function __invoke()
    {
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
        return $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].$_SERVER['QUERY_STRING'];
    }
}