<?php
/*
|--------------------------------------------------------------------------
| AcDevelopers Repository Config
|--------------------------------------------------------------------------
|
|
*/
return [

    /*
    |--------------------------------------------------------------------------
    | Cache Config
    |--------------------------------------------------------------------------
    |
    */
    'cache'      => [
        /*
         |--------------------------------------------------------------------------
         | Cache Status
         |--------------------------------------------------------------------------
         |
         | Enable or disable cache
         |
         */
        'enabled'    => false,

        /*
         |--------------------------------------------------------------------------
         | Cache Minutes
         |--------------------------------------------------------------------------
         |
         | Time of expiration cache
         |
         */
        'minutes'    => 30,

        /*
         |--------------------------------------------------------------------------
         | Cache Repository
         |--------------------------------------------------------------------------
         |
         | Instance of Illuminate\Contracts\Cache\Repository
         |
         */
        'repository' => 'cache',

        /*
          |--------------------------------------------------------------------------
          | Cache Clean Listener
          |--------------------------------------------------------------------------
          |
          |
          |
          */
        'clean'      => [

            /*
              |--------------------------------------------------------------------------
              | Enable clear cache on repository changes
              |--------------------------------------------------------------------------
              |
              */
            'enabled' => true,

            /*
              |--------------------------------------------------------------------------
              | Actions in Repository
              |--------------------------------------------------------------------------
              |
              | create : Clear Cache on create Entry in repository
              | update : Clear Cache on update Entry in repository
              | delete : Clear Cache on delete Entry in repository
              |
              */
            'on'      => [
                'create' => true,
                'update' => true,
                'delete' => true,
            ]
        ],

        'params'     => [
            /*
            |--------------------------------------------------------------------------
            | Skip Cache Params
            |--------------------------------------------------------------------------
            |
            |
            | Ex: http://localhost:8000/?search=lorem&skipCache=true
            |
            */
            'skipCache' => 'skipCache'
        ],

        /*
       |--------------------------------------------------------------------------
       | Methods Allowed
       |--------------------------------------------------------------------------
       |
       | methods cacheable : all, paginate, find, findByField, findWhere, getByCriteria
       |
       | Ex:
       |
       | 'only'  =>['all','paginate'],
       |
       | or
       |
       | 'except'  =>['find'],
       */
        'allowed'    => [
            'only'   => null,
            'except' => null
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Criteria Config
    |--------------------------------------------------------------------------
    |
    | Settings of request parameters names that will be used by Criteria
    |
    */
    'criteria'   => [
        /*
        |--------------------------------------------------------------------------
        | Accepted Conditions
        |--------------------------------------------------------------------------
        |
        | Conditions accepted in consultations where the Criteria
        |
        | Ex:
        |
        | 'acceptedConditions'=>['=','like']
        |
        | $query->where('foo','=','bar')
        | $query->where('foo','like','bar')
        |
        */
        'acceptedConditions' => [
            '=',
            'like'
        ],
        /*
        |--------------------------------------------------------------------------
        | Request Params
        |--------------------------------------------------------------------------
        |
        | Request parameters that will be used to filter the query in the repository
        |
        | Params :
        |
        | - search : Searched value
        |   Ex: http://localhost:8000/?search=lorem
        |
        | - searchFields : Fields in which research should be carried out
        |   Ex:
        |    http://localhost:8000/?search=lorem&searchFields=name;email
        |    http://localhost:8000/?search=lorem&searchFields=name:like;email
        |    http://localhost:8000/?search=lorem&searchFields=name:like
        |
        | - filter : Fields that must be returned to the response object
        |   Ex:
        |   http://localhost:8000/?search=lorem&filter=id,name
        |
        | - orderBy : Order By
        |   Ex:
        |   http://localhost:8000/?search=lorem&orderBy=id
        |
        | - sortedBy : Sort
        |   Ex:
        |   http://localhost:8000/?search=lorem&orderBy=id&sortedBy=asc
        |   http://localhost:8000/?search=lorem&orderBy=id&sortedBy=desc
        |
        | - searchJoin: Specifies the search method (AND / OR), by default the
        |               application searches each parameter with OR
        |   EX:
        |   http://localhost:8000/?search=lorem&searchJoin=and
        |   http://localhost:8000/?search=lorem&searchJoin=or
        |
        */
        'params'             => [
            'search'       => 'search',
            'searchFields' => 'searchFields',
            'filter'       => 'filter',
            'orderBy'      => 'orderBy',
            'sortedBy'     => 'sortedBy',
            'with'         => 'with',
            'searchJoin'   => 'searchJoin'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Generator Config
    |--------------------------------------------------------------------------
    |
    */
    'generator'  => [
        'namespaces'       => [
            'repositories' => '\Repositories',
            'interfaces'   => [
                'repository'    => '\Contracts\Repositories',
                'service'       => '\Contracts\Services',
            ],
            'criteria'     => '\Criteria',
            'providers'    => '\Providers',
            'services'     => '\Services'
        ],

        'provider'  => 'RepositoryServiceProvider'
    ]
];
