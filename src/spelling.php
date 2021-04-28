<?php
include('levensthein.php');
// Konversi UTF-8 string ke single-byte string supaya pemrosesan lebih cepat
function utf8_to_extended_ascii($str, &$map)
{
    // find all multibyte characters (cf. utf-8 encoding specs)
    $matches = array();
    if (!preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)){
        return $str; // plain ascii string
    }
   
    // update the encoding map with the characters not already met
    foreach ($matches[0] as $mbc){
        if (!isset($map[$mbc])){
            $map[$mbc] = chr(128 + count($map));
        }
    }
    // finally remap non-ascii characters
    return strtr($str, $map);
}

function levenshtein_utf8($s1, $s2)
{
    $charMap = array();
    $s1 = utf8_to_extended_ascii($s1, $charMap);
    $s2 = utf8_to_extended_ascii($s2, $charMap);
    
    //Gunakan fungsi dari file sebelah
    return levdistance($s1, $s2);
}

function autochecker($input, $words){
    // Inisiasi nilai s
    $shortest = -1;

    // loop through words to find the closest
    foreach ($words as $word) {

        // calculate the distance between the input word,
        // and the current word
        $lev = levenshtein_utf8($input, $word);
        
        //echo $input." dan ".$word." dengan lev = ".$lev."\n";

        // check for an exact match
        if ($lev == 0) {

            // closest word is this one (exact match)
            $closest = $word;
            $shortest = 0;

            // break out of the loop; we've found an exact match
            break;
        }

        /*$kmp = KMPSearch($input, $word);
        // check for an exact match
        if ($kmp) {

            // closest word is this one (exact match)
            $closest = $word;
            $shortest = 0;

            // break out of the loop; we've found an exact match
            break;
        }*/

        // if this distance is less than the next found shortest
        // distance, OR if a next shortest word has not yet been found
        if ($lev <= $shortest || $shortest < 0) {
            // set the closest match, and shortest distance
            $closest  = $word;
            $shortest = $lev;
        }
    }
    return [$closest,$shortest];

}

function pembersihan($input,$words){
    $perubahan = 0;
    $arrayhasil = array();
    $arrayinput = preg_split("/[\s,]+/", $input);
    foreach ($arrayinput as $kata){
        //hasil[0] = kata terdekatnya
        //hasil[1] = nilai lev terpendeknya
        $hasil = autochecker($kata, $words);
        //print_r($hasil);
        //Kemiripan string diatas 75%
        if ($kata!=$hasil[0] && ($hasil[1]/strlen($hasil[0]))<=0.25){
            $arrayhasil += array($kata => $hasil[0]);
            $perubahan += 1;
        }
    }
    if($perubahan == 0){
        return [$input, True];
    }else{
        // create array of regex using array keys
        $rearr = array_map(function($k) { return '/'.$k .'/'; },
        array_keys($arrayhasil));

        # pass 2 arrays to preg_replace
        $inputbaru = preg_replace($rearr, $arrayhasil, $input) . "\n";
        return ["Mungkin Maksud Kamu : " . $inputbaru, False];
    }
}

?>