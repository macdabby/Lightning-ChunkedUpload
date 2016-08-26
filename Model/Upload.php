<?php

namespace Modules\ChunkedUpload\Model;

use Lightning\Model\Object;
use Lightning\Tools\ClientUser;
use Lightning\Tools\Database;
use Lightning\Tools\Security\Random;

/**
 * Class Upload
 * @package Modules\ChunkedUpload\Model
 *
 * @property integer $id
 * @property integer $upload_id
 * @property integer $uploaded_bytes
 * @property integer $created
 * @property string $locator
 * @property integer $complete
 */
class UploadOverridable extends Object {
    const TABLE = 'chunked_upload';
    const PRIMARY_KEY = 'upload_id';

    /**
     * Create a new object with a random locator.
     *
     * @param array $settings
     *   Additional values to add to the entry on creation.
     *
     * @return Upload
     *   The upload object.
     */
    public static function create($settings = []) {
        $db = Database::getInstance();
        do {
            $locator = Random::get(64, Random::HEX);
            try {
                $id = $db->insert(static::TABLE, ['locator' => $locator, 'created' => time()]);
            } catch (\Exception $e) {
                // Try again.
            }
        } while (empty($id));
        $upload = static::loadByID($id);
        $upload->setData($settings + $upload->getData());
        $upload->save();
        return $upload;
    }

    public static function loadByLocator($locator) {
        if ($row = Database::getInstance()->selectRow(static::TABLE, [
            'user_id' => ClientUser::getInstance()->id,
            'locator' => $locator
        ])) {
            return new static($row);
        }
        return null;
    }
}
