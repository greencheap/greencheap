<?php

namespace GreenCheap\Application;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Response
{
    /**
     * @var UrlProvider
     */
    protected UrlProvider $url;

    /**
     * Constructor.
     *
     * @param UrlProvider $url
     */
    public function __construct(UrlProvider $url)
    {
        $this->url = $url;
    }

    /**
     * Create shortcut.
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return HttpResponse
     * @see create()
     */
    public function __invoke($content = '', $status = 200, $headers = []): HttpResponse
    {
        return $this->create($content, $status, $headers);
    }

	/**
	 * Returns a response.
	 *
	 * @param  mixed $content
	 * @param  int   $status
	 * @param  array $headers
	 * @return HttpResponse
	 */
	public function create($content = '', $status = 200, $headers = []): HttpResponse
    {
		return new HttpResponse($content, $status, $headers);
	}

    /**
     * Returns a redirect response.
     *
     * @param string $url
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
	public function redirect(string $url, $parameters = [], $status = 302, $headers = []): RedirectResponse
    {
		return new RedirectResponse($this->url->get($url, $parameters), $status, $headers);
	}

	/**
	 * Returns a JSON response.
	 *
	 * @param  string|array $data
	 * @param  int          $status
	 * @param  array        $headers
	 * @return JsonResponse
	 */
	public function json($data = [], $status = 200, $headers = []): JsonResponse
    {
		return new JsonResponse($data, $status, $headers);
	}

	/**
	 * Returns a streamed response.
	 *
	 * @param  callable $callback
	 * @param  int      $status
	 * @param  array    $headers
	 * @return StreamedResponse
	 */
	public function stream(callable $callback, $status = 200, $headers = []): StreamedResponse
    {
		return new StreamedResponse($callback, $status, $headers);
	}

    /**
     * Returns a binary file download response.
     *
     * @param string $file
     * @param null $name
     * @param array $headers
     * @return BinaryFileResponse
     */
	public function download(string $file, $name = null, $headers = []): BinaryFileResponse
    {
		$response = new BinaryFileResponse($file, 200, $headers, true, 'attachment');

		if (!is_null($name)) {
			$response->setContentDisposition('attachment', $name);
		}

		return $response;
	}
}
