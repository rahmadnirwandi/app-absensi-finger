<?php
namespace App\Traits;

trait ResponseTrait
{
    /**
     * Success response formatter
     *
     * @uses return error response
     * @param string  $message
     * @param mixed  $data
     *
     * @return array|object
     */
    public function sendSuccess($data = null, $message = null)
    {
        $message = (empty($message)) ? trans('messages.request.success') : $message;

        // check if message constructed in array format (multiple message)
        if (is_array($message)) {
            $extract = array_values($message);
            $message = $extract[0];
        }

        $response = [
            'code'      => http_response_code(),
            'success'   => true,
            'message'   => $message,
            'data'      => $data
        ];

        return $this->castToObject($response);
    }

    /**
     * Error response formatter
     *
     * @uses return error response
     * @param string  $message
     * @param mixed  $data
     *
     * @return array|object
     */
    public function sendError($data = null, $message = null)
    {
        $message = (empty($message)) ? trans('messages.request.error') : $message;

        // check if message constructed in array format (multiple message)
        if (is_array($message)) {
            $extract = array_values($message);
            $message = $extract[0];
        }

        $response = [
            'code'      => http_response_code(),
            'success'   => false,
            'message'   => $message,
            'data'      => $data
        ];

        return $this->castToObject($response);
    }

    /**
     * Private function message
     * @param string|array  $message
     * @return string|array
     */
    private function message($message)
    {
        $message = (empty($message)) ? __('messages.request.success') : $message;

        // check if message constructed in array format (multiple message)
        if (is_array($message)) {
            $extract = array_values($message);
            $message = $extract[0];
        }

        return $message;
    }

    /**
     * Cast data into object
     * @param $response
     * @return object
     */
    private function castToObject($response)
    {
        return (object) $response;
    }
}
