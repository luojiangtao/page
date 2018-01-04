<?php

namespace luojiangtao\page;

/**
 *
 */
class Page
{
    public function __construct()
    {
        echo '__construct';
    }


    public function show(){
        echo 'show';
    }
}

$page = new Page();
$page->show();