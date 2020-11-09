<?php

namespace App\Http\Controllers;

use Illuminate\Filesystem\Cache;
use Illuminate\Http\Request;
use App\Models\Offices;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis as Redis;

/**
 * Class apiOfficesController
 * @package App\Http\Controllers
 */
class apiOfficesController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllOffices(Request $request){
        try {

            if(Redis::exists('allOffices')){
                $offices = (Redis::get('allOffices'));
            }else {
                $offices = Offices::all();
                Redis::set('allOffices',($offices));
            }
            return response()->json([
                "message" => ($offices)
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                "message" => ('error retrieving data:'.$e->getMessage())
            ], 422);


        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function newOffice(Request $request){
        try {
            $offices = new Offices();
            $offices->name = $request->name;
            $offices->address = $request->address;
            $offices->save();
            //all ok return json ok response to api
            return response()->json([
                "message" => "New office with id:".$offices->id.' and name :'.$offices->name.' saved'
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                "message" => 'Cannot create :'.$e->getMessage()
            ], 422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOffice(Request $request){
        try {
            $officeToDelete = Offices::find($request->id);
            if($officeToDelete!=null) {
                $officeToDelete->delete();
                //all ok ,return json ok response to api
                return response()->json([
                    "message" => "office with id:".$officeToDelete." deleted sucesfully!"
                ], 201);
            }else{
                return response()->json([
                    "message" => 'office with id:'.$request->id.' not exist'
                ], 422);
            }

        }catch (\Exception $e){
            return response()->json([
                "message" => 'Cannot delete :'.$e->getMessage()
            ], 422);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOffice(Request $request){
        try {
            $idToUpdate = $request->id;
            $officeToUpdate = Offices::findOrFail($idToUpdate);
            $officeToUpdate->name = $request->name;
            $officeToUpdate->address = $request->address;
            $officeToUpdate->save();

            //all ok return json ok response to api
            return response()->json([
                "message" => "office with id:".$idToUpdate." updated sucesfully!"
            ], 201);
        }catch (\Exception $e){
            return response()->json([
                "message" => 'Cannot update :'.$e->getMessage()
            ], 422);
        }
    }

}
