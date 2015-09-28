<?php
$MAXLEN = 1024 * 1024; // (1MB)
$FILENAME = "treasure.enc";
$MD5 = 'frY/Ai5pAazldHimgixU';

if(!isset($_POST['msg'])) {
  http_response_code(400);
  die("Wrong parameter!");                          
}

if(strlen($_POST['msg']) < $MAXLEN ) {
  
   // validate msg towards MD5: the number value of the first two bytes of the
   // md5 sum is added to the first two bytes of the encoded message. the sum
   // modulo the length of the message determines the position in the received
   // message where the remainder of the MD5 string is expected. if it is found
   // there the message is considered as valid and is saved to the file system.
   $p = utf8_char_code_at($_POST['msg'], 0) *256 + 
          utf8_char_code_at($_POST['msg'], 1) + 
          utf8_char_code_at($MD5, 0) *256 + 
          utf8_char_code_at($MD5, 1);
    
   $pos = 2 + $p % ( strlen($_POST['msg']) - strlen($MD5) - 2 );
   
   $test = substr($_POST['msg'], $pos, strlen($MD5) - 2);   

   //echo 'p('.utf8_char_code_at($_POST['msg'], 0).' '.utf8_char_code_at($_POST['msg'], 1).' '.utf8_char_code_at($MD5, 0).' '.utf8_char_code_at($MD5, 1).'): '.$p. '=='.$pos.' test: '.$test;
   //echo 'strlenPost: '.  strlen($_POST['msg']) . ' strlenmd5: '.strlen($MD5);

   // only write, if partial test string (i.e. md5 except the first 2 characters) 
   // can be found at the expected position. 
   if(strcmp ($test, substr($MD5, 2)) == 0 )
     file_put_contents($FILENAME, $_POST['msg']);
   else {
      sleep(5);
      http_response_code(400);
      die("Bad Request!");
   }
} else {
  http_response_code(400);
  die("Maxlength exceeded!");
}

// -----------------------------------------------------------------------------
function utf8_char_code_at($str, $index)
{
    $char = mb_substr($str, $index, 1, 'UTF-8');

    if (mb_check_encoding($char, 'UTF-8')) {
        $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
        return hexdec(bin2hex($ret));
    } else {
        return null;
    }
}


?>