<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Validation\ValidationException;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $transformer)
    {
        $originalInputs = [];

        foreach ($request->request->all() as $input => $value) {
            $originalInputs[$transformer::originalAttribute($input)] = $value;
        }

        $request->replace($originalInputs);

        $response = $next($request);

        if (isset($response->exception) && $response->exception instanceOf ValidationException) {
            $data = $response->getData();

            $transformedErrors = [];

            foreach ($data->errors as $field => $value) {
                $transformedField = $transformer::transformedAttribute($field);

                $transformedErrors[$transformedField] = str_replace($field, $transformedField, $value);
            }

            $data->errors = $transformedErrors;

            $response->setData($data);
        }

        return $response;
    }
}
