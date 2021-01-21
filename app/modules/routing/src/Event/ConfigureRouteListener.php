<?php

namespace GreenCheap\Routing\Event;

/**
 * @todo Deprecated Class
 * @body The removed class needs to be edited.
 * @deprecated
 * use Doctrine\Common\Annotations\SimpleAnnotationReader;
 * to
 * use Doctrine\Common\Annotations\Reader;
 */
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Doctrine\Common\Annotations\Reader;
use GreenCheap\Event\EventSubscriberInterface;

class ConfigureRouteListener implements EventSubscriberInterface
{
    protected $reader;
    protected $namespace;

    /**
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader = null)
    {
        $this->reader    = $reader;
        $this->namespace = 'GreenCheap\Routing\Annotation';
    }

    /**
     * Reads the @Request annotations.
     */
    public function onConfigureRoute($event, $route)
    {
        if (!$route->getControllerClass()) {
            return;
        }

        $reader = $this->getReader();

        foreach (['_request' => 'Request'] as $name => $class) {

            $class = "{$this->namespace}\\$class";

            if (($annotation = $reader->getClassAnnotation($route->getControllerClass(), $class) or $annotation = $reader->getMethodAnnotation($route->getControllerMethod(), $class))
                and $data = $annotation->getData()
            ) {
                $route->setDefault($name, $data);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function subscribe()
    {
        return [
            'route.configure' => 'onConfigureRoute'
        ];
    }

    /**
     * Gets an annotation reader.
     *
     * @return Reader
     */
    protected function getReader()
    {
        if (!$this->reader) {
            $this->reader = new SimpleAnnotationReader();
            $this->reader->addNamespace($this->namespace);
        }

        return $this->reader;
    }
}
