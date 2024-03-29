<?php

namespace RedJasmine\Support\Services;


use Closure;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Throwable;


class DomainRoute
{

    public static bool $subDomain = false;


    public static function register() : void
    {
        Route::pattern('domain', '[A-Za-z0-9\.\-]+');
    }

    public static function boot() : void
    {

        // 是否开启子域名
        self::$subDomain = config('app.sub_domain', false);


        if (self::$subDomain) {
            // 注册中间件

            $kernel = app()->make(Kernel::class);
            $kernel->appendMiddlewareToGroup('web', __CLASS__);
            $kernel->prependMiddlewareToGroup('web', __CLASS__);
            $kernel->appendMiddlewareToGroup('api', __CLASS__);
            $kernel->prependMiddlewareToGroup('api', __CLASS__);

            // 设置链接基础参数
            $hostUri = config('app.url', 'localhost');
            $domain  = Str::remove([ 'https://', 'http://' ], $hostUri);

            URL::defaults([ 'domain' => $domain ]);
        }
    }


    /**
     * 中间件处理
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next) : mixed
    {

        try {
            $route  = $request->route();
            $domain = $route->parameter('domain');

            $route->forgetParameter('domain');
            URL::defaults([ 'domain' => $domain ]);
        } catch (Throwable $throwable) {

        }

        return $next($request);
    }


    /**
     * 设置域名
     *
     * @param string|null $domain
     *
     * @return string|null
     */
    public static function domain(string $domain = null) : ?string
    {
        // 是否开启子域名
        if ((boolean)config('app.sub_domain', false) === false) {
            return null;
        }
        return filled($domain) ? $domain . '.{domain}' : '.{domain}';

    }

    public static function adminDomain() : ?string
    {
        return self::domain('admin');
    }

    public static function adminWebPrefix(string $module = null) : ?string
    {
        if (filled($module)) {
            return config('admin.route.prefix') . '/' . $module;
        }
        return config('admin.route.prefix');
    }

    /**
     * 路由前缀
     *
     * @param string      $module
     * @param string|null $guard
     * @param bool        $isApi
     *
     * @return string
     */
    public static function prefix(string $module, string $guard = null, bool $isApi = false) : string
    {
        $prefix = $isApi ? 'api/' : '';
        $prefix .= $guard;
        return $prefix . '/' . $module;
    }


}
