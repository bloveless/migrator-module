<?php namespace Bloveless\MigratorModule\Http\Controller\Admin;

use Anomaly\PostsModule\Command\GenerateRoutesFile;
use Anomaly\SettingsModule\Setting\Form\SettingFormBuilder;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

/**
 * Class SettingsController
 *
 * @link          http://www.control4.com
 * @author        Control4 <bloveless@control4.com>
 * @author        Brennon Loveless <bloveless@control4.com>
 * @package       Bloveless\MigratorModule\Http\Controller\Admin
 */
class SettingsController extends AdminController
{

    /**
     * Return a form to edit settings for the posts module.
     *
     * @param SettingFormBuilder $settings
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit(SettingFormBuilder $settings)
    {
        return $settings->render('bloveless.module.migrator');
    }
}