<?php namespace Bloveless\MigratorModule;

use Anomaly\Streams\Platform\Addon\Module\Module;

class MigratorModule extends Module
{
    /**
     * Sections to show in the admin
     *
     * @var array
     */
    protected $sections = [
        'migrate_files'  => [
            'buttons' => [
                'migrate_files' => [
                    'href'        => 'admin/migrator/migrate_files/migrate',
                    'text'        => 'bloveless.module.migrator::button.migrate_files'
                ]
            ]
        ],
        'migrate_pages'  => [
            'buttons' => [
                'migrate_pages' => [
                    'text' => 'bloveless.module.migrator::button.migrate_pages'
                ]
            ]
        ],
        'migrate_column' => [
            'buttons' => [
                'migrate_column' => [
                    'text' => 'bloveless.module.migrator::button.migrate_column'
                ]
            ]
        ],
        'settings'
    ];
}