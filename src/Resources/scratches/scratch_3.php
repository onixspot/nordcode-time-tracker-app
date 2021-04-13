<?php

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;

ini_set('auto_detect_line_endings', true);

require __DIR__.'/../../../vendor/autoload.php';


$map = new ArrayCollection();
if (($fpr = fopen(__DIR__.'/../dictionaries/media-types.csv', 'rb')) !== false) {
    while (false !== ([$key, $value] = fgetcsv($fpr, 0, ';'))) {
        $map->set($key, $value);
    }

    fclose($fpr);
}

class CsvReader
{
    private File $file;

    private function __construct(string $filepath)
    {
        $this->file = new File($filepath);
    }

    public static function readFile(string $filepath)
    {
        return new self($filepath);
    }
}

dd($map->get('pdf'));