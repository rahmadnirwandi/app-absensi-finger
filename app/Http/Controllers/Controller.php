<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function sendSuccess($data = null, $message = null)
    {
        // set default message
        if (empty($message)) {
            $message = __('messages.request.success');
        }

        if (is_array($message)) {
            $extract = array_values($message);
            $message = $extract[0];
        }

        $response['code']       = http_response_code();
        $response['success']    = true;
        $response['message']    = $message;
        $response['content']    = (!is_null($data)) ? $data : null;

        return response()->json($response, http_response_code());
    }
}
