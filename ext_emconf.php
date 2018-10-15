<?php

$EM_CONF[$_EXTKEY] = [
  'title' => 'CORS',
  'description' => 'Cross Origin Resource Sharing for TYPO3',
  'category' => 'fe',
  'author' => 'Mathias Brodala',
  'author_email' => 'mbrodala@pagemachine.de',
  'author_company' => 'Pagemachine AG',
  'state' => 'stable',
  'version' => '2.0.6',
  'constraints' => [
    'depends' => [
      'php' => '7.0.0-7.2.99',
      'typo3' => '8.7.0-9.9.99',
    ],
  ],
];
