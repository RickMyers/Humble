<?php
//------------------------------------------------------------------------------
//Include things you want to do after to application beginning
$models             = [];
$view               = false;
$views              = [];
$chainActions       = [];
$chainControllers   = [];
$abort              = false;
$ajaxUpload         = false;
//################### REMOVE BEFORE GOING TO PRODUCTION ########################
//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
ob_start();
//##############################################################################
function isInteger($i) {
    return ($i == (string)(int)$i);
}
function isfloat($f) {
    return ($f == (string)(float)$f);
}
function isBoolean($b) {
    return (($b===1) || ($b===0) || ($b==="1") || ($b==="0") || (strtolower($b)==='true') || (strtolower($b)==='false'));
}
//------------------------------------------------------------------------------
//
//The following routine detects if there has been an AJAX based file upload and
// then processes the raw input into the global post variable
//
//------------------------------------------------------------------------------
function parseAjaxUpload() {
    $_POST      = [];                   //reset post
    $data       = file_get_contents("php://input");
    $separator  = "\r\n";               //delimits a header row
    $section    = "\r\n\r\n";           //delimits the header/content section
    $boundary   = substr($data,0,strpos($data,$separator));
    $vars       = [];
    $files      = [];
    $upDir      = ini_get('upload_tmp_dir');
    $content    = explode($boundary,$data);
    foreach ($content as $stuff => $data) {
        if (($data == "--\r\n") || (str_replace("\r\n","",$data) == '')) {
            continue;
        }
        $sepPos = strpos($data,$section);
        $hdr    = str_replace($separator," ",substr($data,0,$sepPos));
        $cnt    = substr($data,$sepPos+4);
        $cnt    = substr($cnt,0,strlen($cnt)-2);
        if (strpos($hdr,'filename=')===false) {
            $name = substr($hdr,strpos($hdr,'name="')+6);
            $vars[substr($name,0,strpos($name,'"'))]=urldecode($cnt);
        } else {
            $tmpfile    = tempnam($upDir,'tmp');
            $filename   = substr($hdr,strpos($hdr,'filename="')+10);
            $filename   = substr($filename,0,strpos($filename,'"'));
            $name       = substr($hdr,strpos($hdr,'name="')+6);
            $fileKey    = substr($name,0,strpos($name,'"'));
            $type       = substr($hdr,strpos($hdr,'Content-Type: ')+14);
            if (isset($files[$fileKey])) {
                if (gettype($files[$fileKey]['tmp_name'])=='string') {
                    $files[$fileKey]['tmp_name'] = array($files[$fileKey]['tmp_name']);
                    $files[$fileKey]['name']     = array($files[$fileKey]['name']);
                    $files[$fileKey]['type']     = array($files[$fileKey]['type']);
                    $files[$fileKey]['size']     = array($files[$fileKey]['size']);
                    $files[$fileKey]['error']    = array($files[$fileKey]['error']);
                }
                $files[$fileKey]['tmp_name'][]   = $tmpfile;
                $files[$fileKey]['name'][]       = $filename;
                $files[$fileKey]['type'][]       = $type;
                $files[$fileKey]['size'][]       = strlen($cnt);
                $files[$fileKey]['error'][]      = UPLOAD_ERR_OK;
            } else {
                $files[$fileKey] = array('tmp_name'=>$tmpfile,'name'=>$filename,'size'=>strlen($cnt),'type'=>$type,'error'=>UPLOAD_ERR_OK);
            }
            file_put_contents($tmpfile,$cnt);
        }
    }
    foreach ($vars as $var => $value) {
        $_REQUEST[$var] = $_POST[$var] = $value;   //rebuilds the global post/request variable
    }
    foreach ($files as $key => $data) {
        $_FILES[$key] = $data;
    }

}
//------------------------------------------------------------------------------
//Detects if what has been posted is a raw feed of mulipart data
//------------------------------------------------------------------------------
foreach ($_POST as $var => $value) {
    if (strpos($var,'Content-Disposition:')!==false) {
        parseAjaxUpload();
        $ajaxUpload = true;
    }
    break;
}
?>