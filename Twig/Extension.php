<?php

namespace BurdaPraha\FrontendBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;

class Extension extends \Twig_Extension
{
    /**
     * @var RequestStack
     */
    private $requestStack;


    /**
     * @var
     */
    private $rootDir;


    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack, $rootDir)
    {
        $this->requestStack = $requestStack;
        $this->rootDir = $rootDir;
    }


    /**
     * @return array
     */
    public function getFunctions()
    {
        $requestStack = $this->requestStack;

        return [
            new \Twig_SimpleFunction('detectOS', function() use ($requestStack) {
                $request = $requestStack->getCurrentRequest();
                return $this->checkOs($request->headers->get('User-Agent'));
            })
        ];
    }


    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('goPayToHuman', function($number) {
                $crowns = $number / 100;


                return $crowns;
            }),

            new \Twig_SimpleFilter('currencyToSymbol', function($iso) {

                switch($iso)
                {
                    case 'CZK':
                        return 'Kč';
                    break;

                    case 'EUR':
                        return '€';

                    default:
                        return $iso;
                }
            }),

            new \Twig_SimpleFilter('version', function($relative_path) {

                $absolute_path  = "{$this->rootDir}{$relative_path}";
                $time_changed   = filemtime($absolute_path) ? file_exists($absolute_path) : 'FILE_NOT_EXIST';


                return "{$relative_path}?v={$time_changed}";

            })
        ];
    }


    /**
     * @param $user_agent string <you can get it by: $request->headers->get('User-Agent');>
     * @return string
     */
    public function checkOs($user_agent)
    {
        $os_platform    = "Unknown OS Platform";
        $os_array       = [
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
        ];

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }

        if ($os_platform[0] == 'i')
        {
            $os = "ios";
        }
        else if ($os_platform == 'Android')
        {
            $os = "android";
        }
        else
        {
            //retrun default (android) if not recognized
            $os = "android";
        }


        return $os;
    }
}