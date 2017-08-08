<?php

namespace Kirby\Plugins\CopyPages;

use Response;
use Dir;

if (!function_exists('panel')) return;

function stripDotSegments($path) {
  return preg_replace('#(^|/)\.{1,}/#', '/', $path);
}

// Load widget
kirby()->set('widget', 'copy-pages', __DIR__ . DS . 'widgets' . DS . 'copy-pages');

// Add routes
panel()->routes([[
  'pattern' => 'copy-pages/api/copy',
  'method' => 'POST',
  'action' => function() {
    $user = site()->user()->current();
    if (!$user || (!$user->hasPermission('panel.page.create') && !$user->isAdmin())) {
      return Response::error("Keine Berechtigung");
    }

    $sourceUrl = stripDotSegments(get('source'));
    $destUrl = stripDotSegments(get('dest'));

    $source = page($sourceUrl);
    if ($source) {
      $sourceUrl = $source->diruri();
      $sourceUid = $source->uid();
    }

    if ($destUrl == "/") {
      $dest = site();
      $uri = DS;
      $diruri = DS;
    }
    else {
      $dest = page($destUrl);
      $uri = $dest->uri() . DS;
      $diruri = $dest->diruri() . DS;
    }
    if ($dest) {
      if (get('uid')) {
        $destUid = get('uid');
      }
      else {
        $destUid = $sourceUid;
      }
      $destUri = $uri . $destUid;
      $destUrl = $diruri . $destUid;
    }
    
    $sourcePath = kirby()->roots->content() . DS . $sourceUrl;
    $destPath = kirby()->roots->content() . DS . $destUrl;
        
    $destPathBefore = substr($destPath, 0,strrpos($destPath, '/'));
    $destPathAfter = substr($destPath, strrpos($destPath, '/') + 1);
    
    if (file_exists($destPath) OR count(glob($destPathBefore . '*-' . $destPathAfter)) > 0) {
      
      $i = 2;
      while(file_exists($destPath . "-" . $i)) {
        $i++;
      }
      $destPath = $destPath . "-" . $i;
      $destUri = $destUri . "-" . $i;
      
    }
        
    if (is_dir($sourcePath) AND substr($destPath, 0,strrpos($destPath, '/')) != $sourcePath) {
      if (!Dir::copy($sourcePath, $destPath)) {
        return Response::error("Seite konnte nicht kopiert werden");
      }
    }
    else {
      return Response::error("Seite konnte nicht kopiert werden");
    }
    
    // Response data
    $data = [];
    if ($source) {
      $data['url'] = panel()->urls->index . "/pages/$destUri/edit";
      panel()->notify("Seite kopiert");
    }

    return Response::success("Kopieren erfolgreich", $data);
  },
]]);
