<?php

namespace App\Console\Commands;

use App\Models\PaymentGateway;
use App\Models\Subscription;
use App\Services\Payment\StripeDirectChargeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InitializeProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project initialization';

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
        if (env('PROJECT_INITIALIZED', false)) {
            $this->info('Project has already been initialized.');
            return Command::SUCCESS;
        }

        $this->call('migrate:fresh');
        $this->call('db:seed');
        $this->call('config:clear');

        // file_put_contents(base_path('.env'), str_replace(
        //     'PROJECT_INITIALIZED=false', 'PROJECT_INITIALIZED=true', file_get_contents(base_path('.env'))
        // ));
        
        $this->info(Artisan::output() . 'DONT forget to run php artisan schedule:work, php artisan storage:link');

        return Command::SUCCESS;
    }
}
