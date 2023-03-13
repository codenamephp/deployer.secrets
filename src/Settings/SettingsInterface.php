<?php declare(strict_types=1);
/*
 *   Copyright 2023 Bastian Schwarz <bastian@codename-php.de>.
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *         http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

namespace de\codenamephp\deployer\secrets\Settings;

use de\codenamephp\platform\secretsManager\base\Secret\SecretInterface;
use Deployer\Host\Host;

/**
 * Interface to set settings either globally or on hosts
 *
 * @psalm-api
 */
interface SettingsInterface {

  /**
   * Resolves the given secret and sets it either on all hosts or if no hosts are given as a global setting
   *
   * Implementations should make use of the \de\codenamephp\platform\secretsManager\base\Client\ClientInterface to resolve the secret
   *
   * @param string $settingsKey The key to set the payload of the secret to
   * @param SecretInterface $secret The secret to get the payload for
   * @param Host ...$hosts Hosts to set the secret on. If no hosts are given the secret will be set globally
   * @return void
   */
  public function set(string $settingsKey, SecretInterface $secret, Host ...$hosts) : void;

  /**
   * An array of settings keys and secrets to set. The array key is used as settings key.
   *
   * Resolves the given secrets and sets them either on all hosts or if no hosts are given as global settings
   *
   * Implementations should make use of the \de\codenamephp\platform\secretsManager\base\Client\ClientInterface to resolve the secrets
   *
   * @param array<string, SecretInterface> $secretsToSet The key/secrets mapping
   * @param Host ...$hosts Hosts to set the secrets on. If no hosts are given the secrets will be set globally
   * @return void
   */
  public function setMultiple(array $secretsToSet, Host ...$hosts) : void;

  /**
   * Fetches the payload content of the given secret. Implementations should use the \de\codenamephp\platform\secretsManager\base\Client\ClientInterface to
   * resolve the secret
   *
   * @param SecretInterface $secret The secret to fetch the payload for
   * @return string The payload content
   */
  public function fetch(SecretInterface $secret) : string;

  /**
   * Fetches the payload content of the given secrets. Implementations should use the \de\codenamephp\platform\secretsManager\base\Client\ClientInterface to
   * resolve the secrets. Implementations MUST preserve the order and array keys of the given array as they may be used to identify the secrets
   *
   * @param array<SecretInterface> $secretsToResolve The secrets to fetch the payload for
   * @return array<string> The payload content of the secrets with the keys preserved
   */
  public function fetchMultiple(array $secretsToResolve) : array;
}