<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GraphQL Schema Location
    |--------------------------------------------------------------------------
    |
    | This is the path to your GraphQL schema definition file.
    | You may change this path to organize your project better.
    |
    */
    'schema_path' => base_path('graphql/schema.graphql'),
    
    /*
    |--------------------------------------------------------------------------
    | Schema Registration
    |--------------------------------------------------------------------------
    |
    | This array holds the paths to additional GraphQL schema files which are
    | automatically registered if present. Module schemas are registered
    | by their respective ServiceProviders.
    |
    */
    'schema' => [
        'register' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the HTTP route that your GraphQL server responds to.
    |
    */
    'route' => [
        'uri' => '/graphql',
        'middleware' => [
            'api',
            Modules\Security\Http\Middleware\GraphQLLoggingMiddleware::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Controller
    |--------------------------------------------------------------------------
    |
    | Specify which controller handles GraphQL requests.
    |
    */
    'controller' => Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController::class,

    /*
    |--------------------------------------------------------------------------
    | Global ID
    |--------------------------------------------------------------------------
    |
    | Whether to use global IDs for nodes, for Relay compatibility.
    |
    */
    'global_id' => true,

    /*
    |--------------------------------------------------------------------------
    | Pagination Type
    |--------------------------------------------------------------------------
    |
    | The default pagination type to use when pagination is enabled.
    |
    */
    'pagination_type' => 'paginator',

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | Control the debug level for GraphQL.
    |
    */
    'debug' => env('LIGHTHOUSE_DEBUG', env('APP_DEBUG', false)),
];
