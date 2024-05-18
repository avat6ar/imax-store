<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, $next)
  {
    if ($request->hasHeader('Accept-Language'))
    {
      $locale = $request->header('Accept-Language');
      if (in_array($locale, ['ar', 'en', 'fr']))
      {
        app()->setLocale($locale);
      }
    }
    return $next($request);
  }
}
