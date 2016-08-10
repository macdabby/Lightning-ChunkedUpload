<?php

return [
    'package' => [
        'module' => 'ChunkedUpload',
        'version' => '1.0',
    ],
    'routes' => [
        'static' => [
            'api/chunked/upload' => 'Modules\\ChunkedUpload\\API\\Upload',
        ]
    ],
];
