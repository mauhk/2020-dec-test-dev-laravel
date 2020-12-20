<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;

class JsonMiddleware
{
    /**
     * The Response Factory our app uses
     *
     * @var ResponseFactory
     */
    protected $factory;

    /**
     * JsonMiddleware constructor.
     *
     * @param ResponseFactory $factory
     */
    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // First, set the header so any other middleware knows we're
        // dealing with a should-be JSON response.
        $request->headers->set('Accept', 'application/json');

        // Get the response
        $response = $next($request);

        // If the response is not strictly a JsonResponse, we make it
        if (!$response instanceof JsonResponse) {
            $response = $this->factory->json(
                $response->content(),
                $response->status(),
                $response->headers->all()
            );
        }

        $data = $response->getData(true);
        if (isset($data['exception'])) {
            if (config('app.debug')) {
                $response->setData([
                    'message' => $response->exception->getMessage(),
                    'errors' => [
                        $response->exception->getCode()?: 'exception' => [$response->exception->getMessage()]
                    ],
                    'file' => $response->exception->getFile(),
                    'line' => $response->exception->getLine(),
                    'trace' => $response->exception->getTrace(),
                ]);
            } else {
                $response->setData([
                    'message' => $response->exception->getMessage(),
                    'errors' => [
                        $response->exception->getCode() => $response->exception->getMessage()
                    ],
                ]);
            }
        } else if (!isset($data['errors'])) {
            $response->setData(['result' => $data]);
        }

        $response->setStatusCode(200);

        return $response;
    }
}
