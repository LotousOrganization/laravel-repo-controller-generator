<?php

namespace SobhanAali\LaravelRepoControllerGenerator\Helper\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    public function __construct(
        $resource ,
        protected string $keyName,
        protected string $resourceClass
    ) {
      
        parent::__construct($resource);

    }
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request)
    {
        return [
            $this->keyName.'s' => call_user_func(
                    ["App\\Http\\Resources\\". ucfirst($this->keyName) . '\\' . $this->resourceClass, 'collection'], 
                    $this->collection
                ),
            'meta'  => [
                'current_page' => $this->currentPage(),
                'last_page'    => $this->lastPage(),
                'per_page'     => $this->perPage(),
                'total'        => $this->total(),
            ],
            'links' => [
                'first'        => $this->url(1),
                'last'         => $this->url($this->lastPage()),
                'next'         => $this->nextPageUrl(),
                'prev'         => $this->previousPageUrl(),
            ],
        ];
    }
}
