<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Alimentacion\ListadoTurnoController;
class AprobarMerienda extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aprobar:merienda';

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
     * @return mixed
     */
    public function handle()
    {
        $idalimento=3; //merienda id=3
        $objAprobarMerienda = new ListadoTurnoController();
        echo $objAprobarMerienda->aprobarAlimentoJob($idalimento);
    }
}