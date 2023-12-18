<?php

namespace App\Facades;

use App\Http\Middleware\AuthGuard;
use Psr\Container\ContainerInterface;

class Auth extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'auth';
    }

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * Tạo guard mới.
     *
     * @return AuthGuard
     */
    public static function factory(): AuthGuard
    {
        return new AuthGuard();
    }

    /**
     * Lấy guard hiện tại.
     *
     * @return AuthGuard
     */
    public static function get(): AuthGuard
    {
        return app(AuthGuard::class);
    }

    /**
     * Xác thực người dùng.
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public static function attempt(string $email, string $password): bool
    {
        return self::get()->attempt($email, $password);
    }

    /**
     * Kiểm tra xem người dùng đã đăng nhập hay chưa.
     *
     * @return bool
     */
    public static function check(): bool
    {
        return self::get()->check();
    }

    /**
     * Đăng xuất người dùng.
     *
     * @return void
     */
    public static function logout(): void
    {
        self::get()->logout();
    }
}
