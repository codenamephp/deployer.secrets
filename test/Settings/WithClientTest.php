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

namespace de\codenamephp\deployer\secrets\test\Settings;

use de\codenamephp\deployer\base\functions\iSet;
use de\codenamephp\deployer\secrets\Settings\WithClient;
use de\codenamephp\platform\secretsManager\base\Client\ClientInterface;
use de\codenamephp\platform\secretsManager\base\Secret\Payload\PayloadInterface;
use de\codenamephp\platform\secretsManager\base\Secret\SecretInterface;
use Deployer\Host\Host;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

final class WithClientTest extends TestCase {

  use MockeryPHPUnitIntegration;

  public function testSetMultiple() : void {
    $payload1 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret1']);
    $payload2 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret2']);
    $payload3 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret3']);

    $secret1 = $this->createMock(SecretInterface::class);
    $secret2 = $this->createMock(SecretInterface::class);
    $secret3 = $this->createMock(SecretInterface::class);

    $client = Mockery::mock(ClientInterface::class);
    $client->allows('fetchPayload')->once()->ordered()->with($secret1)->andReturn($payload1);
    $client->allows('fetchPayload')->once()->ordered()->with($secret2)->andReturn($payload2);
    $client->allows('fetchPayload')->once()->ordered()->with($secret3)->andReturn($payload3);

    $deployerFunctions = Mockery::mock(iSet::class);
    $deployerFunctions->allows('set')->once()->ordered()->with('key1', 'secret1');
    $deployerFunctions->allows('set')->once()->ordered()->with('key2', 'secret2');
    $deployerFunctions->allows('set')->once()->ordered()->with('key3', 'secret3');

    $sut = new WithClient($client, $deployerFunctions);

    $sut->setMultiple(['key1' => $secret1, 'key2' => $secret2, 'key3' => $secret3]);
  }

  public function testSetMultiple_withHosts() : void {
    $host1 = Mockery::mock(Host::class);
    $host1->allows('set')->once()->ordered()->with('key1', 'secret1');
    $host1->allows('set')->once()->ordered()->with('key2', 'secret2');
    $host1->allows('set')->once()->ordered()->with('key3', 'secret3');
    $host2 = Mockery::mock(Host::class);
    $host2->allows('set')->once()->ordered()->with('key1', 'secret1');
    $host2->allows('set')->once()->ordered()->with('key2', 'secret2');
    $host2->allows('set')->once()->ordered()->with('key3', 'secret3');
    $host3 = Mockery::mock(Host::class);
    $host3->allows('set')->once()->ordered()->with('key1', 'secret1');
    $host3->allows('set')->once()->ordered()->with('key2', 'secret2');
    $host3->allows('set')->once()->ordered()->with('key3', 'secret3');

    $payload1 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret1']);
    $payload2 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret2']);
    $payload3 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret3']);

    $secret1 = $this->createMock(SecretInterface::class);
    $secret2 = $this->createMock(SecretInterface::class);
    $secret3 = $this->createMock(SecretInterface::class);

    $client = Mockery::mock(ClientInterface::class);
    $client->allows('fetchPayload')->once()->ordered()->with($secret1)->andReturn($payload1);
    $client->allows('fetchPayload')->once()->ordered()->with($secret2)->andReturn($payload2);
    $client->allows('fetchPayload')->once()->ordered()->with($secret3)->andReturn($payload3);

    $deployerFunctions = $this->createMock(iSet::class);
    $deployerFunctions->expects(self::never())->method('set');

    $sut = new WithClient($client, $deployerFunctions);

    $sut->setMultiple(['key1' => $secret1, 'key2' => $secret2, 'key3' => $secret3], $host1, $host2, $host3);
  }

  public function testSet() : void {
    $payload = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret']);
    $secret = $this->createMock(SecretInterface::class);

    $client = $this->createMock(ClientInterface::class);
    $client->expects(self::once())->method('fetchPayload')->with($secret)->willReturn($payload);

    $deployerFunctions = $this->createMock(iSet::class);
    $deployerFunctions->expects(self::once())->method('set')->with('some.key', 'secret');

    $sut = new WithClient($client, $deployerFunctions);

    $sut->set('some.key', $secret);
  }

  public function testSet_withHosts() : void {
    $host1 = $this->createMock(Host::class);
    $host1->expects(self::once())->method('set')->with('some.key', 'secret');
    $host2 = $this->createMock(Host::class);
    $host2->expects(self::once())->method('set')->with('some.key', 'secret');
    $host3 = $this->createMock(Host::class);
    $host3->expects(self::once())->method('set')->with('some.key', 'secret');

    $payload = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret']);
    $secret = $this->createMock(SecretInterface::class);

    $client = $this->createMock(ClientInterface::class);
    $client->expects(self::once())->method('fetchPayload')->with($secret)->willReturn($payload);

    $deployerFunctions = $this->createMock(iSet::class);
    $deployerFunctions->expects(self::never())->method('set');

    $sut = new WithClient($client, $deployerFunctions);

    $sut->set('some.key', $secret, $host1, $host2, $host3);
  }

  public function testFetchMultiple() : void {
    $payload1 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret1']);
    $payload2 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret2']);
    $payload3 = $this->createConfiguredMock(PayloadInterface::class, ['getContent' => 'secret3']);

    $secret1 = $this->createMock(SecretInterface::class);
    $secret2 = $this->createMock(SecretInterface::class);
    $secret3 = $this->createMock(SecretInterface::class);

    $client = Mockery::mock(ClientInterface::class);
    $client->allows('fetchPayload')->once()->ordered()->with($secret1)->andReturn($payload1);
    $client->allows('fetchPayload')->once()->ordered()->with($secret2)->andReturn($payload2);
    $client->allows('fetchPayload')->once()->ordered()->with($secret3)->andReturn($payload3);

    $sut = new WithClient($client);

    self::assertSame(['key1' => 'secret1', 'key2' => 'secret2', 'key3' => 'secret3'], $sut->fetchMultiple(['key1' => $secret1, 'key2' => $secret2, 'key3' => $secret3]));
  }

  public function test__construct() : void {
    $client = $this->createMock(ClientInterface::class);
    $deployerFunctions = $this->createMock(iSet::class);

    $sut = new WithClient($client, $deployerFunctions);

    self::assertSame($client, $sut->client);
    self::assertSame($deployerFunctions, $sut->deployerFunctions);
  }

  public function test__construct_withMinimalParameters() : void {
    $client = $this->createMock(ClientInterface::class);

    $sut = new WithClient($client);

    self::assertSame($client, $sut->client);
    self::assertInstanceOf(iSet::class, $sut->deployerFunctions);
  }

  public function testFetch() : void {
    $secret = $this->createMock(SecretInterface::class);
    $payload = $this->createMock(PayloadInterface::class);
    $payload->expects(self::once())->method('getContent')->willReturn('some secret');

    $client = $this->createMock(ClientInterface::class);
    $client->expects(self::once())->method('fetchPayload')->with($secret)->willReturn($payload);

    $sut = new WithClient($client);

    self::assertSame('some secret', $sut->fetch($secret));
  }
}
