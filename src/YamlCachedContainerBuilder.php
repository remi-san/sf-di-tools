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
    public function build($configDir, $configFile, $className = 'CachedContainer', $namespace = 'Cache')
    {
        if ($this->debug) { // provides useful info regarding namespace when in debug, ignored in prod for performance
            self::checkNamespace($namespace);
            self::checkClassName($className);
        }

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
    private static function checkNamespace($namespace)
    {
        if (empty($namespace)) {
            throw new \InvalidArgumentException('Namespace cannot be empty');
        }

        if (strrpos($namespace, '\\') === 0) { // if namespace begins with \
            throw new \InvalidArgumentException(sprintf('Namespace "%s" cannot begin with \\', $namespace));
        }

        if (strrpos($namespace, '\\') === strlen($namespace) - 1) { // if namespace is ending with \
            throw new \InvalidArgumentException(sprintf('Namespace "%s" cannot end with \\', $namespace));
        }

        // check if each part of the namespace is valid
        $parts = explode('\\', $namespace);
        foreach ($parts as $part) {
            if (!self::isNameValid($part)) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid namespace part : "%s" for "%s"', $part, $namespace)
                );
            }
        }
    }

    /**
     * @param $className
     */
    private static function checkClassName($className)
    {
        if (!self::isNameValid($className)) {
            throw new \InvalidArgumentException(sprintf('Class name "%" is not valid', $className));
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    private static function isNameValid($name)
    {
        return preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $name, $matches) === 1 &&
            count($matches) === 1 && $matches[0] === $name;
    }
}
