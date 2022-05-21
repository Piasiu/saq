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

            /* Route prefix */
            $prefixes = $reflection->getAttributes(RoutePrefix::class);

            if (count($prefixes) === 1)
            {
                /** @var RoutePrefix $prefix */
                $prefix = $prefixes[0]->newInstance();
            }
            else
            {
                $prefix = null;
            }

            /* Class middleware */
            $classMiddleware = [];
            $middlewareAttrs = $reflection->getAttributes(Middleware::class);

            foreach ($middlewareAttrs as $middlewareAttr)
            {
                $classMiddleware[] = $middlewareAttr->newInstance();
            }

            foreach ($methods as $method)
            {
                /* Route */
                $routeAttributes = $method->getAttributes(Route::class);

                if (count($routeAttributes) === 1)
                {
                    /** @var Route $route */
                    $route = $routeAttributes[0]->newInstance();
                    $route->setRawCallable([$className, $method->getName()]);

                    /* Route prefix */
                    if ($prefix !== null)
                    {
                        $route->addPrefix($prefix);
                    }

                    /* Route segments */
                    $segmentAttributes = $method->getAttributes(RouteSegment::class);

                    foreach ($segmentAttributes as $segmentAttribute)
                    {
                        /** @var RouteSegment $segment */
                        $segment = $segmentAttribute->newInstance();
                        $route->addSegment($segment);
                    }

                    /* Set class middleware */
                    foreach ($classMiddleware as $middleware)
                    {
                        $route->addMiddleware($middleware);
                    }

                    /* Set method middleware */
                    $middlewareAttrs = $method->getAttributes(Middleware::class);

                    foreach ($middlewareAttrs as $middlewareAttr)
                    {
                        /** @var Middleware $middleware */
                        $middleware = $middlewareAttr->newInstance();
                        $route->addMiddleware($middleware);
                    }

                    $routeCollection->addRoute($route);
                    $data[] = $this->getPreparedRouteData($route);
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
     * @param Route $route
     * @return array
     */
    private function getPreparedRouteData(Route $route): array
    {
        $segments = [];

        foreach ($route->getSegments() as $segment)
        {
            $segments[] = [
                $segment->getPath(),
                $segment->getRawArguments(),
                $segment->getDefaults(),
                $segment->getPattern()
            ];
        }

        $middlewareItems = [];

        foreach ($route->getMiddlewareList() as $middleware)
        {
            $middlewareItems[] = $middleware->getClass();
        }

        return [
            $route->getName(),
            $route->getMethods(),
            $route->getRawCallable(),
            $segments,
            $middlewareItems
        ];
    }

    /**
     * @param RouteCollectionInterface $routeCollection
     */
    #[NoReturn]
    private function collectFromCache(RouteCollectionInterface $routeCollection): void
    {
        $json = file_get_contents($this->cacheFile);
        $data = json_decode($json, true);

        foreach ($data as $routeData)
        {
            $mainSegment = $this->createSegment(array_shift($routeData[3]));
            $route = new Route($routeData[0], $mainSegment, $routeData[1]);
            $route->setRawCallable($routeData[2]);

            foreach ($routeData[3] as $segmentData)
            {
                $segment = $this->createSegment($segmentData);
                $route->addSegment($segment);
            }

            foreach ($routeData[4] as $middlewareClass)
            {
                $middleware = new Middleware($middlewareClass);
                $route->addMiddleware($middleware);
            }

            $routeCollection->addRoute($route);
        }
    }

    /**
     * @param array $data
     * @return RouteSegment
     */
    private function createSegment(array $data): RouteSegment
    {
        $segment = new RouteSegment($data[0], $data[1], $data[2]);
        $segment->setPattern($data[3]);
        return $segment;
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