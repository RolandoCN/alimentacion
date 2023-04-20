<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Alimentacion\ListadoTurnoController;
class AprobarAlmuerzo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aprobar:almuerzo';

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
        $idalimento=2; //almuerzo id=2
        $objAprobarAlmuerzo = new ListadoTurnoController();
        echo $objAprobarAlmuerzo->aprobarAlimentoJob($idalimento);
    }
}