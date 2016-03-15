<?php
namespace PAGEmachine\Cors\AccessControl;

/*
 * This file is part of the PAGEmachine CORS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\HttpUtility;

/**
 * Represents a HTTP response
 */
class Response {

  /**
   * @var string $allowedOrigin
   */
  protected $allowedOrigin;

  /**
   * @return string
   */
  public function getAllowedOrigin() {
    return $this->allowedOrigin;
  }

  /**
   * @param string $allowedOrigin
   * @return void
   */
  public function setAllowedOrigin($allowedOrigin) {
    $this->allowedOrigin = $allowedOrigin;
  }

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
   * @var array $exposedHeaders
   */
  protected $exposedHeaders = array();

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
   * @var boolean $isPreflight
   */
  protected $isPreflight = FALSE;

  /**
   * @return boolean
   */
  public function isPreflight() {
    return $this->isPreflight;
  }

  /**
   * @param boolean $isPreflight
   * @return void
   */
  public function setPreflight($isPreflight) {
    $this->isPreflight = $isPreflight;
  }

  /**
   * @var array $allowedMethods
   */
  protected $allowedMethods = array();

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
   * @var array $allowedHeaders
   */
  protected $allowedHeaders = array();

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
   * Sends all HTTP headers and the body as necessary
   *
   * @return void
   */
  public function send() {

    if ($this->getAllowedOrigin()) {

      $this->sendHeader($this->buildHeaderString('Access-Control-Allow-Origin', $this->getAllowedOrigin()));
    }

    if ($this->getAllowCredentials()) {

      $this->sendHeader($this->buildHeaderString('Access-Control-Allow-Credentials', 'true'));
    }

    if (count($this->getExposedHeaders())) {

      $this->sendHeader($this->buildHeaderString('Access-Control-Expose-Headers', $this->getExposedHeaders()));
    }

    if ($this->isPreflight()) {

      if (count($this->getAllowedMethods())) {

        $this->sendHeader($this->buildHeaderString('Access-Control-Allow-Methods', $this->getAllowedMethods()));
      }

      if (count($this->getAllowedHeaders())) {

        $this->sendHeader($this->buildHeaderString('Access-Control-Allow-Headers', $this->getAllowedHeaders()));
      }

      // No need for a body in a preflight response
      $this->skipBodyAndExit();
    }
  }

  /**
   * Builds an HTTP response header
   *
   * Multiple values are joined like "foo, bar"
   *
   * @param string $name Name of an HTTP header
   * @param mixed $value Simple or multivalued value
   * @return string
   */
  protected function buildHeaderString($name, $value) {

    if (is_array($value)) {

      $value = implode(', ', $value);
    }

    return $name . ': ' . $value;
  }

  /**
   * Sends an HTTP response header
   *
   * @param string $header HTTP header
   * @return void
   */
  protected function sendHeader($header) {

    header($header);
  }

  /**
   * Skips the HTTP response body, sends an according
   * header (status 204) and stops script execution
   *
   * @return void
   */
  public function skipBodyAndExit() {

    HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_204);
  }
}
