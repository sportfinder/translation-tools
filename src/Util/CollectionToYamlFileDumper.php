<?php


namespace App\Util;


use App\Translation\DomainCollection;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;

class CollectionToYamlFileDumper
{

    public static function collectionToFile(DomainCollection $collection, $outputPath, $requiredLanguages = [])
    {
        $locales = CollectionUtil::getLocales($collection, $requiredLanguages);
        dump($locales);

        $associativeArray = CollectionUtil::toArray($collection, $locales);

        foreach ( $collection->getDomains() as $domain )
        {
            $locales = array_merge( $requiredLanguages, $domain->getLocales() );
            foreach ( $locales as $locale )
            {
                $path = sprintf( "%s%s%s.%s.yaml", $outputPath, DIRECTORY_SEPARATOR, $domain->getName(), $locale );
                dump($path);

                if ( isset( $associativeArray[ $domain->getName() ][ $locale ] ) )
                {
                    // if the collection exist, use it!
                    self::createFile( $associativeArray[ $domain->getName() ][ $locale ], $path );
                }
                else
                {
                    // if not, create an empty collection!
                    self::createFile( EmptyCollection::createFromDomain($associativeArray[ $domain->getName() ]), $path );
                }
            }
        }
    }

    public static function createFile( $array, $outputPath )
    {
        $dumper = new Dumper();
        if ( file_exists( $outputPath ) )
        {
            unlink( $outputPath );
        }
        $file = fopen( $outputPath, 'x+' );
        $array = YamlFlattener::flatten( $array );
        ksort( $array, SORT_STRING | SORT_FLAG_CASE );

        array_walk_recursive($array, function(&$value){
            if(empty($value) AND !is_array($value)) $value = "#FIXME";
        });

        $yaml = $dumper->dump( $array, 1, 0, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK );
        $yaml = str_replace("'#FIXME'", '#FIXME', $yaml);

        fwrite( $file, $yaml );
        fclose( $file );

    }


}