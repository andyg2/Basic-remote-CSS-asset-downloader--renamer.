<?php

// Paths: Relative local path with trailing slash
define('FONTS_DIR', './fonts/');
define('ICONS_DIR', './icons/');
define('IMAGES_DIR', './img/');


/**
 * Usage: php  .\parse.php remote_css_url [strings,to,replace replacement]/[strings,to,replace replacement,string,sequence]
 * 
 * arg-1: (required) URL of CSS
 * arg-2: (otional) filename text search
 * arg-3: (otional / require if arg-2 is present) filename text replacement
 * 
 * Minimum
 * Example: php .\parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css
 * 
 * Rename downloaded files from *sourcesanspro*.* to *dgtepro*.*
 * Example: php .\parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcesanspro dgtepro
 * 
 * Rename downloaded files from *sourcesanspro*.* to *sanspro*.* and *sourcecodepro*.* to *codepro*.*
 * Example: php .\parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcesanspro,sourcecodepro sanspro,codepro
 * 
 * REmove sourcesanspro and sanspro from all filenames
 * Example: php .\parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcesanspro,sourcecodepro
 * 
 */


if (isset($argv[1]) && !empty($argv[1])) {
  if (filter_var($argv[1], FILTER_VALIDATE_URL)) {

    $css_url = $argv[1];
    $filename['find'] = isset($argv[2]) ? explode(',', $argv[2]) : '';   // find string in firename and 
    $filename['replace'] = isset($argv[3]) ? explode(',', $argv[3]) : '';   // replace with this

    $url_parts = parse_url($css_url);

    $css_filename = basename($argv[1], '.css');

    $pos = strrpos($url_parts['path'], $css_filename);

    $url_parts['path'] = substr($url_parts['path'], 0, $pos);

    $basepath = $url_parts['scheme'] . '://' . $url_parts['host'];
    $relbase = $basepath . $url_parts['path'];


    if (!empty($css_filename)) {
      $css_filename .= '.css';
      $file_contents = file_get_contents($css_url);

      $pattern = '/url\(([^)]*)\)/';
      preg_match_all($pattern, $file_contents, $matches);
      // print_r($matches[1]);
      // exit;
      $r = [];
      if (isset($matches[1]) && !empty($matches[1])) {
        // print_r($matches[1]);
        $r['rewrites'] = [];
        $r['urls'] = [];
        foreach ($matches[1] as $uri) {
          $basename = basename($uri);
          $basename = explode('#', $basename)[0];
          $basename = explode('?', $basename)[0];
          $ex = explode('.', $basename);
          if (count($ex) == 1) { //presume an image url, jpg for now
            $basename .= '.jpg';
            $ext = 'jpg';
          } else {
            $ext = end($ex);
          }
          $four = substr($uri, 0, 4);

          // print_r($four);
          // Ignore data:image urls
          if ($four != 'data') {

            // figure out where to put this locally

            switch (strtolower($ext)) {
              case 'woff':
              case 'woff2':
              case 'eot':
              case 'ttf':
                $subdir = FONTS_DIR;
                break;

              case 'svg':
                $subdir = ICONS_DIR;
                break;

              case 'png':
              case 'jpg':
              case 'jpeg':
              case 'gif':
              case 'webp':
                $subdir = IMAGES_DIR;
                break;


              default:
                $subdir = './';
                break;
            }


            if ($subdir != './') {
              if (!is_dir($subdir)) {
                mkdir($subdir, 0755, true);
              }
            }

            // rewrite any filenames
            $basename = str_replace($filename['find'], $filename['replace'], $basename);

            // concat local path
            $local_filepath = $subdir . $basename;


            if ($four != 'http') { // not a url
              if (substr($four, 0, 1) == '/') {
                // root based  url
                $r['urls'][$local_filepath] = $basepath . $uri;
              } else {
                // relative url
                $r['urls'][$local_filepath] = $relbase . $uri;
              }
            } else {
              // full url
              $r['urls'][$local_filepath] = $uri;
            }

            $r['rewrites'][$local_filepath] = $uri;
          }
        }
      }

      $r['urls'] = array_unique($r['urls']);
      foreach ($r['urls'] as $basename => $url) {

        $subfile_contents = file_get_contents($url);
        if (!empty($subfile_contents)) {
          echo 'write ' . $basename . "\n";
          file_put_contents($basename, $subfile_contents);
        }
      }
      foreach ($r['rewrites'] as $replace => $find) {
        echo 'replace ' . $find . ' > ' . $replace . "\n";
        $file_contents = str_replace($find, $replace, $file_contents);
      }

      // print_r($r);

      if (!empty($file_contents)) {
        echo 'write ' . $css_filename . "\n";
        file_put_contents($css_filename, $file_contents);
      }
    }
  }
}
