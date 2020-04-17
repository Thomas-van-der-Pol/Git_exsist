<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RefreshCacheVersion  extends Command {

    protected $name = 'refreshcacheversion';
    protected $description = 'Refresh cache version from CLI';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Cache::forever('cache_version_number', time());
        var_dump('Cache number refreshed!');
    }

}