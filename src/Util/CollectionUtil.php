<?php


namespace App\Util;


use App\Translation\DomainCollection;

class CollectionToArray
{

    public static function convert(DomainCollection $collection, $locales)
    {
        foreach ( $collection->getDomains() as $domain )
        {
            $domainName = $domain->getName();

//            $locales = $domain->getLocales();
            $keys = $domain->getKeys();
            $associativeArray[ $domainName ] = [];
            foreach ( $locales as $locale )
            {
                $associativeArray[ $domainName ][ $locale ] = [];

                foreach ( $keys as $key )
                {
                    $keyValue = $key->getTranslation( $locale )->getValue();
                    $associativeArray[ $domainName ][ $locale ][ $key->getName() ] = $keyValue;
                }
            }
        }

        return $associativeArray;
    }
}