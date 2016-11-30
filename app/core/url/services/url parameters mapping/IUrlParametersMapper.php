<?php

namespace Url;

interface IUrlParametersMapper
{
    /**
     * @return string
     */
    public function getPresenter();

    /**
     * @return array
     */
    public function getUrlMappings();
}