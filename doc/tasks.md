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