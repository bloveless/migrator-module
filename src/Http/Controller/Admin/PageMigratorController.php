<?php namespace Bloveless\MigratorModule\Http\Controller\Admin;

use Anomaly\PagesModule\Page\PageModel;
use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Model\Pages\PagesTypesEntryModel;
use Anomaly\Streams\Platform\Stream\Contract\StreamRepositoryInterface;

class PageMigratorController extends AdminController
{
    protected $baseUrl = '';

    public function __construct(SettingRepositoryInterface $settings)
    {
        $this->baseUrl = $settings->get('bloveless.module.migrator::base_url');

        parent::__construct();
    }

    public function migratePages(PageModel $pages, PagesTypesEntryModel $types)
    {
        // dump($pages->getStream()->getEntryModel()->getAssignments());
        // dump($pages->getStream()->getAssignmentFieldSlugs());

        // dd($types->all());

        $pages = json_decode($this->getData($this->baseUrl->value.'/migrator/get_pages'));
        // $page_types = json_decode($this->getData($this->baseUrl->value . '/migrator/get_page_types'));

        dd($pages);
        // dd($page_types);

        /*
        $streams = json_decode($this->getData($this->baseUrl . '/migrator/get_pages'));

        foreach($streams as $stream) {
            echo("<b>" . $stream->stream->stream_name . "</b>");
            echo "\r\n<br>------\r\n<br>";
            foreach($stream->assignments as $assignment) {
                echo($assignment->field_slug);
                echo "\r\n<br>";
                echo($assignment->field_type);
                echo "\r\n<br>";
                echo($assignment->field_data);
                echo "\r\n<br>---\r\n<br>";
            }
            echo "\r\n<br>--------------\r\n<br>\r\n<br>";
        }

        var_dump($streams);
        */
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
}