<?php

namespace rsanchez\Deep\Model;

interface FileInterface
{
    public function getUrlAttribute();

    public function getServerPathAttribute();

    public function __toString();
}
