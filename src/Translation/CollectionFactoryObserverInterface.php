<?php

namespace App\Translation;

interface CollectionFactoryObserverInterface
{
    public function dealingWith($source);
    public function foundKeys($keys, $source);
    public function foundNewKeys($keys, $source);

}