<?php

namespace App\Helpers;
use DB;

class RatesComments
{
    public static function setRate($id, $sign, $user_id){
        $basefolder = storage_path().'\app\rate\\';

        DB::setFetchMode(\PDO::FETCH_ASSOC);
        $result = DB::table('comments')
            ->select('comments.created_at','comments.rate','comments.user_id')
            ->where('published', '=', '1')
            ->where('id', '=', $id)->first();
        if($result)
        if($result['user_id'] == $user_id) die('true');

        if($result == null) die('true');

        $folder = ((int)$id)/10000;
        if($folder < 1) $folder=1;
        $folder = $folder*10000;

        if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700);
        $folder.= '\\'.date("y", strtotime($result['created_at']));
        if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700);

        $fpath = $basefolder.'\\'.$folder.'\\'.$id;

        if($fdata = @file_get_contents($fpath)){ //echo '111'.$result['created_at'];
            if(!RatesComments::setRateStatus($id, $sign, $user_id, $result['created_at'])) dd("RatesComments UPDATE FAIL!");
            $fdata=explode('|', $fdata); // $fdata[] = $data['user_id']; $fdata[] = $data['sign']; $fdata=implode('|', $fdata); file_put_contents($basefolder.'\\'.$folder.'\\'.$data['id'], $fdata);

            $i=0; $next=0;
            foreach($fdata as $key => &$item) {
                //if($i==0){ $sum = $item; $i++; continue; }
                if($next) {
                    if($item == $sign) die('true'); else {
                        $item = $sign;
                        unset($fdata[$key]);
                        if($sign==='+') $result['rate']++; else $result['rate']--;
                        $result = DB::table('comments')
                            ->where('id', $id)
                            ->update(['rate' => $result['rate']]);
                        if(!$result) dd("UPDATE FAIL!");
                        if(empty($fdata)) { unlink($fpath); die('true'); }
                        $fdata = implode('|', $fdata); file_put_contents($fpath, $fdata); die('true');
                    }
                }
                if($item == $user_id){ unset($fdata[$key]); $next = 1;}
                $i++;
            }

        } else {
            if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700); // echo $basefolder.'\\'.$folder;
            if($sign === '+') $rate = 1; else $rate = -1;
            $fdata=$user_id.'|'.$sign;
            file_put_contents($fpath, $fdata);
            $updateResult = DB::table('comments')
                ->where('id', $id)
                ->update(['rate' => $rate]); if(!$updateResult) die ("Update fail!");
            if(RatesComments::setRateStatus($id, $sign, $user_id, $result['created_at'])) die('true'); else die('FAIL!');
        }
    }

    public static function setRateStatus($id, $sign, $user_id, $created_at){
        $basefolder = storage_path().'\app\users_info';

        //dd($result);
        //echo date("Y", $result[0]); //echo date("Y", mktime($result[0]));
        $folder = ((int)$user_id)/10000;
        if($folder < 1) $folder=1;
        $folder = $folder*10000; //$folder.= '\\'.$data['user_id'].'\\'.date("y", strtotime($result[0]));

        if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700);

        $folder.= '\\'.$user_id;
        if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700);

        $folder.= '\\'.date("y", strtotime($created_at));

        $fpath = $basefolder.'\\'.$folder; //$fpath = $basefolder.'\\'.$folder.'\\'.$data['id'];

        //echo $created_at, ' ', $fpath;

        if($fdata = @file_get_contents($fpath)){
            $fdata = json_decode($fdata, true);
            if(!isset($fdata[$id])){ $fdata[$id]=$sign; file_put_contents($fpath, json_encode($fdata)); return true; }
            if($fdata[$id] == $sign) return true; else {
                unset($fdata[$id]);
                if(empty($fdata)) unlink($fpath); else file_put_contents($fpath, json_encode($fdata));
            }

            return true;

        } else {
            //$fata=[];
            $fdata[$id] = $sign;
            file_put_contents($fpath, json_encode($fdata));
        }

        return true;
    }

    public static function getRateStatus($commentInfo, $user_id){

        $basefolder = storage_path().'\app\users_info';
        $folder = ((int)$commentInfo['user_id'])/10000;
        if($folder < 1) $folder=1;
        $folder = $folder*10000; //$folder.= '\\'.$data['user_id'].'\\'.date("y", strtotime($result[0]));
        if(!is_dir($basefolder.'\\'.$folder)) return 0;
        //if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700);
        $folder.= '\\'.$user_id;
        //if(!is_dir($basefolder.'\\'.$folder)) mkdir($basefolder.'\\'.$folder, 0700);
        if(!is_dir($basefolder.'\\'.$folder)) return 0;

        $folder.= '\\'.date("y", strtotime($commentInfo['created_at']));

        $fpath = $basefolder.'\\'.$folder;  //$fpath = $basefolder.'\\'.$folder.'\\'.$data['id'];
        //echo $fpath.'<br>';
        if($fdata = @file_get_contents($fpath)){ //echo $fpath.'<br>';
            $fdata = json_decode($fdata, true); // print_r($fdata);

            // echo $commentInfo['id'].' == '.$fdata[$commentInfo['id']]."<p>";
            if(isset($fdata[$commentInfo['id']])) return $fdata[$commentInfo['id']]; else return 0;
        } else return 0;

        return false;
    }
}