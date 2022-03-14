<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Wall;
use App\Models\WallLike;

class WallController extends Controller
{

     public function getAll(){
          $array = ['error' => '', 'list' => []];

             $user = auth()->user();

              $walls = Wall::all();

              foreach($walls as $wallKey => $wallValue){
                   $walls[$wallKey]['likes'] = 0;
                   $walls[$wallKey]['liked'] = false;
                   $walls[$wallKey]['datecreated'] = date('d/m/Y H:i', strtotime($wallValue['datecreated']));

                   $likes = WallLike::where('id_wall', $wallValue['id'])->count();

                   $walls[$wallKey]['likes'] = $likes;


                   $meLikes = WallLike::where('id_wall', $wallValue['id'])
                   ->where('id_user', $user['id'])
                   ->count();

                   if($meLikes > 0){
                     $walls[$wallKey]['liked'] = true;
                   }


              }

              $array['list'] = $walls;

          return $array;
     }

     public function like($id){
         $array = ['error' => ''];

           $user = auth()->user();

           $meLikes = WallLike::where('id_wall', $id)
           ->where('id_user', $user['id'])
           ->count();


            if($meLikes > 0){
                  //remover o like
                   WallLike::where('id_wall', $id)
                           ->where('id_user', $user['id'])
                           ->delete();
                    $array['liked'] = false;
            }else {
               // add o like
               $newLike = new WallLike();
               $newLike->id_wall = $id;
               $newLike->id_user = $user['id'];
               $newLike->save();

                 $array['liked'] = true;
            }

            $array['likes'] = WallLike::where('id_wall', $id)->count();


         return $array;
     }

     public function updateWall($id, Request $request ){

               $array = ['error' => ''];
       
               $title = $request->input('title');
                $body = $request->input('body');
               if($title && $body){
       
                    $item = Wall::find($id);
                     if($item){
                          $item->title = $title;
                          $item->body = $body;
                          $item->save();
                     }else{
                          $array['error'] = 'Aviso não Localizado.';
                          return $array;
                     }
       
                 }else{
                      $array['error'] = 'CAMPOS VÁZIOS.';
                      return $array;
                 }
       
                return $array;
      }

      public function addWall(Request $request ){

          $array = ['error' => ''];

          $validator = Validator::make($request->all(), [
               'title' => 'required',
               'body' => 'required',
          ]);
 
          if(!$validator->fails()){
               $title = $request->input('title');
               $body = $request->input('body');
 
               $new = new Wall();
               $new->title = $title;
               $new->body = $body;
               $new->save();
 
          }else{
              $array['error'] = $validator->errors()->first();
              return $array;
          }
  
         return $array;
     }

     public function removeWall($id){
          $array = ['error' => ''];
             if($id){
                Wall::where('id', $id)->delete();  
             }else{
                $array['error'] = 'ID inexistente';
             }
          return $array;
       }
  
}
