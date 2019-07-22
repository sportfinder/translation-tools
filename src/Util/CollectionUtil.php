<?php


namespace App\Util;


use App\Translation\DomainCollection;

class CollectionUtil
{

    public static function toArray(DomainCollection $collection, $locales)
    {
        $associativeArray = [];
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

    public static function toArrayKeyFirst(DomainCollection $collection, $locales)
    {
        $associativeArray = [];
        foreach ( $collection->getDomains() as $domain )
        {
            $domainName = $domain->getName();

//            $locales = $domain->getLocales();
            $keys = $domain->getKeys();
            $associativeArray[ $domainName ] = [];
            foreach ( $keys as $key )
            {
                $associativeArray[ $domainName ][ $key->getName() ] = [];

                foreach ( $locales as $locale )
                {
                    $translation = $key->getTranslation( $locale )->getValue();
                    $associativeArray[ $domainName ][ $key->getName()][ $locale ] = $translation;
                }
            }
            uksort($associativeArray[$domainName], 'strcasecmp'  );
        }

        return $associativeArray;
    }

    public static function getLocales(DomainCollection $collection, $inputLocales)
    {
        $locales = $inputLocales;
        foreach ($collection->getDomains() as $domain) {
            $locales = array_merge($locales, $domain->getLocales());
        }
        return array_unique($locales);
    }
}