<?php
namespace Saq\Routing;

use JetBrains\PhpStorm\NoReturn;
use ReflectionClass;
use ReflectionException;
use Saq\Interfaces\Routing\RouteCollectionInterface;

class RouteCollector
{
    /**
     * @var string
     */
    private string $path = '.';

    /**
     * @var string
     */
    private string $pattern = '/^.+Controller\.php$/i';

    /**
     * @var string|null
     */
    private ?string $cacheFile = null;

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = rtrim(trim($path), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $pattern
     */
    public function setPattern(string $pattern): void
    {
        $this->pattern = sprintf('/^%s\.php$/i', $pattern);
    }

    /**
     * @param string $cacheFile
     */
    public function setCacheFile(string $cacheFile): void
    {
        $path = dirname($cacheFile);

        if (!file_exists($path))
        {
            mkdir($path, 0777, true);
        }

        $this->cacheFile = $cacheFile;
    }

    /**
     * @param RouteCollectionInterface $routeCollection
     * @throws ReflectionException
     */
    public function collect(RouteCollectionInterface $routeCollection): void
    {
        if ($this->cacheFile === null || !file_exists($this->cacheFile))
        {
            $this->collectFromFiles($routeCollection);
        }
        else
        {
            $this->collectFromCache($routeCollection);
        }
    }

    /**
     * @param RouteCollectionInterface $routeCollection
     * @throws ReflectionException
     */
    private function collectFromFiles(RouteCollectionInterface $routeCollection): void
    {
        $files = $this->findFiles($this->path);
        $data = [];

        foreach ($files as $file)
        {
            $className = $this->parseFile($file);
            $reflection = new ReflectionClass($className);
            $methods = $reflection->getMethods();

            $groups = $reflection->getAttributes(RouteGroup::class);

            if (count($groups) === 1)
            {
                /** @var RouteGroup $group */
                $group = $groups[0]->newInstance();
            }
            else
            {
                $group = null;
            }

            foreach ($methods as $method)
            {
                $attributes = $method->getAttributes(Route::class);

                if (count($attributes) === 1)
                {
                    /** @var Route $route */
                    $route = $attributes[0]->newInstance();
                    $route->setRawCallable([$className, $method->getName()]);

                    if ($group !== null)
                    {
                        $route->addGroup($group);
                    }

                    $routeCollection->addRoute($route);

                    $data[] = [
                        $route->getName(),
                        $route->getPath(),
                        $route->getMethods(),
                        $route->getRawArguments(),
                        $route->getDefaults(),
                        $route->getPattern(),
                        $route->getRawCallable()
                    ];
                }
            }
        }

        if ($this->cacheFile !== null)
        {
            $json = json_encode($data);
            file_put_contents($this->cacheFile, $json);
        }
    }

    /**
     * @param RouteCollectionInterface $routeCollection
     */
    #[NoReturn]
    private function collectFromCache(RouteCollectionInterface $routeCollection): void
    {
        $json = file_get_contents($this->cacheFile);
        $data = json_decode($json, true);

        foreach ($data as $item)
        {
            $route = new Route($item[0], $item[1], $item[2], $item[3], $item[4]);
            $route->setPattern($item[5]);
            $route->setRawCallable($item[6]);
            $routeCollection->addRoute($route);
        }
    }

    /**
     * @param string $path
     * @return array
     */
    private function findFiles(string $path): array
    {
        $files = [];

        if (is_dir($path))
        {
            $names = scandir($path);

            foreach ($names as $name)
            {
                $fullPath = $path.DIRECTORY_SEPARATOR.$name;

                if (is_dir($fullPath))
                {
                    if ($name !== '.' && $name !== '..')
                    {
                        $files = array_merge($files, $this->findFiles($fullPath));
                    }
                }
                elseif (preg_match($this->pattern, $name))
                {
                    $files[] = $fullPath;
                }
            }
        }

        return $files;
    }

    /**
     * @param string $filePath
     * @return string
     */
    private function parseFile(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $namespace = null;
        $class = null;

        if (preg_match('/namespace\s+([a-z][-_a-z\d\\\\]+)/i', $content, $matches) === 1)
        {
            $namespace = $matches[1];
        }

        if (preg_match('/class\s+([a-z][-_a-z\d]+)/i', $content, $matches) === 1)
        {
            $class = $matches[1];
        }

        return $namespace.'\\'.$class;
    }
}