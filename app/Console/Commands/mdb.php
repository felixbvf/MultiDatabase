<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Log;

class mdb extends Command
{
   
    protected $signature = 'update:dates';

   
    protected $description = 'Update date entry ';

   
    public function __construct()
    {
        parent::__construct();
    }

    
    public function handle()
    {       
        $db_md = \DB::connection('mysql');
        $afi_md = $db_md->table('affiliates')->whereNotNull('birth_date')->select('id', 'identity_card','birth_date','date_entry')->get();
        
        $db_po = \DB::connection('pgsql');
        foreach ($afi_md as $item) 
        {
            $afi_po = $db_po->table('affiliates')->where('identity_card','=', rtrim($item->identity_card))->first();
            if($afi_po)
            {                
                Log::info($afi_po->id."--".$item->id);
               
                $fecha = explode("-",$item->birth_date);
                if(strcmp($fecha[1],'00')==0)
                {                   
                   $fecha=null;
                }
                else
                {
                    $fecha = $item->birth_date;
                }                
                if($fecha)
                {   $afi_po1 = $db_po->table('economic_complements')->leftJoin('affiliates','economic_complements.affiliate_id', '=','affiliates.id')->where('identity_card','=', rtrim($item->identity_card))->select('affiliates.id', 'affiliates.identity_card','affiliates.birth_date','affiliates.date_entry')->first();

                    if(!$afi_po1)
                    {
                        $db_po->table('affiliates')->where('id','=', $afi_po->id)->update(['birth_date' => $fecha]);
                    }
                    
                }         
                
            }
        }
        Log::info('Registros '. $afi_md->count());      
        $this->info(" Termino XD");
        
    }
}
