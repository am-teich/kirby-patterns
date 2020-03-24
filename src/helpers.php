<?php

function pattern($path, $data = [], $return = true) {
  
  $output = null;
  foreach (option('amteich.patterns.includepaths', []) as $subpath) {
    $pattern = new Kirby\Patterns\Pattern($subpath . '/' . $path, $data);
    if ($pattern->exists()) {
      $output = $pattern;
      break;
    }
  }
  if ($output == null) {
    throw new Exception("Pattern $path not found");
  }

  if($return === true) {
    return $output;
  } else {
    echo $output;
  }
}