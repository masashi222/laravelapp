<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\KeyRecord;
use Illuminate\Support\Facades\DB;

class KeyGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batch:keygenerate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '本日のキー生成';

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
        //打刻キーの生成
        date_default_timezone_set('Asia/Tokyo');
        $date = date('Y-m-d');
        $length = 4;
        $max = pow(10, $length) - 1;
        $rand = random_int(0, $max);
        $code = sprintf('%0'. $length. 'd', $rand);
        DB::table('key_records')->delete();
        $key = new KeyRecord();
        $key->fill([
            'date' => $date,
            'code' => $code,
        ])->save();
    }
}
