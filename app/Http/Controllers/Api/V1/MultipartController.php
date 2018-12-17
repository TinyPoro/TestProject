<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MultipartController extends Controller
{
    public function __construct(){
        $media_path = storage_path('Media');

        if(!is_dir($media_path)){
            @mkdir($media_path);
        }

        $problem_dir = $media_path."/Problems";
        @mkdir($problem_dir);

        $solution_dir = $media_path."/Solutions";
        @mkdir($solution_dir);
    }

    public function postMedia(Request $request){
        $response = [
            'data' => [],
            'message' => ""
        ];

        $url = $request->get('url');
        if(!$url) {
            $response['message'] = "No url received!";

            return $request->json($response);
        }

        $type = $request->get('type');
        $id = $request->get('id');

        if(!$id) {
            $response['message'] = "No id received!";

            return $request->json($response);
        }

        $dir = $this->getDir($type, $id);

        if(stripos($url, 'base64') !== false){
            list(, $data) = explode(';', $url);
            list(, $data) = explode(',', $data);
            $media_content = base64_decode($data);
        }else{
            $media_content = file_get_contents($url);
        }
        $input_path = $this->newTmp($media_content, $dir);
        $input_name = basename($input_path);

        $response['data'] = route('show.file', [
            'type' => $type,
            'id' => $id,
            'name' => $input_name
        ]);
        $response['message'] = "OK";

        return response()->json($response);
    }

    public function showFile($type, $id, $filename){
        $dir = $this->getDir($type, $id);
        $pathToFile = $dir."/$filename";

        if(file_exists($pathToFile)) return response()->file($pathToFile);
        else return "file đã bị xóa";
    }

    private function getDir(&$type, $id){
        if($type == "Solutions"){
            $type = $type_dir = "Solutions";
        }else{
            $type = $type_dir = "Problems";
        }

        if($type_dir == "Solutions"){
            $id_dir = "solution_id_$id";
        }else{
            $id_dir = "problem_id_$id";
        }

        $dir = storage_path("Media/$type_dir/$id_dir/");

        if(!is_dir($dir)){
            @mkdir($dir);
        }

        return $dir;
    }

    protected function newTmp($input = null, $dir, $is_content = true, $wm = 'w+'){

        $filename = tempnam($dir, 'Topkid');

        if($input != null){
            if(is_resource($input)){
                $ft = fopen($filename, $wm);
                while($block = fread($input, 4096)){
                    fwrite($ft, $block);
                }
                fclose($ft);
            }elseif($is_content){
                file_put_contents($filename, $input);
            }else{
                $fi = fopen($input, 'rb');
                $ft = fopen($filename, 'wb');
                while($block = fread($fi, 4096)){
                    fwrite($ft, $block);
                }
                fclose($fi);
                fclose($ft);
            }
        }
        return $filename;
    }
}
