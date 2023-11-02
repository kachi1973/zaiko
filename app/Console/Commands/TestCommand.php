<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Zaiko;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Test:Test';

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
        $z = Zaiko::find(1);
        $this->info($z->get_rel_rec([10], [10,20]));
        $this->info($z->sinsei_suu);
        $this->info($z->kashi_suu);
        return 0;
    }
}
