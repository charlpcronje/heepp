<?php

$Xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
            <mainNode>
		<node1>
                    <FirstName>First Name1</FirstName>
                    <LastName>Last Name1</LastName>
                    <Misc>
                        <sample1>A1</sample1>
                        <sample2>A2</sample2>
                    </Misc>
		</node1>
		<node2>
                    <FirstName>First Name2</FirstName>
                    <LastName>Last Name2</LastName>
                    <Misc>
                        <sample1>B1</sample1>
                        <sample2>B2</sample2>
                    </Misc>
		</node2>
		<node3>
                    <FirstName>First Name3</FirstName>
                    <LastName>Last Name3</LastName>
                    <Misc>
                        <sample1>C1</sample1>
                        <sample2>C2</sample2>
                    </Misc>
		</node3>
            </mainNode>';

$sampleArray = array(
    'node1' => array("abc" => 1234),
    'node2' => array("xyz" => 9876)
);

$sampleJson = '{"menu": {
  "id": "file",
  "value": "File",
  "popup": {
    "menuitem": [
      {"value": "New", "onclick": "CreateNewDoc()"},
      {"value": "Open", "onclick": "OpenDoc()"},
      {"value": "Close", "onclick": "CloseDoc()"}
    ]
  }
}}';

$sampleString = "!#=#!node1#*#FirstName#*#First Name1!#=#!!#=#!node1#*#LastName#*#Last Name1!#=#!!#=#!node1#*#Misc#*#sample1#*#A1!#=#!!#=#!node1#*#Misc#*#sample2#*#A2!#=#!node2#*#FirstName#*#First Name2!#=#!node2#*#LastName#*#Last Name2!#=#!node2#*#Misc#*#sample1#*#B1!#=#!node2#*#Misc#*#sample2#*#B2!#=#!node3#*#FirstName#*#First Name3!#=#!node3#*#LastName#*#Last Name3!#=#!node3#*#Misc#*#sample1#*#C1!#=#!node3#*#Misc#*#sample2#*#C2!#=#!";

//Create class object
require_once("PhpJsonXmlArrayStringChanger.php");
$object = new PhpJsonXmlArrayStringChanger();


//XML to Array
$array = $object->convertXmltoArray($Xml);
if ($array === false) {
    //$object->displayErrorLog();
    $object->displayLastError();
} else {
    echo "<pre>";
    print_r($array);
    exit;
}
/*
  // Array to xml
  $xml=$object->convertArrayToXML($sampleArray);
  if($xml===false){
  //$object->displayErrorLog();
  $object->displayLastError();
  }
  else{
  header("content-type: text/xml");
  echo $xml;
  exit;
  }
 */
/*
  // JSON to xml
  $xml=$object->convertJsonToXML($sampleJson);
  if($xml===false){
  //$object->displayErrorLog();
  $object->displayLastError();
  }
  else{
  header("content-type: text/xml");
  echo $xml;
  exit;
  }
 */

/*
  // JSON to array
  $array=$object->convertJsonToArray($sampleJson);
  if($array===false){
  //$object->displayErrorLog();
  $object->displayLastError();
  }
  else{
  echo "<pre>";
  print_r($array);
  exit;
  }
 */
/*
  //XML to String
  $string=$object->convertXmltoString($Xml);
  if($string===false){
  //$object->displayErrorLog();
  $object->displayLastError();
  }
  else{
  echo $string;exit;
  }
 */
/*
  //String to Array
  $array=$object->convertStringToArray($sampleString);
  if($array===false){
  //$object->displayErrorLog();
  $object->displayLastError();
  }
  else{
  echo "<pre>";
  print_r($array);
  }
 */
?>