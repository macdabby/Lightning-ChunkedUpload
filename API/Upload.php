<?php

namespace Modules\ChunkedUpload\API;

use Exception;
use Lightning\Tools\ClientUser;
use Lightning\Tools\Configuration;
use Lightning\Tools\IO\FileManager;
use Lightning\Tools\Output;
use Lightning\Tools\Request;
use Lightning\View\API;
use Modules\ChunkedUpload\Model\Upload as UploadModel;

class Upload extends API {
    /**
     * Save data to the file.
     *
     * @return mixed
     * @throws Exception
     */
    public function post() {
        // Make sure they are signed in.
        if (ClientUser::getInstance()->isAnonymous()) {
            return Output::ACCESS_DENIED;
        }

        $locator = Request::post('locator', Request::TYPE_HEX);
        if (empty($locator)) {
            throw new Exception('Invalid file locator.');
        }

        $upload = UploadModel::loadByLocator($locator);
        if (empty($upload)) {
            throw new Exception('File not found.');
        }

        $offset = Request::post('offset', Request::TYPE_INT);
        $data = base64_decode(Request::post('data', Request::TYPE_BASE64));

        $handler = Configuration::get('chunked_upload.file_handler', 'Lightning\\Tools\\IO\\File');
        $location = Configuration::get('chunked_upload.location', 'upload');
        $file = FileManager::getFileHandler($handler, $location);

        // TODO: Make sure if the packages come in out of order that they are saved correctly.
        $file->write($upload->locator, $data, $offset);

        return Output::SUCCESS;
    }

    /**
     * Create a new file and locator.
     */
    public function postNew() {
        // Make sure they are signed in.
        if (ClientUser::getInstance()->isAnonymous()) {
            return Output::ACCESS_DENIED;
        }

        $file = UploadModel::create(['user_id' => ClientUser::getInstance()->id]);
        return [
            'locator' => $file->locator
        ];
    }
}
