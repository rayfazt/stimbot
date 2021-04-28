<?php
declare(strict_types=1);

function levdistance(string $str1, string $str2){
    //inisiasi cost untuk insert
    $costIns = 1.0;
    //inisiasi cost untum replace
    $costRep = 1.0;
    //inisiasi cost untuk delete
    $costDel = 1.0;
    //matrix awal
    $matrix = [];
    //string awal
    $str1Array = multiByteStringToArray($str1);
    //string target
    $str2Array = multiByteStringToArray($str2);
    //panjang string 1
    $str1Length = count($str1Array);
    //panjang string 2
    $str2Length = count($str2Array);

    //inisiasi baris ke matriksnya
    $row = [];
    $row[0] = 0.0;
    for ($j = 1; $j < $str2Length + 1; $j++) {
        $row[$j] = $j * $costIns;
    }
    $matrix[0] = $row;
    for ($i = 0; $i < $str1Length; $i++) {
        $row = [];
        $row[0] = ($i + 1) * $costDel;
        for ($j = 0; $j < $str2Length; $j++) {
            //menemukan cost yang minimal antara insert, delete, atau replace
            $row[$j + 1] = min(
                //Dynamic Programming dilakukan (optimasi k, k+1)
                $matrix[$i][$j + 1] + $costDel,
                $row[$j] + $costIns,
                $matrix[$i][$j] + ($str1Array[$i] === $str2Array[$j] ? 0.0 : $costRep)
            );
        }
        $matrix[$i + 1] = $row;
    }
    //return levensthein distancenya
    return $matrix[$str1Length][$str2Length];
}

function multiByteStringToArray(string $str)
{
    $length = mb_strlen($str);
    $array = [];
    for ($i = 0; $i < $length; $i++) {
        $array[$i] = mb_substr($str, $i, 1);
    }
    return $array;
}