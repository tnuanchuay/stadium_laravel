<?php

namespace App\Http\Controllers;

use App\Stadium;
use DB;
use App\User;
use App\Http\Requests;
use App\Field;
class StadiumController extends Controller
{
    public function getAll($id, $type){
        $user = User::where('facebook_id', '=', $id);
        if($user->count() == 0)
            return response()->json(["status"=>"err", "err"=>"User Invalid"]);
        $result = array();
        $resultSet = Field::where('type', '=', $type)
            ->join('stadiums', 'stadiums.id', '=', 'fields.stadium_id')
            ->select("fields.*")
            ->groupBy('fields.stadium_id')
            ->get();

        foreach($resultSet as $object){
            $stadium = $object->stadium;
            array_push($result, $stadium);
        }

        return response()->json(["status"=>"ok", "data"=>$result]);
    }

    public function getStadiumDetail($id, $type){
        $resultSetStadium = Stadium::where('id', '=', $id)->get();
        $resulSetPriceRate = DB::select("
            select min(price_rate.price) as max, max(price_rate.price) as min
            from price_rate, fields, stadiums
            where
            stadiums.id = fields.stadium_id and
            price_rate.field_id = fields.id and
            fields.type = :type and
            stadiums.id = :id
        ", array('type'=>$type, 'id'=>$id));
        $result = array('basic'=>$resultSetStadium[0], 'price'=>$resulSetPriceRate[0]);
        return response()->json(['status'=>'ok', 'data'=>$result]);
    }
}