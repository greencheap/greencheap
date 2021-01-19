<?php
namespace GreenCheap\Dashboard\Controller;

use GreenCheap\Application as App;
use GreenCheap\Application\Response;
use GreenCheap\Module\Module;
use GreenCheap\Routing\Annotation\Request;
use GreenCheap\Routing\Annotation\Route;
use GreenCheap\User\Annotation\Access;

/**
 * @Access(admin=true)
 */
class DashboardController
{
    /**
     * @var Module
     */
    protected Module $dashboard;

    /**
     * @var string
     */
    protected string $api = 'http://api.openweathermap.org/data/2.5';

    /**
     * @var string
     */
    protected string $apiKey = '08c012f513db564bd6d4bae94b73cc94';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->dashboard = App::module('system/dashboard');
    }

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction(): array
    {
        return [
            '$view' => [
                'title' => __('Dashboard'),
                'name' => 'system/dashboard:views/index.php'
            ],
            '$data' => [
                'widgets' => array_values($this->dashboard->getWidgets())
            ]
        ];
    }

    /**
     * @Route("/", methods="POST")
     * @Request({"widgets": "array"}, csrf=true)
     * @param array $widgets
     * @return array|mixed
     */
    public function saveAction($widgets = []): mixed
    {
        $this->dashboard->saveWidgets($widgets);
        return $widgets;
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\w+"})
     * @Request({"id"}, csrf=true)
     * @param $id
     */
    #[deprecated]
    public function deleteAction($id)
    {}

    /**
     * @Request({"data": "array", "action": "string",})
     * @param $data
     * @param $action
     * @return mixed
     */
    public function weatherAction($data, $action):Response
    {
        $url = $this->api;

        if ($action === 'weather') {
            $url .= '/weather';
        } elseif ($action === 'find') {
            $url .= '/find';
        }

        $data['APPID'] = $this->apiKey;
        $url .= '?' . http_build_query($data);

        return App::response(file_get_contents((string) $url), 200, ['Content-Type' => 'application/json']);
    }
}
