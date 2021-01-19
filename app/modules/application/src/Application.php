<?php

namespace GreenCheap;

use GreenCheap\Application\Traits\EventTrait;
use GreenCheap\Application\Traits\RouterTrait;
use GreenCheap\Application\Traits\StaticTrait;
use GreenCheap\Event\EventDispatcher;
use GreenCheap\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method static response(false|string $file_get_contents, int $int, string[] $array)
 */
class Application extends Container
{
    use StaticTrait, EventTrait, RouterTrait;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * Constructor.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this['events'] = function () {
            return new EventDispatcher();
        };

        $this['module'] = function () {
            return new ModuleManager($this);
         };
    }

    /**
     * Boots all modules.
     */
    public function boot()
    {
        if (!$this->booted) {

            $this->booted = true;
            $this->trigger('boot', [$this]);

        }
    }

    /**
     * Handles the request.
     *
     * @param Request|null $request
     */
    public function run(Request $request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        if (!$this->booted) {
            $this->boot();
        }

        $response = $this['kernel']->handle($request);
        $response->send();

        $this['kernel']->terminate($request, $response);
    }

    /**
     * Checks if running in the console.
     *
     * @return bool
     */
    public function inConsole()
    {
        return PHP_SAPI == 'cli';
    }
}
