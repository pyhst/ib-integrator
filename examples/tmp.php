<?php

function writeln(string $string)
{
	echo $string . "\n";
}

$finfo = new SplFileInfo(sys_get_temp_dir());

writeln("directory is " . $finfo->getRealPath());
writeln("is it really a directory? " . ($finfo->isDir() ? 'yes' : 'no'));
writeln("it belongs to group? " . $finfo->getGroup());
writeln("it belongs to owner? " . $finfo->getOwner());
writeln("permissions are? " . ($finfo->getPerms() ? sprintf('%o', $finfo->getPerms()) : 'no info'));
writeln("can I write to it? " . (file_put_contents($finfo->getRealPath() . '/tmpfile', 'Hello world') ? 'yes' : 'no'
));


$ch = curl_init('http://www.google.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FILE, fopen('php://temp', 'w+'));
$result = curl_exec($ch);
if (curl_errno($ch)) {
	writeln("curl failed : " . curl_error($ch));
} else {
	writeln("curl success");
}

// gave the following output
// directory is /tmp
// is it really a directory? yes
// it belongs to group? 0
// it belongs to owner? 0
// permissions are? 40777
// can I write to it? yes
// curl failed : curl_setopt_array(): Unable to create temporary file.