<?php

namespace rsanchez\Entries\Entry\Field;

use rsanchez\Entries\DbInterface;
use rsanchez\Entries\Channel;
use rsanchez\Entries\FilePaths;
use rsanchez\Entries\Entry\Field;
use rsanchez\Entries\Collection as EntryCollection;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entity;

class File extends Field
{
    public function __construct(
        $value,
        Channel $channel,
        ChannelField $channelField,
        EntryCollection $entries,
        $entity = null,
        FilePaths $filePaths
    ) {
        parent::__construct($value, $channel, $channelField, $entries, $entity);

        $this->filePaths = $filePaths;
    }

    public function __toString()
    {
        if (! $this->value) {
            return '';
        }

        $value = $this->value;

        if (preg_match('/^{filedir_(\d+)}/', $this->value, $match)) {
            try {
                $filePath = $this->filePaths->find($match[1]);

                return str_replace($match[0], $filePath->url, $this->value);
            } catch (Exception $e) {
                //$e->getMessage();
                return '';
            }
        }

        return $this->value;
    }
}
