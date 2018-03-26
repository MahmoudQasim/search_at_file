<?php
$dir = "files";
$opd = opendir($dir);
$rd = readdir($opd);
$word = $_POST["word"];
function read_file_docx($filename){

    $striped_content = '';

    $content = '';

    if(!$filename || !file_exists($filename)) return false;

    $zip = zip_open($filename);

    if (!$zip || is_numeric($zip)) return false;

    while ($zip_entry = zip_read($zip)) {

        if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

        if (zip_entry_name($zip_entry) != "word/document.xml") continue;

        $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

        zip_entry_close($zip_entry);

    }// end while

    zip_close($zip);

    $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);

    $content = str_replace('</w:r></w:p>', "\r\n", $content);

    $striped_content = strip_tags($content);

    return $striped_content;

}

function search_word($file , $word)
{
    $ext = explode(".",$file);
    $FULLTEXT = '';
    if($ext[1] == "docx")
    {
        $FULLTEXT = read_file_docx($file);
    }
    else{
        $FULLTEXT = file_get_contents($file , true);
    }
    $x = 0;
    $y=0;
    $eq = false;
    $FULLTEXT = substr($FULLTEXT , strpos($FULLTEXT ,$word),strlen($word));
    if($word == $FULLTEXT){
        $eq=true;
    }
    return $eq;
}
$true = 0;
while (false !== ($entry = readdir($opd))) {
    if(!is_dir($dir."/".$entry)) {
        if (search_word($dir . "/" . $entry, $word)) {
            echo $dir . "/" . $entry . "<br>";
            $true ++;
        }
    }
}
if($true > 0)
{
    echo  "We founded " . $true . " file .";
}else{
    echo "Can't find any file with (<font style='background: yellow'>".$word."</font>) word ...";
}