
<?php
function KMPSearch($pat, $txt)
{
    $ishasil = False;
    $M = strlen($pat);
    $N = strlen($txt);
    $lps=array_fill(0,$M,0);
    computeLPSArray($pat, $M, $lps);
  
    $i = 0; // index for txt[]
    $j = 0; // index for pat[]
    while ($i < $N) {
        if ($pat[$j] == $txt[$i]) {
            $j++;
            $i++;
        }
  
        if ($j == $M) {
            $ishasil = True;
            $j = $lps[$j - 1];
            return $ishasil;
        }
  
        else if ($i < $N && $pat[$j] != $txt[$i]) {
            if ($j != 0){
                $j = $lps[$j - 1];
            }else{
                $i = $i + 1;
            }
        }
    }
    return $ishasil;
}
  
// Fills lps[] for given patttern pat[0..M-1]
function computeLPSArray($pat, $M, &$lps)
{
    $len = 0;
  
    $lps[0] = 0;
    $i = 1;
    while ($i < $M) {
        if ($pat[$i] == $pat[$len]) {
            $len++;
            $lps[$i] = $len;
            $i++;
        }
        else 
        {
            if ($len != 0) {
                $len = $lps[$len - 1];
            }
            else
            {
                $lps[$i] = 0;
                $i++;
            }
        }
    }
}
?>