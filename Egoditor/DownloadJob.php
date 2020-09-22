<?php


namespace Egoditor;


use Illuminate\Database\Eloquent\Model;

class DownloadJob extends Model
{
        public const SYNC_PENDING = 'pending';
        public const SYNC_FETCHED_CSV_INFO = 'fetched_csv_info';
        public const SYNC_DOWNLOAD_STARTED = 'downloaded_started';
        public const SYNC_DOWNLOAD_COMPLETE = 'downloaded_complete';
        public const SYNC_UNZIPPED = 'unzipped';
        public const SYNC_STORED_TO_TEMP_TABLE = 'stored_to_temp_table';
        public const SYNC_COMPLETED = 'completed';
}
