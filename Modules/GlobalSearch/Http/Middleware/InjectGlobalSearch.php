<?php

namespace Modules\GlobalSearch\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\View;

class InjectGlobalSearch
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        try {
            if (!function_exists('auth') || !auth()->check()) {
                return $response;
            }

            if (!method_exists($response, 'headers') && !property_exists($response, 'headers')) {
                return $response;
            }

            $contentType = $response->headers->get('Content-Type');
            if ($contentType && stripos($contentType, 'text/html') === false) {
                return $response;
            }

            if (!method_exists($response, 'getContent') || !method_exists($response, 'setContent')) {
                return $response;
            }

            $content = $response->getContent();
            if (stripos($content, 'id="erp-global-search-overlay"') !== false) {
                return $response;
            }

            $html = View::make('globalsearch::partials.global-search')->render();

            if (stripos($content, '</body>') !== false) {
                $content = str_ireplace('</body>', $html . "\n</body>", $content);
            } else {
                $content .= $html;
            }

            $response->setContent($content);
        } catch (\Exception $e) {
            return $response;
        }

        return $response;
    }
}
