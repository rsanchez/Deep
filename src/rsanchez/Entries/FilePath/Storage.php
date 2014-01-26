<?php

namespace rsanchez\Entries\FilePath;

use rsanchez\Entries\DbInterface;

class Storage
{
    protected $db;

    protected $uploadPrefs;

    public function __construct(DbInterface $db, $uploadPrefs = array())
    {
        $this->db = $db;

        if (is_array($uploadPrefs)) {
            foreach ($uploadPrefs as $id => $data) {
                $row = new \stdClass();
                $row->id = $id;
                $row->server_path = $data['server_path'];
                $row->url = $data['url'];
                $this->uploadPrefs[] = $row;
            }
        }
    }

    public function __invoke()
    {
        if (!is_null($this->uploadPrefs)) {
            return $this->uploadPrefs;
        }

        $this->db->select('id, server_path, url');

        $query = $this->db->get('upload_prefs');

        $this->uploadPrefs = $query->result();

        $query->free_result();

        return $this->uploadPrefs;
    }
}
