<?php

namespace Symfony\Component\DependencyInjection;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlCachedContainerBuilder
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var bool
     */
    private $debug;

    /**
     * Constructor.
     *
     * @param string $cacheDir
     * @param bool   $debug
     */
    public function __construct($cacheDir, $debug = false)
    {
        $this->cacheDir = $cacheDir;
        $this->debug = $debug;
    }

    /**
     * Build the container.
     *
     * @param string $configDir
     * @param string $configFile
     * @param string $className
     * @param string $namespace
     *
     * @return Container
     */
    public function build($configDir, $configFile, $className = 'CachedContainer', $namespace = '')
    {
        $fqClassName = '\\' . $namespace . '\\' . $className;
        $cacheFile = $this->cacheDir . '/' . $className . '.php';
        $configCache = new ConfigCache($cacheFile, $this->debug);

        if (!$configCache->isFresh()) {
            $containerBuilder = new ContainerBuilder();
            $loader = new YamlFileLoader(
                $containerBuilder,
                new FileLocator($configDir)
            );
            $loader->load($configFile);
            $containerBuilder->compile();

            $dumper = new PhpDumper($containerBuilder);
            $configCache->write(
                $dumper->dump(array('class' => $className, 'namespace' => $namespace)),
                $containerBuilder->getResources()
            );
        }

        return new $fqClassName();
    }
}
