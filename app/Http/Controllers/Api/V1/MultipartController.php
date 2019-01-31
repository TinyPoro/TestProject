<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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
            'data' => '',
            'message' => ""
        ];

        $url = $request->get('url');
        if(!$url) {
            $response['message'] = "No url received!";

            return response()->json($response);
        }

        $type = $request->get('type');
        $id = $request->get('id');

        if(!$id) {
            $response['message'] = "No id received!";

            return response()->json($response);
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
        $input_path = $this->renameImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_path = $this->removeWatermark($input_path);
        $input_path = $this->desaturateImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_name = basename($input_path);

        $response['data'] = route('show.file', [
            'type' => $type,
            'id' => $id,
            'name' => $input_name
        ]);
        $response['message'] = "OK";

        return response()->json($response);
    }

    public function uploadMedia(Request $request){
        $response = [
            'data' => '',
            'message' => ""
        ];

        $resource = $request->file('file');

        if(!$resource) {
            $response['message'] = "No resource received!";

            return response()->json($response);
        }

        $type = $request->get('type');
        $id = $request->get('id');

        if(!$id) {
            $response['message'] = "No id received!";

            return response()->json($response);
        }

        $dir = $this->getDir($type, $id);

        $media_content = file_get_contents($resource);

        $input_path = $this->newTmp($media_content, $dir);
        $input_path = $this->renameImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_path = $this->removeWatermark($input_path);
        $input_path = $this->desaturateImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_name = basename($input_path);

        $response['data'] = route('show.file', [
            'type' => $type,
            'id' => $id,
            'name' => $input_name
        ]);
        $response['message'] = "OK";

        return response()->json($response);
    }

    public function uploadMedia1(Request $request){
        $response = [
            'data' => '',
            'message' => ""
        ];

        $resource = $request->get('file');

        if(!$resource) {
            $response['message'] = "No resource received!";

            return response()->json($response);
        }

        $type = $request->get('type');
        $id = $request->get('id');

        if(!$id) {
            $response['message'] = "No id received!";

            return response()->json($response);
        }

        $dir = $this->getDir($type, $id);

        $media_content = $resource;

        $input_path = $this->newTmp($media_content, $dir);
        $input_path = $this->renameImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_path = $this->removeWatermark($input_path);
        $input_path = $this->desaturateImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_name = basename($input_path);

        $response['data'] = route('show.file', [
            'type' => $type,
            'id' => $id,
            'name' => $input_name
        ]);
        $response['message'] = "OK";

        return response()->json($response);
    }

    public function uploadMedia2(Request $request){
        $response = [
            'data' => '',
            'message' => ""
        ];

        $resource = $request->get('file');

        if(!$resource) {
            $response['message'] = "No resource received!";

            return response()->json($response);
        }

        $type = $request->get('type');
        $id = $request->get('id');

        if(!$id) {
            $response['message'] = "No id received!";

            return response()->json($response);
        }

        $dir = $this->getDir($type, $id);

        $media_content = $resource;

        $input_path = $this->newTmp($media_content, $dir);
        $input_path = $this->renameImage($input_path);
        $input_path = $this->convertImage($input_path);
        $input_name = basename($input_path);

        $response['data'] = route('show.file', [
            'type' => $type,
            'id' => $id,
            'name' => $input_name
        ]);
        $response['message'] = "OK";

        return response()->json($response);
    }

    function convertImage($originalImage, $quality = 100)
    {
        $ext = exif_imagetype($originalImage);

        if ($ext === IMAGETYPE_JPEG) {
            $imageTmp=imagecreatefromjpeg($originalImage);
            $outputImage = str_replace('.jpeg', '.jpg', $originalImage);
        }
        else if ($ext === IMAGETYPE_PNG) {
            $imageTmp=imagecreatefrompng($originalImage);
            $outputImage = str_replace('.png', '.jpg', $originalImage);

        }
        else if ($ext === IMAGETYPE_GIF) {
            $imageTmp=imagecreatefromgif($originalImage);
            $outputImage = str_replace('.gif', '.jpg', $originalImage);

        }
        else if ($ext === IMAGETYPE_BMP) {
            $imageTmp=imagecreatefrombmp($originalImage);
            $outputImage = str_replace('.bmp', '.jpg', $originalImage);

        }
        else
            return $originalImage;

        // quality is a value from 0 (worst) to 100 (best)
        imagejpeg($imageTmp, $outputImage, $quality);
        imagedestroy($imageTmp);

        return $outputImage;
    }

    function removeWatermark($path){
        $data = file_get_contents($path);
        $base64 = base64_encode($data);

        $client = new Client();

        try{
            $response = $client->post('42.113.207.172:4747/remove/watermark', [
                RequestOptions::JSON => ['image' => $base64]
            ]);

            $res = json_decode($response->getBody()->getContents());
            $base64 = $res->result;

            file_put_contents($path, base64_decode($base64));
        }catch (\Exception $e){

        }

        return $path;
    }

    function desaturateImage($path){
        $src = imagecreatefromjpeg($path);
        imagecopymergegray($src, $src, 0, 0, 0, 0, imagesx($src), imagesy($src), 0);
        imagePNG($src, $path, 0);
        imagedestroy($src);

        return $path;
    }

    function renameImage($originalImage)
    {
        $image_type = getimagesize($originalImage)['mime'];

        if ($image_type === 'image/jpeg')
            $new_name = "$originalImage.jpg";
        else if ($image_type === 'image/png')
            $new_name = "$originalImage.png";
        else if ($image_type === 'image/gif')
            $new_name = "$originalImage.gif";
        else if ($image_type === 'image/bmp')
            $new_name = "$originalImage.bmp";
        else
            $new_name = $originalImage;

        rename($originalImage, $new_name);

        return $new_name;
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

        $filename = @tempnam($dir, 'Topkid');

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

    public function __destruct()
    {
        $media_path = storage_path('Media');
        chmod($media_path, 0775);
    }
}
