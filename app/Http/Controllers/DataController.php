<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use App\Jobs\DataMigrationJob;

class DataController extends Controller
{
    public function index(Request $request){

     /**
      * payload for the queue
      */

      $payload = '';

     /**
      * Check the content type
      */

      if($request->getContentType() == 'xml'){

        $xml = simplexml_load_string($request->getContent());
        $payload = json_encode($xml);
        Queue::push(new DataMigrationJob($payload));

      }else if($request->hasFile('excel')){

        \Excel::load($request->file('excel'), function($reader) {

            $payload = json_encode($reader->all());
            Queue::push(new DataMigrationJob($payload));

        });

      }
    }
}
