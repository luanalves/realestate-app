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
    'schema_path' => base_path('modules/UserManagement/GraphQL/schema.graphql'),

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
        'middleware' => ['api'],
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
