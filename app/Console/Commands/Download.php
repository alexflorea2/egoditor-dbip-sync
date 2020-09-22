<?php

namespace App\Console\Commands;

use Egoditor\DownloadJob;
use Egoditor\SyncException;
use Egoditor\SyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Download extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbip:update {--S|stage=complete : from what stage to start} {--A|auto=false : ask user input}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Utility for syncing DB-IP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param SyncService $client
     * @return void
     */
    public function handle(SyncService $client)
    {
        $possibleStages = [
            'complete',
            'fetch',
            'unzip',
            'insert'
        ];

        $stage = $this->option('stage');
        $auto = filter_var($this->option('auto'), FILTER_VALIDATE_BOOLEAN);

        if( !in_array($stage,$possibleStages) )
        {
            $this->output->warning($stage . ' is not a valid option. Please select from: '. implode(', ', $possibleStages));
            return;
        }

        $this->output->title('Sync with DB-IP');

        switch( $stage )
        {
            case 'fetch':
                $job = new DownloadJob();
                $processUntilStage = DownloadJob::SYNC_DOWNLOAD_COMPLETE;
                break;
            case 'unzip':
                $job = DownloadJob::where('sync_stage', DownloadJob::SYNC_DOWNLOAD_COMPLETE)->orderBy('id','DESC')->first();
                $processUntilStage = DownloadJob::SYNC_UNZIPPED;
                break;
            case 'insert':
                $job = DownloadJob::where('sync_stage', DownloadJob::SYNC_UNZIPPED)->orderBy('id','DESC')->first();
                $processUntilStage = DownloadJob::SYNC_COMPLETED;
                break;
            default:
                $job = new DownloadJob();
                $processUntilStage = DownloadJob::SYNC_COMPLETED;
                break;
        }

        if( !$job )
        {
            if( $auto )
            {
                $this->output->warning("Could not find a Job for the selected `{$stage}` stage, starting a new one.");
            }
            else
            {
                $continue = $this->output->choice("Could not find a Job for the selected `{$stage}` stage. Create a new one.",['yes','no']);
                if( $continue == 'no' )
                {
                    return;
                }
            }
            $job = new DownloadJob();
            $processUntilStage = DownloadJob::SYNC_DOWNLOAD_COMPLETE;
        } else
        {
            $jobInfo = [
                'id' => $job->id,
                'name' => $job->name,
                'sha1sum' => $job->sha1sum,
                'sync_stage' => $job->sync_stage,
                'created_at' => $job->created_at,
            ];

            $this->output->table(array_keys($jobInfo), [$jobInfo]);
        }

        $client->setURL(env('DBIP_URL'));

        try {
            while($job->sync_stage !== $processUntilStage)
            {
                switch ($job->sync_stage)
                {
                    case null:
                    case DownloadJob::SYNC_PENDING:
                        $this->output->section('Fetching CSV info');
                        $job = $client->syncCsvInfo($job);
                        $this->output->success('Done');
                        break;

                    case DownloadJob::SYNC_FETCHED_CSV_INFO:
                        $this->output->section('Downloading CSV file');
                        $bar = $this->output->createProgressBar(100);
                        $bar->start();
                        $job = $client->syncCsvFile($job,  [
                            'progress' => function ($download_size, $downloaded) use($bar) {
                                if( $download_size === 0 )
                                {
                                    $progress = 0;
                                }
                                else
                                {
                                    $progress = ceil(($downloaded * 100) / $download_size);
                                }
                                $bar->setProgress($progress);
                            },
                        ]);
                        $bar->finish();
                        $this->output->success('Done');
                        break;

                    case DownloadJob::SYNC_DOWNLOAD_COMPLETE:
                        $this->output->section('Unzipping');
                        $job = $client->syncUnzip($job);
                        $this->output->success('Done');
                        break;

                    case DownloadJob::SYNC_UNZIPPED:
                        $this->output->section('Importing to database');
                        $job = $client->syncImportTable($job);
                        $this->output->success('Done');
                        break;

                    case DownloadJob::SYNC_COMPLETED:
                        $this->output->success('Completed');
                        $this->output->text('Import is complete.');
                        break;
                }
            }
        }catch (SyncException $e)
        {
            $this->output->note("Process failed with the following messages (more information can be found in the logs).");
            $this->output->error($e->getMessage());
            if($e->getPrevious())
            {
                $this->output->error($e->getPrevious()->getMessage());
            }

            Log::error($e->getMessage());
        }

    }
}
