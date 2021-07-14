<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SheetController extends Controller
{
    public $ttl = 60 * 60 * 24;
    public $spreadsheetId = "15vgY5sP3fIOxQvYQ5uiJeqZklpaFgAnOKSK82HZK3No";
    public $range = "A:L";
    public function GetWorksheet(){
        $curl = curl_init();
        $spreadsheetId = $this->spreadsheetId;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://208.87.134.42:5100/get-spreadsheet/$spreadsheetId",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
    public function GetValue($request){
        $curl = curl_init();
        $spreadsheetId = $this->spreadsheetId;
        $range = $this->range;
        $sheet_title = $request;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://208.87.134.42:5100/get-value/$spreadsheetId/$sheet_title/$range",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }
    public function GetAll(){
        Artisan::call('get:data');
    }
    public function GetAllFunc(){
        $y = $this->GetWorksheet();
        $values = $y->worksheet_list->sheet_title ?: [];
        foreach($values as $key => $data){
            if($key != count($values)-1){
                $arr_x[$data] = [];
                $x = $this->GetValue($data);
                array_push($arr_x[$data], $x->value[0]);
            }
        }
        if(count($arr_x)!=0){
            Cache::forget('data-worksheet');
            Cache::forever('data-worksheet', collect($arr_x));
            return ResponseFormatter::success(null, "Success", 200);
        }else{
            return ResponseFormatter::success(null, "Server to busy", 400);
        }
    }
}
