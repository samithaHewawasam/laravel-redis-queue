<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Queue;
use App\Jobs\DataMigrationJob;

class DataController extends Controller
{
    public function index(Request $request){

      $request->validate([
        'file' => 'required|mimetypes:application/xml,text/csv,text/plain,application/csv,application/octet-stream'
      ]);

      $file = $request->file('file');

      switch ($request->file->getClientMimeType()) {
        case 'application/xml':
          $this->process(simplexml_load_string($request->getContent()));
          break;
        case 'application/vnd.ms-excel':
        case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
        case 'text/csv':
          $this->process(\Excel::load($file)->get());
          break;
        default:
          return response()->json(['message' => 'Unsupported Format' ], 400);
          break;
      }

    }

    protected function process($data)
    {

      $payload = json_encode($data);
      $this->dispatch(new DataMigrationJob($payload));
      return response()->json(['message' => 'dispatched' ], 200);
    }

}
