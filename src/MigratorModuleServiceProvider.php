<?php namespace Bloveless\MigratorModule;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

class MigratorModuleServiceProvider extends AddonServiceProvider
{
    /**
     * Routes for the module
     *
     * @var array
     */
    protected $routes = [
        'admin/migrator'                                                    => 'Bloveless\MigratorModule\Http\Controller\Admin\FileMigratorController@index',
        'admin/migrator/migrate_files/migrate'                              => 'Bloveless\MigratorModule\Http\Controller\Admin\FileMigratorController@migrate',
        'admin/migrator/migrate_files/{lowerLimit}/{upperLimit}' => 'Bloveless\MigratorModule\Http\Controller\Admin\FileMigratorController@migrateFiles',
        'admin/migrator/migrate_pages'                                      => 'Bloveless\MigratorModule\Http\Controller\Admin\PageMigratorController@migratePages',
        'admin/migrator/migrate_column'                                     => 'Bloveless\MigratorModule\Http\Controller\Admin\ColumnMigratorController@index',
        'admin/migrator/migrate_column/migrate'                             => 'Bloveless\MigratorModule\Http\Controller\Admin\ColumnMigratorController@migrate',
        'admin/migrator/migrate_column/{table}'                             => 'Bloveless\MigratorModule\Http\Controller\Admin\ColumnMigratorController@getColumns',
        'admin/migrator/settings'                                           => 'Bloveless\MigratorModule\Http\Controller\Admin\SettingsController@edit'
    ];
}