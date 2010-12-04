<?php

class Template_loader extends Twig_Loader_Filesystem
{
    public function __construct($paths)
    {
        parent::__construct($paths);
    }

    protected function findTemplate($name)
    {
        $name = str_replace('.html','',$name).'.html';
        return parent::findTemplate($name);
    }
}
