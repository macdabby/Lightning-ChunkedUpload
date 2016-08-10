<?php

namespace Modules\ChunkedUpload\Database\Schema;

use Lightning\Database\Schema;

class User extends Schema {

    const TABLE = 'chunked_upload';

    public function getColumns() {
        return array(
            'upload_id' => $this->autoincrement(),
            'user_id' => $this->int(true),
            'uploaded_bytes' => $this->int(true, self::BIGINT),
            'created' => $this->int(true),
            'locator' => $this->varchar(64),
        );
    }

    public function getKeys() {
        return [
            'primary' => 'upload_id',
            'user_id' => [
                'columns' => ['email'],
                'unique' => false,
            ],
            'locator' => [
                'columns' => ['locator'],
                'unique' => true,
            ]
        ];
    }
}
