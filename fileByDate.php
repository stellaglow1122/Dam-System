<?php
$dir_name ="../rec/";            
$dir = opendir($dir_name);
$file_list = array();             //ファイル名を保存する配列
$time_list = array();           //ファイルの日付を保存する配列
while (false !== ($file = readdir($dir))){  //ディレクトリ走査
    //先頭文字が"." のファイルを除外
    if($file[0] != "."){
        $time_list[] = filemtime($dir_name.$file);    //ソート用にファイル時刻を取得
        $file_list[] = $file;   //ファイル名を取得
    }
}
closedir($dir);
array_multisort($time_list,SORT_DESC,$file_list);         //時刻でソート
print_r($file_list);

?>