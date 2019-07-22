<?php


namespace App\Util;


use App\Translation\Domain;
use App\Translation\DomainCollection;

class EmptyCollection
{

    public static function createFromDomain(DomainCollection $domain)
    {
        if(empty($domain))
        {
            throw new \LogicException("Cannot create empty collection if the domain is empty");
        }
        $aCollection = array_pop($domain);
        array_walk_recursive($aCollection, function(&$value){
            $value = "#FIXME";
        });
        return $aCollection;
    }

}