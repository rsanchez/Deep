<?php

namespace rsanchez\Entries\Entry\Field;

use rsanchez\Entries\DbInterface;
use rsanchez\Entries\Channel;
use rsanchez\Entries\FilePaths;
use rsanchez\Entries\Entry\Field;
use rsanchez\Entries\Channel\Field as ChannelField;
use rsanchez\Entries\Entry;

class File extends Field
{
    public function __construct(Channel $channel, ChannelField $channelField, Entry $entry, $value, FilePaths $filePaths)
    {
        parent::__construct($channel, $channelField, $entry, $value);

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
