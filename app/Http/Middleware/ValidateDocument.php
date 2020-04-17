<?php

namespace App\Http\Middleware;

use App\Models\Admin\Project\Document\Collection;
use Closure;
use Illuminate\Support\Facades\Auth;

class ValidateDocument
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set file request library
        config(['documentservice.file_request.library' => 'App\Libraries\Consumer\FileRequestUtils']);

        $guid = $request->route()->parameter('GUID');

        // If user is logged in
        if (Auth::guard('document')->check() && (($guid ?? '') != '')) {
            $collection = Collection::where('GUID', $guid)->first();

            // If collection exists
            if ($collection) {
                // If user doesn't exists inside collection
                if (!$collection->hasContact(Auth::guard('document')->user()->ID)) {
                    return abort(403);
                }
                // If page is expired for user
                else if ($collection->isExpiredForContact(Auth::guard('document')->user()->ID)) {
                    return abort(410);
                }
                else {
                    // Remember collection guid
                    session(['document_collection_guid' => $guid]);

                    // Continue to shared document page
                    return $next($request);
                }
            } else {
                return abort(404);
            }
        } else {
            // Continue to login
            return $next($request);
        }
    }
}