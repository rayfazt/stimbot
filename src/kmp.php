
<?php
// PHP program for implementation of KMP pattern searching
// algorithm
  
  
// Prints occurrences of txt[] in pat[]
function KMPSearch($pat, $txt)
{
    $M = strlen($pat);
    $N = strlen($txt);
  
    // create lps[] that will hold the longest prefix suffix
    // values for pattern
    $lps=array_fill(0,$M,0);
  
    // Preprocess the pattern (calculate lps[] array)
    computeLPSArray($pat, $M, $lps);
  
    $i = 0; // index for txt[]
    $j = 0; // index for pat[]
    while ($i < $N) {
        if ($pat[$j] == $txt[$i]) {
            $j++;
            $i++;
        }
  
        if ($j == $M) {
            printf("Found pattern at index ".($i - $j));
            $j = $lps[$j - 1];
        }
  
        // mismatch after j matches
        else if ($i < $N && $pat[$j] != $txt[$i]) {
            // Do not match lps[0..lps[j-1]] characters,
            // they will match anyway
            if ($j != 0)
                $j = $lps[$j - 1];
            else
                $i = $i + 1;
        }
    }
}
  
// Fills lps[] for given patttern pat[0..M-1]
function computeLPSArray($pat, $M, &$lps)
{
    // length of the previous longest prefix suffix
    $len = 0;
  
    $lps[0] = 0; // lps[0] is always 0
  
    // the loop calculates lps[i] for i = 1 to M-1
    $i = 1;
    while ($i < $M) {
        if ($pat[$i] == $pat[$len]) {
            $len++;
            $lps[$i] = $len;
            $i++;
        }
        else // (pat[i] != pat[len])
        {
            // This is tricky. Consider the example.
            // AAACAAAA and i = 7. The idea is similar
            // to search step.
            if ($len != 0) {
                $len = $lps[$len - 1];
  
                // Also, note that we do not increment
                // i here
            }
            else // if (len == 0)
            {
                $lps[$i] = 0;
                $i++;
            }
        }
    }
}
  
// Driver program to test above function
  
    $txt = "ABABDABACDABABCABAB";
    $pat = "ABABCABAB";
    KMPSearch($pat, $txt);
      
?>