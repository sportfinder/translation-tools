<?php

namespace App\Translation;

class DomainCollection
{
    /** @var Domain[string] */
    protected $domains;

    /**
     * @param $name
     *
     * @return Domain
     */
    public function getDomain( $name )
    {
        if ( isset( $this->domains[ $name ] ) )
        {
            return $this->domains[ $name ];
        }
        $domain = new Domain($name);
        $this->addDomain($domain);
        return $domain;
    }

    public function addDomain( Domain $domain )
    {
        $this->domains[$domain->getName()] = $domain;
    }

    /**
     * @return Domain[]
     */
    public function getDomains()
    {
        return $this->domains;
    }

}