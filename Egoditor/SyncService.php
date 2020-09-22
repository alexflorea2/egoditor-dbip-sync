<?php


namespace Egoditor;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SyncService
{
    private FileDownloader $downloader;
    private string $url;
    public const DBIP_TABLE = "db_ip_full";

    public function __construct(FileDownloader $downloader)
    {
        $this->downloader = $downloader;
    }

    public function setURL(string $url) : self
    {
        $this->url = $url;

        return $this;
    }

    public function syncCsvInfo(DownloadJob $model) : DownloadJob
    {
        $csvInfo = $this->downloadCSVInfo();

        $model->url = $csvInfo->url;
        $model->name = $csvInfo->name;
        $model->size = $csvInfo->size;
        $model->sha1sum = $csvInfo->sha1sum;
        $model->version = $csvInfo->version;

        $model->sync_stage = DownloadJob::SYNC_FETCHED_CSV_INFO;

        $model->save();

        return $model;
    }


    public function syncCsvFile(DownloadJob $model, array $downloaderOptions) : DownloadJob
    {
        $csvGzPath = storage_path(env('SYNC_FOLDER','sync') . DIRECTORY_SEPARATOR . $model->name );

        $model->csv_gz_path = $csvGzPath;
        $model->sync_stage = DownloadJob::SYNC_DOWNLOAD_STARTED;
        $model->save();

        if(  $this->downloadCSV($model->url, $csvGzPath, $downloaderOptions ) === false )
        {
            throw new SyncException("Could not download {$model->url} contents to {$csvGzPath} path.");
        }

        $model->sync_stage = DownloadJob::SYNC_DOWNLOAD_COMPLETE;
        $model->save();

        return $model;
    }

    public function syncUnzip(DownloadJob $model) : DownloadJob
    {
        $csvUnzippedPath = str_replace('.gz', '', $model->csv_gz_path);
        $this->unzipCSV($model->csv_gz_path, $csvUnzippedPath);

        $model->sync_stage = DownloadJob::SYNC_UNZIPPED;
        $model->save();

        if( $this->checkSha1Sum($csvUnzippedPath, $model->sha1sum) === false )
        {
            throw new SyncException("Sha1 check for {$csvUnzippedPath} does not match {$model->sha1sum} value.");
        }

        return $model;
    }

    public function syncImportTable(DownloadJob $model) : DownloadJob
    {
        $tempTableName = 'sync_job_id' . $model->id;
        $csvUnzippedPath = str_replace('.gz', '', $model->csv_gz_path);

        $this->createTempTable($tempTableName);

        if( $this->importInfile($csvUnzippedPath, $tempTableName) === false)
        {
            throw new SyncException("Cannot use import Infile");
        }

        $model->sync_stage = DownloadJob::SYNC_STORED_TO_TEMP_TABLE;
        $model->save();

        $this->swapTables($tempTableName, self::DBIP_TABLE);
        $this->cleanupTempTable($tempTableName);

        $model->sync_stage = DownloadJob::SYNC_COMPLETED;
        $model->save();

        return $model;
    }

    private function swapTables(string $table1, string $table2) : void
    {
        if( Schema::hasTable($table2) === false )
        {
            Schema::rename($table1, $table2);
        }
        else
        {
            Schema::rename($table2, 'tmp_' . uniqid());
            Schema::rename($table1, $table2);
        }
    }

    private function cleanupTempTable(string $tableName) : bool
    {
        try {
            Schema::dropIfExists($tableName);

            return true;
        }catch (\Exception $e)
        {
            Log::error($e->getMessage());
            return false;
        }
    }

    private function createTempTable(string $tableName) : bool
    {
        try {
            Schema::create($tableName, function (Blueprint $table) {
                $table->engine = 'MyIsam';

                $table->string('ip_start', 45);
                $table->string('ip_end', 45);
                $table->char('continent', 2)->nullable();
                $table->char('country', 2)->nullable();
                $table->string('stateprov', 128)->nullable();
                $table->string('district', 128)->nullable();
                $table->string('city', 128)->nullable();
                $table->string('zipcode', 128)->nullable();
                $table->float('latitude')->nullable();
                $table->float('longitude')->nullable();
                $table->integer('geoname_id')->unsigned()->nullable();
                $table->float('timezone_offset')->nullable();
                $table->string('timezone_name')->nullable();
                $table->string('weather_code', 10)->nullable();
                $table->string('isp_name')->nullable();
                $table->integer('as_number')->unsigned()->nullable();
                $table->string('connection_type')->nullable();
                $table->string('organization_name')->nullable();
            });

            return true;
        }catch (\Exception $e)
        {
            Log::error($e->getMessage());
            return false;
        }

    }

    private function importInfile(string $filePath, string $tempTableName) : bool
    {
        try {
            $query = sprintf("LOAD DATA LOCAL INFILE '%s' INTO TABLE {$tempTableName}
            CHARACTER SET utf8mb4
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\\n'
            IGNORE 1 ROWS", addslashes($filePath));

            DB::connection()->getpdo()->exec($query);

            return true;
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
            return false;
        }
    }

    private function checkSha1Sum(string $filePath, string $sum) : bool
    {
        return $sum === sha1_file($filePath);
    }

    private function unzipCSV(string $zippedfilePath, string $unzippedFilePath, int $bufferSize = 12288) : bool
    {
        try {
            $file = gzopen($zippedfilePath, 'rb');
            $out_file = fopen($unzippedFilePath, 'wb');

            while(!gzeof($file)) {
                fwrite($out_file, gzread($file, $bufferSize));
            }

            fclose($out_file);
            gzclose($file);

            return true;
        }catch(\Exception $e)
        {
            Log::error($e->getMessage());
            return false;
        }
    }

    private function downloadCSV(string $url, string $savePath, array $options = []) : bool
    {
        try {
            $this->downloader->saveToFile(
                $url,
                $savePath,
                $options
            );

            return true;
        }catch(\Exception $e)
        {
            Log::error($e->getMessage());
            return false;
        }
    }

    private function downloadCSVInfo()
    {
        $response = $this->downloader->stream($this->url);
        $json = json_decode($response);

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new SyncException("Could not fetch CSV Info, not valid json.");
        }

        if (!isset($json->csv)) {
            throw new SyncException("CSV Info not valid.");
        }

        return $json->csv;
    }
}
