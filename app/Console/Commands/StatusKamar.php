<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Transaction,kamar};
use Carbon\carbon;

class StatusKamar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statuskamar:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek status sewa kamar';

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
        $kamar = Transaction::where('status','Proses')->get();
        foreach ($kamar as $item) {
          $date = $item->tgl_sewa;
          $datenow = Carbon::now();
          $now = Carbon::parse($date)->diffInDays($datenow);
          $nows = $item->hari + $now;
          $sisa = $nows;

          if ($sisa == 0) {
            Transaction::where('id', $item->id)->update([
              'status'  => 'Done'
            ]);

            kamar::where('id', $item->kamar_id)->update([
              'sisa_kamar'  => $item->kamar->sisa_kamar + 1
            ]);
          }

          $this->info('Cek Status Sewa Kamar Sukses');
        }
    }
}
