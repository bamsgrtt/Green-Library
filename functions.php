<?php
function getData($file){
    if(!file_exists($file)) file_put_contents($file,"");
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach($lines as $line){
        $data[] = json_decode($line,true);
    }
    return $data;
}

function saveData($file,$data){
    $lines = [];
    foreach($data as $d){
        $lines[] = json_encode($d);
    }
    file_put_contents($file, implode("\n",$lines));
}

function getNextId($data){
    $ids = array_column($data,'id');
    return $ids ? max($ids)+1 : 1;
}

// Fungsi cari data berdasarkan ID
function findById($data,$id){
    foreach($data as $d){
        if($d['id']==$id) return $d;
    }
    return null;
}
?>
