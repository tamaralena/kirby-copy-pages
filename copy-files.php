<?php

namespace Kirby\Plugins\CopyFiles;

use Response;
use Dir;

if (!function_exists('panel')) return;

function stripDotSegments($path) {
  return preg_replace('#(^|/)\.{1,}/#', '/', $path);
}

// Load widget
kirby()->set('widget', 'copy-files', __DIR__ . DS . 'widgets' . DS . 'copy-files');

// Add routes
panel()->routes([[
  'pattern' => 'copy-files/api/copy',
  'method' => 'POST',
  'action' => function() {
    $user = site()->user()->current();
    if (!$user || (!$user->hasPermission('panel.page.create') && !$user->isAdmin())) {
      return Response::error("Keine Berechtigung");
    }

    $sourceUrl = stripDotSegments(get('source'));
    $destUrl = stripDotSegments(get('dest'));

    // Convert source uri to its proper path
    $source = page($sourceUrl);
    if ($source) {
      $sourceUrl = $source->diruri();
      $sourceUid = $source->uid();
    }

    // Convert destination uri to its proper path
    
    if ($destUrl == "/") {
      $dest = site();
    }
    else {
      $dest = page($destUrl);
    }
    
    if ($dest) {
      $destUid = $sourceUid . "-2";
      $destUri = $dest->uri() . DS . $destUid;
      $destUrl = $dest->diruri() . DS . $destUid;
    }

    $sourcePath = kirby()->roots->content() . DS . $sourceUrl;
    $destPath = kirby()->roots->content() . DS . $destUrl;
    
    if (is_dir($sourcePath)) {
      if (!Dir::copy($sourcePath, $destPath)) {
        return Response::error("Seite konnte nicht kopiert werden");
      }
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
