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

use de\codenamephp\deployer\base\functions\All;
use de\codenamephp\deployer\base\functions\iSet;
use de\codenamephp\platform\secretsManager\base\Client\ClientInterface;
use de\codenamephp\platform\secretsManager\base\Secret\SecretInterface;
use Deployer\Host\Host;

/**
 * Settings implementation that uses a client to fetch the payload of a secret and then sets it on the hosts or as global settings
 *
 * @psalm-api
 */
final class WithClient implements SettingsInterface {

  public function __construct(public readonly ClientInterface $client, public readonly iSet $deployerFunctions = new All()) {}

  public function fetch(SecretInterface $secret) : string {
    return $this->client->fetchPayload($secret)->getContent();
  }
  public function fetchMultiple(array $secretsToResolve) : array {
    return array_map(fn(SecretInterface $secret) : string => $this->fetch($secret), $secretsToResolve);

  }
  public function set(string $settingsKey, SecretInterface $secret, Host ...$hosts) : void {
    $payload = $this->fetch($secret);
    $hosts ? array_map(static fn(Host $host) => $host->set($settingsKey, $payload), $hosts) : $this->deployerFunctions->set($settingsKey, $payload);
  }

  public function setMultiple(array $secretsToSet, Host ...$hosts) : void {
    foreach($secretsToSet as $settingsKey => $secret) $this->set($settingsKey, $secret, ...$hosts);
  }
}