<?php namespace Bloveless\MigratorModule\Http\Controller\Admin;

use Anomaly\FilesModule\Disk\Contract\DiskRepositoryInterface;
use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use League\Flysystem\MountManager;
use Cache;

class FileMigratorController extends AdminController
{
    protected $baseUrl;
    protected $disk;

    public function __construct(SettingRepositoryInterface $settings, MessageBag $bag)
    {
        $this->baseUrl = $settings->get('bloveless.module.migrator::base_url')->value;
        $this->disk    = $settings->get('bloveless.module.migrator::disk')->value;

        if (!isset($this->baseUrl) || !isset($this->disk)) {
            $bag->error("The base url and disk are required for this module to function. Set them in \"Settings\"");
        }

        parent::__construct();
    }

    public function index()
    {
        $baseUrl = $this->baseUrl;
        $disk    = $this->disk;
        $files   = $this->getFilesToMigrate($this->baseUrl);

        return view('bloveless.module.migrator::admin/file/index', compact('files', 'baseUrl', 'disk'));
    }

    public function migrate(Asset $assets)
    {
        $assets->add('scripts.js', 'bloveless.module.migrator::js/admin/file.js');

        $files = $this->getFilesToMigrate($this->baseUrl);

        return view('bloveless.module.migrator::admin/file/progress', compact('files'));
    }

    public function migrateFiles(
        DiskRepositoryInterface $disksRepo,
        MountManager $manager,
        $lowerLimit,
        $upperLimit
    ) {
        $files = $this->getFilesToMigrate($this->baseUrl);

        if ($this->disk) {

            $migrated = [];
            $failed   = [];

            foreach ($files as $index => $file) {

                if (($index >= $lowerLimit) && ($index < $upperLimit)) {

                    try {
                        $manager->putStream($this->disk->slug . '://' . $file->path . '/' . $file->file,
                            fopen($file->filename, 'r'));

                        $migrated[] = [
                            'path'     => $file->path . '/' . $file->file,
                            'filename' => $file->filename
                        ];

                    } catch (\ErrorException $ex) {

                        $failed[] = [
                            'path'     => $file->path . '/' . $file->file,
                            'filename' => $file->filename
                        ];

                    }
                }

                if ($index >= $upperLimit) {

                    break;

                }
            }
        }

        return json_encode([
            'status'    => true,
            'migrated'  => $migrated,
            'failed'    => $failed,
            'remaining' => (count($files) - $upperLimit)
        ]);
    }

    /**
     * Gets data from a URL
     * CREDIT: http://davidwalsh.name/curl-download
     *
     * @param $url
     * @return mixed
     */
    private function getData($url)
    {
        $ch      = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /**
     * Get the files to migrate from the provided base url
     *
     * @param $baseUrl
     * @return null
     */
    private function getFilesToMigrate($baseUrl)
    {
        if ($baseUrl) {
            return Cache::remember('migrator-files', 5, function () use ($baseUrl) {
                return json_decode($this->getData($baseUrl . '/migrator/get_files'));
            });
        }

        return null;
    }
}