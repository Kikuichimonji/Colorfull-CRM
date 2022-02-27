<?php
use App\Service\HelperService;

if(!function_exists('str_random')){
    function str_random($size = 16)
    {
        return HelperService::strRandom($size);
    }
}
