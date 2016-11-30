<?php

namespace Url\Services;

use Nette\SmartObject;
use Url\Url;

class UrlParametersConverter
{
    use SmartObject;
    
    
    /**
     * Filled from extensions
     * @var array
     */
    private $mappings = [];


    /**
     * @param string $presenter
     * @param array $urlParametersMapping
     */
    public function addMapping($presenter, array $urlParametersMapping)
    {
        $this->mappings[$presenter] = $urlParametersMapping;
    }


    /**
     * @param Url $url
     * @param array $params
     */
    public function in(Url $url, array & $params)
    {
        $this->convert($url, $params, 'in');
    }


    /**
     * @param Url $url
     * @param array $params
     */
    public function out(Url $url, array & $params)
    {
        $this->convert($url, $params, 'out');
    }


    /**
     * @param Url $url
     * @param array $params
     * @param string $direction
     */
    private function convert(Url $url, array & $params, $direction)
    {
        if (empty($this->mappings)) {
            return;
        }

        if (isset($this->mappings[$url->getPresenter()][$url->getAction()])) {
            foreach ($this->mappings[$url->getPresenter()][$url->getAction()][$direction] as $from => $to) {
                if (isset($params[$from])) {
                    $params[$to] = $params[$from];
                    unset($params[$from]);
                }
            }
        }
    }

}