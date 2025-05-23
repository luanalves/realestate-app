## Important Note on Testing

Whenever you finish a task, always run the unit tests to ensure that no existing functionality is broken and that your new feature is covered by tests. Use the command below inside the Docker container:

```bash
cd ../realestate-infra && docker compose exec app php artisan test
```

To run a specific test file:

```bash
cd ../realestate-infra && docker compose exec app php artisan test --filter=TestFileName
```

> **Attention:** Do not consider a task complete until you have run the unit tests and confirmed that all tests pass.

# Real Estate App Development Tasks

This file contains a list of features that need to be implemented in the real estate application. GitHub Copilot can use this file to understand what needs to be built and generate the necessary code.

## How to Use This File

1. Add a new task by creating a new section using the template below
2. Fill in all the required fields with detailed information
3. Save the file and ask GitHub Copilot to implement the feature
4. After implementation, mark the task as completed by changing the status

## Task Template

When adding a new task, copy and paste this template and fill it out:

```
### Task: [Task Name]

**Status:** [Pending/In Progress/Completed]
**Priority:** [High/Medium/Low]
**Module:** [ModuleName]
**Feature Type:** [GraphQL Query/GraphQL Mutation/Model/Migration/Seeder/Service/Test]

#### Description
[Detailed description of what needs to be implemented]

#### Requirements
- [Specific requirement 1]
- [Specific requirement 2]
- [Specific requirement 3]

#### GraphQL Schema (if applicable)
```graphql
# Add the GraphQL schema definition here
```

#### Related Files
- [List related files that might need to be modified]

#### Example Usage (if applicable)
```graphql
# Example of how the feature would be used (e.g., GraphQL query/mutation)
```

#### Test Cases
- [Test case 1]
- [Test case 2]

> **Important:** Each task must include its own unit tests covering all critical scenarios. After implementing the task and its tests, always run the full application test suite to ensure nothing else is broken:
>
> ```bash
> cd ../realestate-infra && docker compose exec app php artisan test
> ```

#### Additional Notes
[Any additional information that might be helpful]
```

## Open Tasks

### Task: Implement User Registration Mutation

**Status:** Pending
**Priority:** High
**Module:** UserManagement
**Feature Type:** GraphQL Mutation

#### Description
Create a GraphQL mutation to register new users in the system. This should validate input, create a new user record, assign the appropriate role, and return the created user along with an authentication token.

#### Requirements
- Implement in the UserManagement module
- Validate email, password, name, and role
- Hash the password using Laravel's built-in functions
- Assign the default role based on the registration context (usually 'client')
- Return the created user with an authentication token
- Add proper error handling for validation failures
- Ensure all fields are properly sanitized

#### GraphQL Schema
```graphql
extend type Mutation {
    registerUser(input: RegisterUserInput! @spread): AuthPayload! @field(resolver: "UserManagement\\GraphQL\\Mutations\\RegisterUserMutation")
}

input RegisterUserInput {
    name: String! @rules(apply: ["required", "string", "max:255"])
    email: String! @rules(apply: ["required", "email", "unique:users,email"])
    password: String! @rules(apply: ["required", "string", "min:8", "confirmed"])
    password_confirmation: String!
    role: String @rules(apply: ["string", "in:client"])
}

type AuthPayload {
    user: User!
    token: String!
}
```

#### Related Files
- modules/UserManagement/GraphQL/schema.graphql
- modules/UserManagement/GraphQL/Mutations/RegisterUserMutation.php
- modules/UserManagement/Models/User.php
- modules/UserManagement/Services/UserService.php

#### Example Usage
```graphql
mutation {
  registerUser(
    input: {
      name: "John Doe"
      email: "john.doe@example.com"
      password: "password123"
      password_confirmation: "password123"
    }
  ) {
    user {
      id
      name
      email
    }
    token
  }
}
```

#### Test Cases
- Test successful user registration with valid data
- Test validation errors for invalid email format
- Test validation errors for password too short
- Test validation errors for password confirmation mismatch
- Test unique email constraint validation

> **Important:** Each task must include its own unit tests covering all critical scenarios. After implementing the task and its tests, always run the full application test suite to ensure nothing else is broken:
>
> ```bash
> cd ../realestate-infra && docker compose exec app php artisan test
> ```

#### Additional Notes
This mutation should be accessible without authentication and should follow the OAuth2 flow defined in ADR-0004. The token returned should be a valid OAuth access token generated through Laravel Passport.

### Task: Implement Property Search Query

**Status:** Pending
**Priority:** High
**Module:** Properties
**Feature Type:** GraphQL Query

#### Description
Create a GraphQL query to search for properties with various filtering options. This should allow clients to search properties by location, price range, property type, and other attributes.

#### Requirements
- Implement in the Properties module
- Support pagination of results
- Allow filtering by location (city, neighborhood)
- Allow filtering by price range (min, max)
- Allow filtering by property type (apartment, house, commercial)
- Allow filtering by features (bedrooms, bathrooms, area)
- Return property details including images and real estate contact info
- Support sorting by price, date listed, and relevance

#### GraphQL Schema
```graphql
extend type Query {
    searchProperties(
        filter: PropertyFilterInput @spread
        page: Int = 1
        perPage: Int = 10
        orderBy: [PropertyOrderByInput!] = [{field: CREATED_AT, direction: DESC}]
    ): PropertyPaginator! @field(resolver: "Properties\\GraphQL\\Queries\\SearchPropertiesQuery")
}

input PropertyFilterInput {
    location: LocationFilterInput
    priceRange: PriceRangeInput
    propertyType: [PropertyType!]
    features: PropertyFeaturesInput
    status: PropertyStatus
}

input LocationFilterInput {
    city: String
    neighborhood: String
    zipCode: String
}

input PriceRangeInput {
    min: Float
    max: Float
}

input PropertyFeaturesInput {
    bedrooms: Int
    bathrooms: Int
    minArea: Float
    maxArea: Float
    hasGarage: Boolean
    hasPool: Boolean
}

enum PropertyType {
    APARTMENT
    HOUSE
    COMMERCIAL
    LAND
}

enum PropertyStatus {
    FOR_SALE
    FOR_RENT
    SOLD
    RENTED
}

input PropertyOrderByInput {
    field: PropertyOrderByField!
    direction: SortDirection!
}

enum PropertyOrderByField {
    PRICE
    CREATED_AT
    RELEVANCE
}

enum SortDirection {
    ASC
    DESC
}

type PropertyPaginator {
    data: [Property!]!
    paginatorInfo: PaginatorInfo!
}
```

#### Related Files
- modules/Properties/GraphQL/schema.graphql
- modules/Properties/GraphQL/Queries/SearchPropertiesQuery.php
- modules/Properties/Models/Property.php
- modules/Properties/Services/PropertySearchService.php

#### Example Usage
```graphql
query {
  searchProperties(
    filter: {
      location: {
        city: "São Paulo"
        neighborhood: "Moema"
      }
      priceRange: {
        min: 500000
        max: 1000000
      }
      propertyType: [APARTMENT]
      features: {
        bedrooms: 2
        bathrooms: 2
        minArea: 70
      }
      status: FOR_SALE
    }
    page: 1
    perPage: 10
    orderBy: [{field: PRICE, direction: ASC}]
  ) {
    data {
      id
      title
      description
      price
      address {
        street
        neighborhood
        city
        state
        zipCode
      }
      features {
        bedrooms
        bathrooms
        area
        hasGarage
      }
      images {
        id
        url
        isPrimary
      }
      realEstate {
        id
        name
        phone
        email
      }
    }
    paginatorInfo {
      currentPage
      lastPage
      perPage
      total
      hasMorePages
    }
  }
}
```

#### Test Cases
- Test searching properties with multiple filters
- Test pagination works correctly
- Test sorting by different fields
- Test with no filters returns all properties
- Test with invalid filter values returns appropriate errors

> **Important:** Each task must include its own unit tests covering all critical scenarios. After implementing the task and its tests, always run the full application test suite to ensure nothing else is broken:
>
> ```bash
> cd ../realestate-infra && docker compose exec app php artisan test
> ```

#### Additional Notes
This query should be accessible without authentication for basic property information, but detailed contact information might require authentication. Consider implementing caching for frequent searches.