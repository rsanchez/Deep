<?php

namespace rsanchez\Deep\Fieldtype;

use rsanchez\Deep\Fieldtype\Fieldtype;
use rsanchez\Deep\Common\Field\AbstractField;
use rsanchez\Deep\Property\AbstractProperty;
use rsanchez\Deep\FilePath\Repository as FilePathRepository;
use rsanchez\Deep\Col\Factory as ColFactory;
use Pimple;
use stdClass;

class Factory extends Pimple
{
    public function __construct(FilePathRepository $filePathRepository, ColFactory $colFactory)
    {
        parent::__construct();

        $this['filePathRepository'] = $filePathRepository;
        $this['colFactory'] = $colFactory;

        $this['date'] = $this->factory(function ($container) {
            return new Date($container['row']);
        });

        $this['file'] = $this->factory(function ($container) {
            return new File($container['row'], $container['filePathRepository']);
        });

        //@TODO enable this
        /*
        $this['matrix'] = $this->factory(function ($container) {
            return new Matrix($container['row'], $container, $container['colFactory']);
        });
        */

        $this['fieldtype'] = $this->factory(function ($container) {
            return new Fieldtype($container['row']);
        });
    }

    public function createFieldtype(stdClass $row)
    {
        $this['row'] = $row;

        if (isset($this[$row->name])) {
            return $this[$row->name];
        }

        return $this['fieldtype'];
    }
}
