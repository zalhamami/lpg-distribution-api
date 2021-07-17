<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ApiRequester
{
    /**
     * @return array
     */
    protected function getRequest()
    {
        $request = request();
        $rules = [
            'per_page' => 'integer|min:2|max:50'
        ];
        Validator::validate($request->all(), $rules);

        // Request Pagination
        $per_page = 30;
        if ($request->has('per_page')) {
            $per_page = $request->per_page;
        }

        // Request Sorting
        $sort_by = null;
        if ($request->has('sort_by')) {
            $sort_by = $request->sort_by;
        }
        $sort_direction = null;
        if ($request->has('sort_direction')) {
            $sort_direction = $request->sort_direction;
        }

        $results = [
            'keyword' => $request->keyword,
            'per_page' => $per_page,
            'sort_by' => $sort_by,
            'sort_direction' => $sort_direction
        ];
        return $results;
    }
}
