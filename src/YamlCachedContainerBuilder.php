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
        $cleanNamespace = self::cleanNamespace($namespace);
        $fqClassName = (isEmpty($cleanNamespace) ? '' : '\\' . $cleanNamespace) . '\\' . $className;
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
                $dumper->dump(['class' => $className, 'namespace' => $namespace]),
                $containerBuilder->getResources()
            );
        }

        return new $fqClassName();
    }

    /**
     * Format the namespace to avoid any problem.
     *
     * @param $namespace
     *
     * @return string
     */
    private static function cleanNamespace($namespace)
    {
        $cleanNamespace = $namespace;

        // if namespace begins with \ we trim it
        if (strrpos($cleanNamespace, '\\') === 0) {
            $cleanNamespace = substr($cleanNamespace, 1, strlen($cleanNamespace)-1);
        }

        // if namespace is ending with \ we trim it
        if (strrpos($cleanNamespace, '\\') === strlen($cleanNamespace)-1) {
            $cleanNamespace = substr($cleanNamespace, 0, strlen($cleanNamespace)-1);
        }

        return $cleanNamespace;
    }
}
