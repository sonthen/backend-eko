<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\UserList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserListController extends Controller
{
    function getData(){

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        } //kalo mau spesifik authenticate 1 function

        //eloquent
        $userList = UserList::get();



        return response()->json($userList,200);
        //pengganti return $userList;
    }

    function addData(Request $request){

        //untuk rollback data jika ada yang error sebagian
        DB::beginTransaction();

        try{
            

            $this->validate($request, [
                'name' => 'required',
                'email'=> 'required|email'
            ]);

            $name = $request->input('name');
            $email = $request->input('email');
            $address = $request->input('address');

            //save ke database(eloquent)

            $usr = new UserList;
            $usr->name = $name;
            $usr->email = $email;
            $usr->address = $address;
            //$usr->save adalah untuk insert
            $usr->save();

            $usrList= UserList::get();
            //temannya beginTransaction(); untuk commit data
            DB::commit();
            return response()->json($usrList, 200);
        }
        catch(\Exception $e){

            //temannya beginTransaction(); untuk rollback
            DB::rollback();
            return response()->json(["message" => $e->getMessage()], 500);
        }


    }

    function deleteData(Request $request){
         

        DB::beginTransaction();

        try{

             $this->validate($request, [
                'id' => 'required'
            ]);


            $id = (integer)$request->input('id'); //integer -> untuk casting $idToRemove
        
            // punya sendiri
            // userList::where('id','=',$idToRemove)->delete();
            
            // punya kak hasbi
            $usr = UserList::find($id); //mirip seperti where
            if(empty($usr)){
                return response()->json(["message"=>"User not found"], 404);
            }
            $usr->delete();
            $usrList= UserList::get();
            DB::commit();
            return response()->json($usrList, 200);

        }

         catch(\Exception $e){

            DB::rollback();
            return response()->json(["message" => $e->getMessage()], 500);
         }
        

    }

}
