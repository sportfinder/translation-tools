<?php

namespace App\Translation;

class Domain
{

    /** @var string */
    protected $name;

    /** @var Key[] */
    protected $keys = [];

    protected $locales = [];

    /**
     * Domain constructor.
     *
     * @param $name
     */
    public function __construct( $name )
    {
        $this->name = $name;
    }

    public function addTranslationsFromArray( $translations, $locale )
    {
        $this->addLocale($locale);
        if(!is_array($translations)){
            return;
        }
        foreach ( $translations as $key => $value )
        {
//            $key = $this->getKey( $key );
//            $key->addValueForLocale( $locale, $value );
            $this->addTranslation($key, $value, $locale);
        }
    }

    public function addTranslation($key, $translation, $locale)
    {
        if(is_array($translation))
        {
            foreach ($translation as $subKey => $subTranslation) {
                $newKey = sprintf("%s.%s", $key, $subKey);
                $this->addTranslation($newKey, $subTranslation, $locale);
            }
            return;
        }
        $key = $this->getKey( $key );
        $key->addValueForLocale( $locale, $translation );
    }

    /**
     * @return Key[]
     */
    public function getKeys()
    {
        ksort($this->keys);
        return $this->keys;
    }

    /**
     * @param Key $key
     */
    public function addKey( Key $key )
    {
        $this->keys[ $key->getName() ] = $key;
    }

    /**
     * @param $name
     *
     * @return Key
     */
    public function getKey( $name )
    {
        if ( isset( $this->keys[ $name ] ) )
        {
            return $this->keys[ $name ];
        }

        $key = new Key( $name );
        $this->addKey( $key );

        return $key;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    protected function addLocale( $locale )
    {
        if(in_array($locale, $this->locales))
        {
            return;
        }
        $this->locales[] = $locale;
    }

    /**
     * @param array $locales
     * @return Domain
     */
    public function setLocales($locales)
    {
        $this->locales = $locales;
        return $this;
    }

    /**
     * @return array
     */
    public function getLocales()
    {
        return $this->locales;
    }

}