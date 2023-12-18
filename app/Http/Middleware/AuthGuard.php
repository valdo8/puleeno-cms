<?php

namespace App\Http\Middleware;

use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\JwtAuthentication;

class AuthGuard
{
    /**
     * The user model.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Create a new auth guard instance.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $account = $request->get('account');
        $password = $request->get('password');

        $user = $this->userService->findUserByAccountAndPassword($account, $password);

        if (!$user) {
            abort(401);
        }

        // Set the user in the request attributes
        $request = $request->withAttribute('user', $user);

        return $next($request, $response);
    }
}
