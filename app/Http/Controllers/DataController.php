<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use App\Jobs\DataMigrationJob;

class DataController extends Controller
{
    public function index(Request $request){

     /**
      * payload array for the queue
      */

      $payload = array();

     /**
      * Check the content type
      */

      if($request->getContentType() == 'xml'){

        $xml = simplexml_load_string($request->getContent());
        $payload = json_encode($xml);
        $this->dispatch(new DataMigrationJob($payload));

      }else if($request->hasFile('excel') || $request->hasFile('csv')){

        \Excel::load($request->file('excel'), function($reader) {
            $payload = json_encode($reader->all());
            $this->dispatch(new DataMigrationJob($payload));
        });

      }
    }
}
