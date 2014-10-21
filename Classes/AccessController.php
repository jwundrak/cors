<?php
namespace PAGEmachine\CORS;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Mathias Brodala <mbrodala@pagemachine.de>, PAGEmachine AG
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PAGEmachine\CORS\Http\Uri;

class AccessController {

  /**
   * @var boolean $allowCredentials
   */
  protected $allowCredentials = FALSE;
  
  /**
   * @return boolean
   */
  public function getAllowCredentials() {
    return $this->allowCredentials;
  }
  
  /**
   * @param boolean $allowCredentials
   * @return void
   */
  public function setAllowCredentials($allowCredentials) {
    $this->allowCredentials = $allowCredentials;
  }

  /**
   * @var array $allowedHeaders
   */
  protected $allowedHeaders;
  
  /**
   * @return array
   */
  public function getAllowedHeaders() {
    return $this->allowedHeaders;
  }
  
  /**
   * @param array $allowedHeaders
   * @return void
   */
  public function setAllowedHeaders(array $allowedHeaders) {
    $this->allowedHeaders = $allowedHeaders;
  }

  /**
   * @var array $allowedMethods
   */
  protected $allowedMethods;
  
  /**
   * @return array
   */
  public function getAllowedMethods() {
    return $this->allowedMethods;
  }
  
  /**
   * @param array $allowedMethods
   * @return void
   */
  public function setAllowedMethods(array $allowedMethods) {
    $this->allowedMethods = $allowedMethods;
  }

  /**
   * @var array $allowedOrigins
   */
  protected $allowedOrigins = array();
  
  /**
   * @return array
   */
  public function getAllowedOrigins() {
    return $this->allowedOrigins;
  }
  
  /**
   * @param array $allowedOrigins
   * @return void
   */
  public function setAllowedOrigins(array $allowedOrigins) {
    $this->allowedOrigins = $allowedOrigins;
  }

  /**
   * @var string $allowedOriginsPattern
   */
  protected $allowedOriginsPattern;
  
  /**
   * @return string
   */
  public function getAllowedOriginsPattern() {
    return $this->allowedOriginsPattern;
  }
  
  /**
   * @param string $allowedOriginsPattern
   * @return void
   */
  public function setAllowedOriginsPattern($allowedOriginsPattern) {
    $this->allowedOriginsPattern = $allowedOriginsPattern;
  }

  /**
   * @var array $exposedHeaders
   */
  protected $exposedHeaders;
  
  /**
   * @return array
   */
  public function getExposedHeaders() {
    return $this->exposedHeaders;
  }
  
  /**
   * @param array $exposedHeaders
   * @return void
   */
  public function setExposedHeaders(array $exposedHeaders) {
    $this->exposedHeaders = $exposedHeaders;
  }

  /**
   * @var integer $maximumAge
   */
  protected $maximumAge;
  
  /**
   * @return integer
   */
  public function getMaximumAge() {
    return $this->maximumAge;
  }
  
  /**
   * @param integer $maximumAge
   * @return void
   */
  public function setMaximumAge($maximumAge) {
    $this->maximumAge = $maximumAge;
  }

  /**
   * Returns TRUE, if the current request is cross origin, FALSE otherwise
   *
   * A request is cross origin one of scheme, host or protocol does not match
   *
   * @param Uri $origin The origin
   * @param Uri $request The current request
   * @return boolean
   */
  public function isCrossOriginRequest(Uri $origin, Uri $request) {

    return $origin->getScheme() != $request->getScheme() ||
           $origin->getHostname() != $request->getHostname() ||
           $origin->getPort() != $request->getPort();
  }

  /**
   * Returns TRUE, if access is allowed for an origin, FALSE otherwise
   *
   * @param string $originUri The origin URI
   * @return boolean
   */
  public function isOriginUriAllowed($originUri) {

    // Check for exact match
    if (in_array($originUri, $this->allowedOrigins)) {

      return TRUE;
    }

    // Check for pattern match
    if ($this->allowedOriginsPattern) {

      // Explicitely not using preg_quote() here to allow for pattern passthrough
      if (preg_match('~^' . $this->allowedOriginsPattern . '~i', $originUri) === 1) {

        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Sends all access control HTTP headers for an origin
   *
   * @param Uri $origin The origin
   * @return void
   */
  public function sendHeadersForOrigin(Uri $origin) {

    $originUri = $origin->getScheme() . '://' . $origin->getHostname();
    $headers = array();

    if ($this->isOriginUriAllowed('*')) {

      $headers['Access-Control-Allow-Origin'] = '*';
    } elseif ($this->isOriginUriAllowed($originUri)) {

      $headers['Access-Control-Allow-Origin'] = $originUri;
    } else {

      return;
    }

    if ($this->getAllowCredentials()) {

      $headers['Access-Control-Allow-Credentials'] = 'true';
    }

    if (count($this->getAllowedHeaders())) {

      $headers['Access-Control-Allow-Headers'] = $this->getAllowedHeaders();
    }

    if (count($this->getAllowedMethods())) {

      $headers['Access-Control-Allow-Methods'] = $this->getAllowedMethods();
    }

    if (count($this->getExposedHeaders())) {

      $headers['Access-Control-Expose-Headers'] = $this->getExposedHeaders();
    }

    if ($this->getMaximumAge()) {

      $headers['Access-Control-Max-Age'] = $this->getMaximumAge();
    }

    foreach ($headers as $name => $value) {

      if (is_array($value)) {

        $value = implode(', ', $value);
      }

      header($name . ': ' . $value);
    }
  }
}
