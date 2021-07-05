<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

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
    public function IndexHome(){
        $role = auth()->user()->role;
        if($role == 'Administrator'){
            return view('admin.pages.home');
        }else{
            $cached = Cache::get('data-worksheet', false);
            $x = $cached->toArray();
            $arr['menus'] = [];
            foreach($x as $key => $data){
                $owner_name = isset($x[$key][0][0][4]) ? $x[$key][0][0][4] : 0;
                if($owner_name == $role){
                    array_push($arr['menus'], $key);
                }
            }
            $first_menus = $arr['menus'][0];
            return redirect("/mining?d=$first_menus");
        }
    }
    public function IndexMasterUser(){
        return view('admin.pages.master.user');
    }
    public function GetMasterUser(){
        /* --------------
        / HEAD DATA
        --------------- */
        $data_array['columns'] = [];
        $data_array['data'] = [];
        $title = [
            "Edit",
            "No.",
            "Name",
            "Email",
            "Role",
        ];
        foreach ($title as $key => $value) {
            array_push($data_array['columns'], ["title" => $value]);
        }
        $query = User::select('id','name','email','role')->orderBy('id','asc')->get();
        foreach($query as $key => $val){
            $edit = "<button class='btn btn-primary waves-effect' id='edit' data-id='$val->id' data-name='$val->name' data-email='$val->email' data-role='$val->role' data-toggle='modal' data-target='#editModal'><i class='material-icons'>edit</i></button>";
            $arr = [
                $edit,
                $key+1,
                $val->name,
                $val->email,
                $val->role,
            ];
            array_push($data_array['data'],$arr);
        }
        return $data_array;
    }
    public function PutMasterUser(Request $request){
        $id = $request->input('id');
        try {
            $data = User::findOrFail($id);
            if($request->input('password')){
                $isi = [
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "role" => $request->input('role'),
                    "password" => Hash::make($request->input('password')),
                ];
            }else{
                $isi = [
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "role" => $request->input('role'),
                ];
            }
            $data->update($isi);
            return ['data' => 200];
        } catch (\Throwable $th) {
            return ['data' => null];
        }
    }
    public function AddMasterUser(Request $request){
        try {
            User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'role' => $request->input('role'),
                'password' => Hash::make($request->input('password')),
            ]);
            return ['data' => 200];
        } catch (\Throwable $th) {
            return ['data' => null];
        }
    }
    public function IndexValues(Request $request){
        $cached = Cache::get('data-worksheet', false);
        if(!$cached){
            $s = new SheetController;
            $df = $s->GetAll();
        }else{
            $df = $cached;
        }
        $x = $df->toArray()[$request->input('d')];
        $arr = [
            'worker_name' => isset($x[0][0][2]) ? $x[0][0][2] : 0,
            'owner_name' => isset($x[0][0][4]) ? $x[0][0][4] : 0,
            'vga' => isset($x[0][1][2]) ? $x[0][1][2] : 0,
            'quantity' => isset($x[0][1][4]) ? $x[0][1][4] : 0,
            'placement' => isset($x[0][2][2]) ? $x[0][2][2] : 0,
            'start_mining' => isset($x[0][2][4]) ? $x[0][2][4] : 0,
            'customer_share' => isset($x[0][3][2]) ? $x[0][3][2] : 0,
            'company_share' => isset($x[0][3][4]) ? $x[0][3][4] : 0,
            'wallet_address' => isset($x[0][4][2]) ? $x[0][4][2] : 0,
            'total_eth_customer' => isset($x[0][5][6]) ? $x[0][5][6] : 0,
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
        $cached = Cache::get('data-worksheet', false);
        if(!$cached){
            $s = new SheetController;
            $df = $s->GetAll();
        }else{
            $df = $cached;
        }
        $x = $df->toArray()[$request->input('d')];
        if(isset($x[0])){
            foreach($x[0] as $key => $value){
                if($key >= 6){
                    if(!$x[0][$key][0]){continue;}
                    $arr2 = [];
                    $arr_sanitizer = [];
                    foreach($x[0][$key] as $key2 => $value2){
                        if($key2 >= 0 || $key2 <= 4){
                            if($key2 == 1){
                                $value2 = $this->FormatDateTime($value2);
                            }
                            array_push($arr2, $value2);
                            array_push($arr_sanitizer, $value2);
                        }
                    }
                    if(count($x[0][$key]) == 1){
                        for($i=0;$i<5;$i++){
                            array_push($arr2, "");
                        }
                    }
                    else if(count($x[0][$key]) == 2){
                        for($i=0;$i<4;$i++){
                            array_push($arr2, "");
                        }
                    }
                    else if(count($x[0][$key]) == 3){
                        for($i=0;$i<3;$i++){
                            array_push($arr2, "");
                        }
                    }
                    else if(count($x[0][$key]) == 4){
                        for($i=0;$i<2;$i++){
                            array_push($arr2, "");
                        }
                    }
                    else{
                        for($i=0;$i<1;$i++){
                            array_push($arr2, "");
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
        $cached = Cache::get('data-worksheet', false);
        if(!$cached){
            $s = new SheetController;
            $df = $s->GetAll();
        }else{
            $df = $cached;
        }
        $x = $df->toArray()[$request->input('d')];
        if(isset($x[0])){
            foreach($x[0] as $key => $value){
                if(isset($x[0][$key][9]) && $key >= 6){
                    if(!$x[0][$key][9]){break;}
                    $arr2 = [];
                    $arr_sanitizer = [];
                    foreach($x[0][$key] as $key2 => $value2){
                        if($key2 == 9 || $key2 == 10 || $key2 == 11){
                            if($key2 == 10){
                                $value2 = $this->FormatDateTime($value2);
                            }
                            array_push($arr2, $value2);
                            array_push($arr_sanitizer, $value2);
                        }
                    }
                    if(count($x[0][$key]) == 10){
                        for($i=0;$i<2;$i++){
                            array_push($arr2, "");
                        }
                    }else{
                        for($i=0;$i<1;$i++){
                            array_push($arr2, "");
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
