<head>
<p align="center">
<img border="0" src="http://profile.ak.fbcdn.net/hprofile-ak-snc4/370365_100003795511051_561161049_n.jpg"</p>
<link href="http://dz48-coders.org/indexi/pic/favicon.ico" type="image/x-icon" rel="shortcut icon" />
<title>ShELL FoUnDeR By Damane2011</title>
<style>
body{background-color:#111;color:#00ff00;}
body,td,th{ font: 8pt Lucida,Tahoma;margin:0;vertical-align:top;color:#00ff00; }
table.info{ color:#000;background-color:#222; }
span,h1,a{ color: $color !important; }
span{ font-weight: bolder; }
h1{ border-left:7px solid $color;padding: 3px 5px;font: 14pt Verdana;background-color:#333;margin:0px; }
div.content{ padding: 5px;margin-left:5px;background-color:#222; }
a{ text-decoration:none; }
a:hover{ text-decoration:underline; }
.ml1{ border:1px solid #555;padding:5px;margin:0;overflow: auto; }
.bigarea{ width:100%;height:300px; }
input,textarea,select{ margin:0;color:#999;background-color:#222;border:1px solid $color; font: 8pt Tahoma,'Tahoma'; }
form{ margin:0px; }
#toolsTbl{ text-align:center; }
.toolsInp{ width: 300px }
.main th{text-align:left;background-color:#5e5e5e;}
.main tr:hover{background-color:#5e5e5e}
.l1{background-color:#444}
.l2{background-color:#333}
pre{font-family:Courier,Monospace;}
.found {
color: #008000;
font-weight: bold;
}
.damane {
color: #FFFF00;
font-weight: bold;
}
.scan {
color: #A52A2A;
font-weight: bold;
}
.start {
color: #0000FF;
font-weight: bold;
}
// -->
</style>
</head>

<body>

<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center"><font color="#8D38C9" size="5">ShELL FoUnDeR</font></p><br>
<form action="" method="post">
<p align="center">
<input name="traget" type="text" size="30" value="http://www.domain.tld/"/><br>
<br><br>
<input name="scan" size="80" value="ScAn ThIs" type="submit">
</form>

<?php

/*
 
File Writed By Damane2011
 
Email: Damane-Dz@hotmail.com
 
Twitter: @DamaneDz
 
*/

set_time_limit(0);

if (isset($_POST["scan"])) {

$url = $_POST['traget'];

echo "<br /><span class='start'>Scanning ".$url."<br /><br /></span>";

echo "ReSulT:<br /><br />";

//Shell List You Can Add What U Want :P

$shells = array("WSO.php","dz.php","cpanel.php","cpn.php","sql.php","mysql.php","madspot.php",
"Cgishell.pl","killer.php","changeall.php","2.php","Sh3ll.php","dz0.php","dam.php","user.php","dom.php","whmcs.php",
"vb.zip","r00t.php","c99.php","gaza.php","1.php","wp.zip"."wp-content/plugins/disqus-comment-system/disqus.php",
"d0mains.php","wp-content/plugins/akismet/akismet.php","madspotshell.php","Sym.php","c22.php","c100.php",
"wp-content/plugins/akismet/admin.php#","wp-content/plugins/google-sitemap-generator/sitemap-core.php#",
"wp-content/plugins/akismet/widget.php#","Cpanel.php","zone-h.php","tmp/user.php","tmp/Sym.php","cp.php",
"tmp/madspotshell.php","tmp/root.php","tmp/whmcs.php","tmp/index.php","tmp/2.php","tmp/dz.php","tmp/cpn.php",
"tmp/changeall.php","tmp/Cgishell.pl","tmp/sql.php","tmp/admin.php","cliente/downloads/h4xor.php",
"whmcs/downloads/dz.php","L3b.php","d.php","tmp/d.php","tmp/L3b.php","wp-content/plugins/akismet/admin.php",
"templates/rhuk_milkyway/index.php","templates/beez/index.php","admin1.php","upload.php","up.php","vb.zip","vb.rar",
"admin2.asp","uploads.php","sa.php","sysadmins/","admin1/","administration/Sym.php","images/Sym.php",
"/r57.php","/wp-content/plugins/disqus-comment-system/disqus.php","/shell.php","/sa.php","/admin.php",
"/sa2.php","/2.php","/gaza.php","/up.php","/upload.php","/uploads.php","/templates/beez/index.php","shell.php","/amad.php",
"/t00.php","/dz.php","/site.rar","/Black.php","/site.tar.gz","/home.zip","/home.rar","/home.tar","/home.tar.gz",
"/forum.zip","/forum.rar","/forum.tar","/forum.tar.gz","/test.txt","/ftp.txt","/user.txt","/site.txt",
"/cpanel","/awstats","/site.sql","/vb.sql","/forum.sql","/backup.sql","/back.sql","/data.sql","wp.rar/",
"wp-content/plugins/disqus-comment-system/disqus.php","asp.aspx","/templates/beez/index.php","tmp/vaga.php",
"tmp/killer.php","whmcs.php","tmp/killer.php","tmp/domaine.pl","tmp/domaine.php","useradmin/",
"tmp/d0maine.php","d0maine.php","tmp/sql.php","tmp/dz1.php","dz1.php","forum.zip","Symlink.php","Symlink.pl", 
"forum.rar","joomla.zip","joomla.rar","wp.php","buck.sql","sysadmin.php","images/c99.php", "xd.php", "c100.php",
"spy.aspx","xd.php","tmp/xd.php","sym/root/home/","billing/killer.php","tmp/upload.php","tmp/admin.php",
"Server.php","tmp/uploads.php","tmp/up.php","Server/","wp-admin/c99.php","tmp/priv8.php","priv8.php","cgi.pl/", 
"tmp/cgi.pl","downloads/dom.php","templates/ja-helio-farsi/index.php","webadmin.html","admins.php",
"/wp-content/plugins/count-per-day/js/yc/d00.php", "admins/","admins.asp","admins.php","wp.zip");

//Start Scan
foreach ($shells as $shell){
$headers = get_headers("$url$shell"); 

if (eregi('200', $headers[0])) {
//Result
    echo "<a href='$url$shell'>$url$shell</a> <span class='found'>Founded!</span><br /><br/><br/>"; //By Damane2011
	$dz = fopen('shells.txt', 'a+');
	$suck = "$url$shell";
	fwrite($dz, $suck."\n");
}
}
//Result In Text File (shells.txt)
echo "<span class='damane'>Click Here to See Shells Founded On a txt File [ <a href='./shells.txt' target='_blank'>shells.txt</a> ]</span>";
}
?></center>
<center><p align="center">&nbsp;</p>
<p align="center">&nbsp;</p>
<p align="center"><font color="#F6358A" size="4">By Damane2011</font><br><br>
MaDe in AlGeria 2012 &copy</p>
<p></center>
</body>

</html>
