<?php

namespace Symfony\Component\DependencyInjection\Test;

use Symfony\Component\DependencyInjection\YamlCachedContainerBuilder;

class YamlCachedContainerBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $cachedDir;
    private $configDir;
    private $configFile;

    public function setUp()
    {
        $this->cachedDir  = __DIR__ . '/cache';
        $this->configDir  = __DIR__ . '/config';
        $this->configFile = 'params.yml';
    }

    public function tearDown()
    {
        \Mockery::close();

        foreach (glob($this->cachedDir . '/*.php*') as $filename) {
            unlink($filename);
        }
    }

    /**
     * @test
     */
    public function testInDebugModeWithValidInfo()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, true);

        $container = $cacheBuilder->build($this->configDir, $this->configFile, 'CachedContainer', 'Cache');

        $this->assertInstanceOf('Cache\\CachedContainer', $container);
        $this->assertEquals('test-value', $container->getParameter('test'));
    }

    /**
     * @test
     */
    public function testInDebugModeWithInvalidClassName()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, true);

        $this->setExpectedException(\InvalidArgumentException::class);

        $cacheBuilder->build($this->configDir, $this->configFile, 'Cached$Container', 'Cache');
    }

    /**
     * @test
     */
    public function testInDebugModeWithInvalidNamespace()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, true);

        $this->setExpectedException(\InvalidArgumentException::class);

        $cacheBuilder->build($this->configDir, $this->configFile, 'CachedContainer', 'Cac$he');
    }

    /**
     * @test
     */
    public function testInDebugModeWithNamespaceBeginningBySeparator()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, true);

        $this->setExpectedException(\InvalidArgumentException::class);

        $cacheBuilder->build($this->configDir, $this->configFile, 'CachedContainer', '\\Cache');
    }

    /**
     * @test
     */
    public function testInDebugModeWithNamespaceEndingWithSeparator()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, true);

        $this->setExpectedException(\InvalidArgumentException::class);

        $cacheBuilder->build($this->configDir, $this->configFile, 'CachedContainer', 'Cache\\');
    }

    /**
     * @test
     */
    public function testInDebugModeWithEmptyNamespace()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, true);

        $this->setExpectedException(\InvalidArgumentException::class);

        $cacheBuilder->build($this->configDir, $this->configFile, 'CachedContainer', '');
    }

    /**
     * @test
     */
    public function testWithoutDebugMode()
    {
        $cacheBuilder = new YamlCachedContainerBuilder($this->cachedDir, false);

        $container = $cacheBuilder->build($this->configDir, $this->configFile, 'CachedContainer', 'Cache');

        $this->assertInstanceOf('Cache\\CachedContainer', $container);
        $this->assertEquals('test-value', $container->getParameter('test'));
    }
}
