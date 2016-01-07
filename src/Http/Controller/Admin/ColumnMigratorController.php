<?php namespace Bloveless\MigratorModule\Http\Controller\Admin;

use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Anomaly\FilesModule\Folder\FolderModel;
use Anomaly\SettingsModule\Setting\Contract\SettingRepositoryInterface;
use Anomaly\Streams\Platform\Asset\Asset;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\Streams\Platform\Message\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ColumnMigratorController extends AdminController
{
    protected $baseUrl;
    protected $diskId;

    public function __construct(SettingRepositoryInterface $settings, MessageBag $bag)
    {
        $this->baseUrl = $settings->get('bloveless.module.migrator::base_url');
        $this->disk = $settings->get('bloveless.module.migrator::disk');

        if(empty($this->baseUrl) || empty($this->disk)) {
            $bag->error("The base url and disk are required for this module to function. Set them in \"Settings\"");
        }

        parent::__construct();
    }

    public function index(Asset $asset)
    {
        $asset->add('scripts.js', 'bloveless.module.migrator::js/admin/column.js');

        $tables = DB::select('SHOW TABLES');

        $tables = array_flatten(array_map(function($table) {
            return array_values((array) $table);
        }, $tables));

        return view('bloveless.module.migrator::admin/columns/index', compact('tables'));
    }

    public function getColumns($table)
    {
        $columns = Schema::getColumnListing(str_replace(Schema::getConnection()->getTablePrefix(), '' , $table));

        return json_encode($columns);
    }

    public function migrate(Request $request, MessageBag $bag, FolderRepositoryInterface $folders)
    {
        $input = $request->input();

        if(!$input['table'] || !$input['column']) {

            $bag->error("Table and column are both required");
            return redirect('/admin/migrator/migrate_column');

        }

        $data = DB::select('select `id`, ' . $input['column'] . ' from ' . $input['table']);

        echo "<table><thead><tr><th>Row</th><th>Original ID</th><th>New Id</th></tr></thead><tbody>";

        foreach($data as $row) {
            $remoteFile = json_decode($this->getData($this->baseUrl . '/migrator/get_file/' . $row->{$input['column']}));

            $folder = $folders->findByPath($remoteFile->path, $this->disk);

            $foundFile = false;
            if($folder) {
                foreach($folder->getFiles() as $file) {
                    if($file->getName() == $remoteFile->file) {
                        $foundFile = true;
                        // return 'found file ID: ' . $file->getId();
                        echo "<tr><td>" . $row->id . "</td><td>" . $row->{$input['column']} . "</td><td>" . $file->getId() . "</td>";
                    }
                }
            }

            if(!$foundFile) {
                echo "<tr><td>" . $row->id . "</td><td>" . $row->{$input['column']} . "</td><td>Could not find file to migrate to.<br>";
                echo "<b>Path:</b> " . $remoteFile->path . "<br>";
                echo "<b>File:</b> " . $remoteFile->file . "<br>";
                echo "<b>Filename:</b> " . $remoteFile->filename;
                echo "</td></tr>";
            }
        }

        echo "</tbody></table>";

        var_dump($input['table']);
        var_dump($input['column']);
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
