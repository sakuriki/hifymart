<?php

namespace App\Console\Commands;

use App\Imports\ZoneImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class ImportVietNameZone extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'vietnam-zone:import';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

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
   * @return int
   */
  public function handle()
  {
    $this->info('Importing data...');

    Excel::import(new ZoneImport(), storage_path("app/vietnam-zone.xls"));
    if (File::isDirectory(storage_path('framework/laravel-excel'))) {
      File::deleteDirectory(storage_path('framework/laravel-excel'));
    }

    Schema::dropIfExists('wards');

    $this->info('Completed.');
  }
}
