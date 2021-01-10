<?php

namespace GreenCheap\Kernel\Event;

class ControllerEvent extends KernelEvent
{
    use ResponseTrait;

    /**
     * @var callable
     */
    protected $controller;

    /**
     * @var mixed
     */
    protected $controllerResult;

    /**
     * Gets the controller.
     *
     * @return callable
     */
    public function getController(): callable
    {
        return $this->controller;
    }

    /**
     * Sets the controller.
     *
     * @param callable $controller
     */
    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Gets the controller result.
     *
     * @return mixed
     */
    public function getControllerResult(): mixed
    {
        return $this->controllerResult;
    }

    /**
     * Sets the controller result.
     *
     * @param mixed $controllerResult
     */
    public function setControllerResult(mixed $controllerResult)
    {
        $this->controllerResult = $controllerResult;
    }
}
