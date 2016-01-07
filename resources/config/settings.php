<?php

return [
    'base_url' => [
        'type'     => 'anomaly.field_type.url',
        'required' => true
    ],
    'disk'  => [
        'type'     => 'anomaly.field_type.relationship',
        'required' => true,
        'config'   => [
            'related' => 'Anomaly\FilesModule\Disk\DiskModel'
        ]
    ]
];
