<?php

namespace Mrkacmaz\LaravelSsp\Traits;

use Mrkacmaz\LaravelSsp\Exceptions\InvalidModelInstanceException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

trait SSP
{
    /**
     * Process server-side processing (SSP) for an Eloquent model, supporting dynamic filtering, sorting, and pagination.
     *
     * @param Request $request HTTP request object containing SSP parameters.
     * @param string $model Class name of the Eloquent model to be processed.
     * @param Builder|null $query Optional query builder instance to customize queries.
     * @param array|string|null $relation Relationships to eager load.
     * @param bool $encodeData Flag to determine whether to encode the response data.
     * @param bool $isMakeVisibleHiddenAttributes Flag to make hidden model attributes visible.
     * @return array Processed data suitable for SSP responses.
     * @throws InvalidModelInstanceException Throws an exception if the model class is invalid.
     */
    public static function processSSP(Request $request, string $model, Builder $query = null, array|string $relation = null, bool $encodeData = true, bool $isMakeVisibleHiddenAttributes = false): array
    {
        // Instantiate the model to access table and methods
        $modelInstance = new $model;

        // Validate the model instance
        if (!$modelInstance instanceof Model) {
            throw new InvalidModelInstanceException($model);
        }

        // Get the table name from the model to query the database
        $table = $modelInstance->getTable();
        $columns = Schema::getColumnListing($table);

        // Initialize query builder with the model's query method or use the provided query
        $query = $query ?: $modelInstance::query();

        // Optionally load any relationships
        if ($relation) $query->with($relation);

        // Count the total records without any filters
        $totalRecords = $query->count();

        // Apply search filtering if a search value is provided
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($subQuery) use ($columns, $searchValue) {
                foreach ($columns as $column) {
                    $subQuery->orWhere($column, 'like', '%' . $searchValue . '%');
                }
            });
        }

        // Count the filtered records after applying search criteria
        $filteredRecords = $query->count();

        // Apply sorting based on request input
        $orderColumnIndex = $request->input('order.0.column', 0);
        if ($columns[$orderColumnIndex]) {
            $orderColumn = $columns[$orderColumnIndex];
            $orderDirection = $request->input('order.0.dir', 'asc');
            $query->orderBy($orderColumn, $orderDirection);
        }


        // Apply pagination if applicable
        $start = $request->input('start');
        $length = $request->input('length');
        if ($length != -1) {
            $query->skip($start)->take($length);
        }

        // Retrieve the data from the database
        $data = $query->get();

        // Optionally make hidden attributes visible
        if ($isMakeVisibleHiddenAttributes) {
            $data->each(function ($item) {
                $item->makeVisible($item->getHidden());
            });
        }

        // Prepare the response data
        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $encodeData ? base64_encode(json_encode($data)) : $data,
        ];
    }
}