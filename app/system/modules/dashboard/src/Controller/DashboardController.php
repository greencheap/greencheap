<?php
namespace GreenCheap\Dashboard\Controller;

use GreenCheap\Application as App;
use GreenCheap\Module\Module;

/**
 * @Access(admin=true)
 */
class DashboardController
{
    /**
     * @var Module
     */
    protected $dashboard;

    /**
     * @var string
     */
    protected $api = 'http://api.openweathermap.org/data/2.5';

    /**
     * @var string
     */
    protected $apiKey = '08c012f513db564bd6d4bae94b73cc94';

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
    public function indexAction()
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
     */
    public function saveAction($widgets = [])
    {
        $this->dashboard->saveWidgets($widgets);
        return $widgets;
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\w+"})
     * @Request({"id"}, csrf=true)
     */
    public function deleteAction($id)
    {
        
    }

    /**
     * @Request({"data": "array", "action": "string",})
     */
    public function weatherAction($data, $action)
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
