<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public $ttl = 60 * 60 * 24;
    public function FormatDateTime($str){
        $formatter = explode('/',$str);
        try {
            if(strlen($formatter[1]) == 1){
                $format = $formatter[0]."-".$formatter[1]."-".$formatter[2];
                $date = $format ? date('Y-m-d', strtotime($format)) : null;
            }else{
                if($str){
                    $format = substr($str,6,4)."-".substr($str,3,2)."-".substr($str,0,2);
                    $date = $format ? date('Y-m-d', strtotime($format)) : null;
                }else{
                    $date = null;
                }
            }
        } catch (\Throwable $th) {
            if($str){
                $format = substr($str,6,4)."-".substr($str,3,2)."-".substr($str,0,2);
                $date = $format ? date('Y-m-d', strtotime($format)) : null;
            }else{
                $date = null;
            }
        }

        return $date;
    }
    public function WeekFromDate($date){
        // $textdt = date($date.'-01');
        $textdt = date($date.'-01', strtotime('first Week'));
        $textdt = date('Y-m-d', strtotime($textdt.'-1 days'));
        // dd($textdt);
        $dt= strtotime( $textdt);
        $currdt=$dt;
        $nextmonth=strtotime($textdt."+1 month");
        $i=0;
        $date = [
            'c_week' => [],
            'startdate' => [],
            'daystart' => [],
            'enddate' => [],
            'dayend' => [],
        ];
        do{
            $weekday= date("w",$currdt);
            $endday=abs($weekday-7);
            $startarr[$i]=$currdt;
            $endarr[$i]=strtotime(date("Y-m-d",$currdt)."+$endday day");
            $currdt=strtotime(date("Y-m-d",$endarr[$i])."+1 day");
            array_push($date['c_week'],"Week ".($i+1));
            array_push($date["startdate"], date("Y-m-d",$startarr[$i]));
            array_push($date["daystart"], date("D",$startarr[$i]));
            array_push($date["enddate"], date("Y-m-d",$endarr[$i]));
            array_push($date["dayend"], date("D",$endarr[$i]));
            $i++;
        }while($endarr[$i-1]<$nextmonth);
        return $date;
    }
    public function CacheSanitizer(){
        $cached = Cache::get('profile', false);
        if(!$cached){
            $cached = Cache::remember('profile', $this->ttl, function () {
                $df = new SheetController;
                $value = $df->GetWorksheet();
                $arr_x = [];
                $values = $value->worksheet_list->sheet_title ?: [];
                foreach($values as $key => $value){
                    if($key != count($values)-1){
                        $x = $df->GetValue($value);
                        array_push($arr_x, $x);
                    }
                }
                return collect($arr_x);
            });
            $q = $cached;
        }else{
            $q = $cached;
        }
        return $q;
    }
    public function IndexHome(){
        return view('admin.pages.home');
    }
    public function IndexValues(Request $request){
        $df = new SheetController;
        $x = $df->GetValue($request->input('d'));
        // dd($x->value[0]);
        $arr = [
            'worker_name' => isset($x->value[0][0][2]) ? $x->value[0][0][2] : 0,
            'owner_name' => isset($x->value[0][0][4]) ? $x->value[0][0][4] : 0,
            'vga' => isset($x->value[0][1][2]) ? $x->value[0][1][2] : 0,
            'quantity' => isset($x->value[0][1][4]) ? $x->value[0][1][4] : 0,
            'placement' => isset($x->value[0][2][2]) ? $x->value[0][2][2] : 0,
            'start_mining' => isset($x->value[0][2][4]) ? $x->value[0][2][4] : 0,
            'customer_share' => isset($x->value[0][3][2]) ? $x->value[0][3][2] : 0,
            'company_share' => isset($x->value[0][3][4]) ? $x->value[0][3][4] : 0,
            'wallet_address' => isset($x->value[0][4][2]) ? $x->value[0][4][2] : 0,
            'total_eth_customer' => isset($x->value[0][5][6]) ? $x->value[0][5][6] : 0,
        ];
        return view('admin.pages.content',[
            'arr' => $arr
        ]);
    }
    public function GetValues(Request $request){
        $m = $request->input('m');
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $data_sanitizer = [];
        $title = [
            "No.",
            "Date.",
            "Avg. Hash Power",
            "Income Mining",
            "Customer Share",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $df = new SheetController;
        $x = $df->GetValue($request->input('d'));
        if(isset($x->value[0])){
            foreach($x->value[0] as $key => $value){
                if($key >= 6){
                    if(!$x->value[0][$key][0]){continue;}
                    $arr2 = [];
                    $arr_sanitizer = [];
                    foreach($x->value[0][$key] as $key2 => $value2){
                        if($key2 >= 0 || $key2 <= 4){
                            if($key2 == 1){
                                $value2 = $this->FormatDateTime($value2);
                            }
                            array_push($arr2, $value2);
                            array_push($arr_sanitizer, $value2);
                        }
                    }
                    if(isset($m)){
                        array_push($data_sanitizer, $arr2);
                    } else {
                        array_push($data_array['data'], $arr2);
                    }
                }
            }
        }
        if(isset($m)){
            $sanitizer = collect($data_sanitizer);
            $date = $this->WeekFromDate($m);
            $startdate = $date['startdate'][0];
            $enddate = $date['enddate'][count($date['enddate'])-1];
            $x = $sanitizer->whereBetween(1, [$startdate, $enddate]);
            foreach($x->toArray() as $key => $val){
                array_push($data_array['data'], $val);
            }
        }
        return $data_array;
    }
    public function GetValuesWithdraw(Request $request){
        $m = $request->input('m');
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $data_sanitizer = [];
        $title = [
            "No.",
            "Date.",
            "Nominal",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $df = new SheetController;
        $x = $df->GetValue($request->input('d'));
        if(isset($x->value[0])){
            foreach($x->value[0] as $key => $value){
                if(isset($x->value[0][$key][9]) && $key >= 6){
                    if(!$x->value[0][$key][9]){break;}
                    $arr2 = [];
                    $arr_sanitizer = [];
                    foreach($x->value[0][$key] as $key2 => $value2){
                        if($key2 == 9 || $key2 == 10 || $key2 == 11){
                            if($key2 == 10){
                                $value2 = $this->FormatDateTime($value2);
                            }
                            array_push($arr2, $value2);
                            array_push($arr_sanitizer, $value2);
                        }
                    }
                    if(isset($m)){
                        array_push($data_sanitizer, $arr2);
                    } else {
                        array_push($data_array['data'], $arr2);
                    }
                }
            }
        }
        if(isset($m)){
            $sanitizer = collect($data_sanitizer);
            $date = $this->WeekFromDate($m);
            $startdate = $date['startdate'][0];
            $enddate = $date['enddate'][count($date['enddate'])-1];
            $x = $sanitizer->whereBetween(1, [$startdate, $enddate]);
            foreach($x->toArray() as $key => $val){
                array_push($data_array['data'], $val);
            }
        }
        return $data_array;
    }
}
