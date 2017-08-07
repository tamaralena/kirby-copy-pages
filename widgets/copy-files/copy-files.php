<?php

return array(
  'title' => array(
    'text'       => 'Seiten kopieren',
    'compressed' => false
  ),
  'options' => array(),
  'html' => function() {
    return tpl::load(__DIR__ . DS . 'template.php');
  },
);
