<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class BaseApiController extends Controller
{
    /**
     * Return response
     *
     * @param JsonResource $jsonResource
     * @param integer $httpCode
     * @param string $message
     * @param boolean $status
     * @return Json
     */
    protected function responseJson(JsonResource $jsonResource, $httpCode = 200, $message = 'Success', $status = true) 
    {   
        $meta = [
            'current_page' => '',
            'from' => '',
            'last_page' => '',
            'path' => '',
            'per_page' => '',
            'to' => '',
            'total' => ''
        ];

        $links = [
            'first' => '',
            'last' => '',
            'prev' => '',
            'next' => ''
        ];

        if(is_null($jsonResource->resource)) {
            $data = [];
            $meta = [];
            $links = [];
        } else {    
            $data = $jsonResource;
            $meta = Arr::only($jsonResource->resource->toArray(), array_keys($meta));
            $links = Arr::only($jsonResource->resource->toArray(), ['first_page_url', 'last_page_url', 'prev_page_url', 'next_page_url']);
        }

        $result = array(
            'status' => $status,
            'message' => $message,
            'http_code' => $httpCode,
            'data' => $data,
            'links' => (object) $links,
            'meta' => (object) $meta
        );

        return $result;
    }

    /**
     * Return response
     *
     * @param array $jsonResource
     * @param integer $httpCode
     * @param string $message
     * @param boolean $status
     * @return Json
     */
    protected function responseJsonFromArray($jsonResource = [], $httpCode = 200, $message = 'Success', $status = true) 
    {   
        $result = array(
            'status' => $status,
            'message' => $message,
            'http_code' => $httpCode,
            'data' => (object) $jsonResource,
            'links' => (object) [],
            'meta' => (object) []
        );

        return $result;
    }
}
