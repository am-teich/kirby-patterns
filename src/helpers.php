<?php

function pattern($path, $data = [], $return = true) {
  if (option('mgfagency.patterns.includepath') != "") {
    $path = option('mgfagency.patterns.includepath') . '/' . $path;
  }
  
  $output = new Kirby\Patterns\Pattern($path, $data); 

  if($return === true) {
    return $output;
  } else {
    echo $output;
  }
}