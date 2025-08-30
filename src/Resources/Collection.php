<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends ResourceCollection
{
    public function __construct(
        $resource,
        protected string $keyName,
        protected string $resourceClass
    ) {
        // اگر null بود => collection خالی بساز
        if (is_null($resource)) {
            $resource = collect([]);
        }

        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return [
            $this->keyName.'s' => call_user_func(
                ["App\\Http\\Resources\\" . ucfirst($this->keyName) . '\\' . $this->resourceClass, 'collection'], 
                $this->collection
            ),
            'meta'  => [
                'current_page' => method_exists($this->resource, 'currentPage') ? $this->currentPage() : 1,
                'last_page'    => method_exists($this->resource, 'lastPage') ? $this->lastPage() : 1,
                'per_page'     => method_exists($this->resource, 'perPage') ? $this->perPage() : 0,
                'total'        => method_exists($this->resource, 'total') ? $this->total() : $this->collection->count(),
            ],
            'links' => [
                'first'        => method_exists($this->resource, 'url') ? $this->url(1) : null,
                'last'         => method_exists($this->resource, 'url') ? $this->url($this->lastPage()) : null,
                'next'         => method_exists($this->resource, 'nextPageUrl') ? $this->nextPageUrl() : null,
                'prev'         => method_exists($this->resource, 'previousPageUrl') ? $this->previousPageUrl() : null,
            ],
        ];
    }
}