<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SheetController extends Controller
{
    public $spreadsheetId = "15vgY5sP3fIOxQvYQ5uiJeqZklpaFgAnOKSK82HZK3No";
    public function GetWorksheet(){
        $curl = curl_init();
        $spreadsheetId = $this->spreadsheetId;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://45.76.182.41:5000/get-spreadsheet/$spreadsheetId",
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
        $sheet_title = $request;
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://45.76.182.41:5000/get-value/$spreadsheetId/$sheet_title",
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
}