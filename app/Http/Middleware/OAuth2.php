<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OAuth2
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client_app = DB::table('oauth_clients')->where('id',  $request->client_id)->where('secret', $request->client_secret)->first();
        if(!$client_app){
            return response()->json([
                'code' => 400,
                'success' => false,
                'message' => 'Contact with system administrator for support'
            ]);
        }           

        return $next ($request);
    }
}
