# GraphQL Pagination Pattern

## Problem

When returning paginated data from GraphQL resolvers, there's a specific format that must be followed to match the schema definition. If the resolver returns data in a different format, GraphQL will throw errors such as:

```
Cannot return null for non-nullable field "XxxPaginator.paginatorInfo".
```

This happens because the schema defines a specific structure for paginated responses, but the resolver is returning a Laravel paginator object directly.

## Solution

Always format paginated responses to match the GraphQL schema structure.

### Schema Structure

In our application, paginated data follows this structure:

```graphql
type ExamplePaginator {
    "Pagination metadata"
    paginatorInfo: PaginatorInfo!

    "List of items"
    data: [Example!]!
}

type PaginatorInfo {
    count: Int!
    currentPage: Int!
    firstItem: Int
    hasMorePages: Boolean!
    lastItem: Int
    lastPage: Int!
    perPage: Int!
    total: Int!
}
```

### Correct Implementation

When implementing a resolver for paginated data:

1. Generate the Laravel paginator as usual
2. Transform the paginator into the expected format with `data` and `paginatorInfo` fields

#### Example:

```php
/**
 * Return a paginated list of items.
 *
 * @param mixed $rootValue
 * @param array $args
 * @param GraphQLContext $context
 * @param ResolveInfo $resolveInfo
 * @return array
 */
public function __invoke($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
{
    $query = Model::query();
    
    // Apply any filters, etc.
    
    // Get paginated results
    $paginator = $query->paginate(
        $args['first'] ?? 10,
        ['*'],
        'page',
        $args['page'] ?? 1
    );

    // Return data in the format expected by the GraphQL schema
    return [
        'data' => $paginator->items(),
        'paginatorInfo' => [
            'count' => $paginator->count(),
            'currentPage' => $paginator->currentPage(),
            'firstItem' => $paginator->firstItem(),
            'hasMorePages' => $paginator->hasMorePages(),
            'lastItem' => $paginator->lastItem(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
        ],
    ];
}
```

### Common Mistake

❌ **Incorrect:**

```php
// Incorrect: directly returning the paginator
return $query->paginate($first, ['*'], 'page', $page);
```

✅ **Correct:**

```php
// Correct: formatting the paginator to match GraphQL schema
$paginator = $query->paginate($first, ['*'], 'page', $page);
return [
    'data' => $paginator->items(),
    'paginatorInfo' => [
        'count' => $paginator->count(),
        'currentPage' => $paginator->currentPage(),
        // ...other pagination fields
    ],
];
```

## Benefits

- Consistent GraphQL responses across all paginated queries
- Prevents "Cannot return null for non-nullable field" errors
- Makes the API data structure predictable for frontend developers

## Related Patterns

- GraphQL Query Resolver Pattern
- GraphQL Filtering Pattern
- GraphQL Sorting Pattern
