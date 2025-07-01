# UserManagement Module - GraphQL API Documentation

This document provides comprehensive documentation for all GraphQL queries and mutations available in the UserManagement module.

## Table of Contents

1. [Authentication](#authentication)
2. [User Queries](#user-queries)
   - [Get Current User Profile (me)](#1-get-current-user-profile-me)
   - [Get All Users](#2-get-all-users)
   - [Get User by ID](#3-get-user-by-id)
   - [Get All Roles](#4-get-all-roles)
3. [User Management Mutations](#user-management-mutations)
   - [Create User](#1-create-user)
   - [Update User](#2-update-user)
   - [Delete User](#3-delete-user)
4. [Authentication Mutations](#authentication-mutations)
   - [Change Password](#1-change-password)
   - [Request Password Reset](#2-request-password-reset)
   - [Reset Password](#3-reset-password)
   - [~~Login~~ (Deprecated)](#deprecated-authentication-mutations)
5. [Profile Management Mutations](#profile-management-mutations)
   - [Update Profile](#1-update-profile)
   - [Update Preferences](#2-update-preferences)
6. [Error Handling](#error-handling)
7. [Examples](#examples)

## Authentication

**Headless & Stateless Architecture**: This application follows a headless, stateless architecture using JWT token-based authentication via Laravel Passport. There are no server-side sessions or cookies - all authentication state is managed through access tokens.

### Getting an Access Token

To obtain an access token, make a POST request to the OAuth endpoint `/oauth/token`:

```bash
curl -X POST http://realestate.localhost/oauth/token \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=password&client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&username=user@example.com&password=password123"
```

> **Important**: The GraphQL mutation `login` has been deprecated and removed. Authentication should always be performed through the OAuth endpoint above, particularly when implementing a Backend-for-Frontend (BFF) architecture.

### Using the Access Token

Most GraphQL operations in the UserManagement module require authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer YOUR_ACCESS_TOKEN
```

Some operations like `requestPasswordReset` and `resetPassword` do not require authentication.

### Architecture Characteristics

- **Headless**: No server-rendered views or session management
- **Stateless**: Each request contains all necessary authentication information
- **Token-based**: Uses JWT tokens for authentication (no cookies or server sessions)
- **API-first**: Designed for consumption by decoupled frontends (SPA, mobile apps, etc.)
- **Scalable**: Stateless design enables horizontal scaling without session affinity

### Cache and Performance Notes

This module implements a cache-first strategy for queries that access relatively stable data.

#### Query `me` - Technical Implementation

The `me` query uses **Redis cache with database fallback** for optimal performance. Here's how it works:

1. **Token Validation**: Laravel Passport validates the JWT token in the Authorization header
2. **User Resolution**: `Auth::guard('api')->user()` extracts user_id from token
3. **Cache-First Strategy**: `UserService::getUserById()` checks Redis cache first, then database if needed
4. **Response**: User data with role relationship returned from cache or database

**Performance Characteristics:**
- **First request**: ~50-100ms (database query + cache storage)
- **Cached requests**: ~1-5ms (Redis lookup)
- **Performance improvement**: ~95% reduction in response time

**Cache Implementation Details:**
- Redis is configured as the default cache store
- UserService uses CachedUserRepository automatically
- TTL: 15 minutes for user data
- Cache keys: `user_management:id:{user_id}`
- Automatic cache invalidation on user updates/deletes
- Safe fallback to database if cache fails

**Cache Benefits:**
- Significantly faster response times for frequent profile requests
- Reduced database load
- Automatic cache invalidation ensures data consistency
- Graceful degradation if Redis is unavailable

#### Query `roles` - Technical Implementation

The `roles` query also uses **Redis cache with database fallback** since role data changes very infrequently:

1. **Authentication**: Token validation followed by permission check
2. **Cache Check**: Attempts to retrieve roles from Redis cache
3. **Cache Miss Strategy**: If no cached data exists, query database and store in cache
4. **Fallback**: Graceful degradation to direct database query if cache operation fails
5. **Long TTL**: 24-hour cache lifetime to minimize database access

**Performance Characteristics:**
- **First request**: ~20-50ms (database query + cache storage)
- **Cached requests**: ~1-3ms (Redis lookup)
- **Cache invalidation**: Automatic on role creation, update, or deletion

**Benefits:**
- Minimal database load for this frequently accessed reference data
- Significantly faster API responses
- High availability through fallback mechanism

---

## User Queries

### 1. Get Current User Profile (me)

Retrieve the profile information of the currently authenticated user.

**Authentication Required:** Yes

**Query:**
```graphql
query {
  me {
    id
    name
    email
    role {
      id
      name
      description
    }
    preferences
    tenant_id
    created_at
    updated_at
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "query { me { id name email role { id name description } preferences tenant_id created_at updated_at } }"
  }'
```

**Response:**
```json
{
  "data": {
    "me": {
      "id": "1",
      "name": "John Doe",
      "email": "john@example.com",
      "role": {
        "id": "2",
        "name": "client",
        "description": "Cliente da imobiliária"
      },
      "preferences": {
        "darkMode": true,
        "notifications": {
          "email": true,
          "push": false
        }
      },
      "tenant_id": "1",
      "created_at": "2025-06-29T10:00:00Z",
      "updated_at": "2025-06-29T15:30:00Z"
    }
  }
}
```

### 2. Get All Users

Retrieve a list of all users in the system.

**Authentication Required:** Yes (Admin roles only)

**Query:**
```graphql
query {
  users {
    id
    name
    email
    role {
      id
      name
    }
    tenant_id
    created_at
    updated_at
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "query": "query { users { id name email role { id name } tenant_id created_at updated_at } }"
  }'
```

### 3. Get User by ID

Retrieve a specific user by their ID.

**Authentication Required:** Yes (Admin roles only)

**Query:**
```graphql
query($id: ID!) {
  user(id: $id) {
    id
    name
    email
    role {
      id
      name
      description
    }
    preferences
    tenant_id
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "id": "1"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "query": "query($id: ID!) { user(id: $id) { id name email role { id name description } preferences tenant_id created_at updated_at } }",
    "variables": { "id": "1" }
  }'
```

### 4. Get All Roles

Retrieve all available roles in the system.

**Authentication Required:** Yes

**Technical Notes:** This query uses Redis cache to improve performance for this infrequently changing data.

**Query:**
```graphql
query {
  roles {
    id
    name
    description
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "query { roles { id name description } }"
  }'
```

**Response Example:**
```json
{
  "data": {
    "roles": [
      {
        "id": "1",
        "name": "super_admin",
        "description": "Administrador geral do sistema"
      },
      {
        "id": "2",
        "name": "real_estate_admin",
        "description": "Administrador da imobiliária"
      },
      {
        "id": "3",
        "name": "real_estate_agent",
        "description": "Funcionário da imobiliária"
      },
      {
        "id": "4",
        "name": "client",
        "description": "Cliente da imobiliária"
      }
    ]
  }
}
```

**Cache Implementation Details:**
- Redis cache with 1-day TTL
- Cache key: `user_management_roles`
- Automatic fallback to database if cache fails
- Optimized for environments with high read, low write volume

---

## User Management Mutations

### 1. Create User

Create a new user in the system.

**Authentication Required:** Yes (Admin roles only)

**Mutation:**
```graphql
mutation($input: CreateUserInput!) {
  createUser(input: $input) {
    id
    name
    email
    role {
      id
      name
    }
    created_at
    updated_at
  }
}
```

**Variables:**
```json
{
  "input": {
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "securePassword123",
    "role_id": "2"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN" \
  -d '{
    "query": "mutation($input: CreateUserInput!) { createUser(input: $input) { id name email role { id name } created_at updated_at } }",
    "variables": {
      "input": {
        "name": "Jane Smith",
        "email": "jane@example.com",
        "password": "securePassword123",
        "role_id": "2"
      }
    }
  }'
```

### 2. Update User

Update an existing user's information.

**Authentication Required:** Yes (Admin roles only)

**Mutation:**
```graphql
mutation($id: ID!, $input: UpdateUserInput!) {
  updateUser(id: $id, input: $input) {
    id
    name
    email
    role {
      id
      name
    }
    updated_at
  }
}
```

**Variables:**
```json
{
  "id": "1",
  "input": {
    "name": "John Updated",
    "email": "john.updated@example.com"
  }
}
```

### 3. Delete User

Delete a user from the system.

**Authentication Required:** Yes (Admin roles only)

**Mutation:**
```graphql
mutation($id: ID!) {
  deleteUser(id: $id) {
    success
    message
  }
}
```

**Variables:**
```json
{
  "id": "1"
}
```

---

## Authentication Mutations

### 1. Change Password

Change the password for the currently authenticated user.

**Authentication Required:** Yes

**Mutation:**
```graphql
mutation($currentPassword: String!, $newPassword: String!, $newPasswordConfirmation: String!) {
  changePassword(
    current_password: $currentPassword,
    new_password: $newPassword,
    new_password_confirmation: $newPasswordConfirmation
  ) {
    success
    message
  }
}
```

**Variables:**
```json
{
  "currentPassword": "oldPassword123",
  "newPassword": "newPassword456",
  "newPasswordConfirmation": "newPassword456"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "mutation($currentPassword: String!, $newPassword: String!, $newPasswordConfirmation: String!) { changePassword(current_password: $currentPassword, new_password: $newPassword, new_password_confirmation: $newPasswordConfirmation) { success message } }",
    "variables": {
      "currentPassword": "oldPassword123",
      "newPassword": "newPassword456",
      "newPasswordConfirmation": "newPassword456"
    }
  }'
```

### 2. Request Password Reset

Request a password reset link to be sent to the user's email.

**Authentication Required:** No

**Mutation:**
```graphql
mutation($email: String!) {
  requestPasswordReset(email: $email) {
    success
    message
  }
}
```

**Variables:**
```json
{
  "email": "john@example.com"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "mutation($email: String!) { requestPasswordReset(email: $email) { success message } }",
    "variables": {
      "email": "john@example.com"
    }
  }'
```

### 3. Reset Password

Reset the user's password using a token received via email.

**Authentication Required:** No

**Mutation:**
```graphql
mutation($email: String!, $token: String!, $password: String!, $passwordConfirmation: String!) {
  resetPassword(
    email: $email,
    token: $token,
    password: $password,
    password_confirmation: $passwordConfirmation
  ) {
    success
    message
  }
}
```

**Variables:**
```json
{
  "email": "john@example.com",
  "token": "abcdef123456789",
  "password": "newSecurePassword123",
  "passwordConfirmation": "newSecurePassword123"
}
```

### Deprecated Authentication Mutations

#### Login (Removed)

> **Note**: The `login` mutation has been removed from the GraphQL schema. Use the OAuth endpoint `/oauth/token` directly instead.
>
> For architectural best practices, authentication should be handled by a Backend-for-Frontend (BFF) layer that securely manages OAuth client credentials and tokens, rather than exposing this functionality through GraphQL.
>
> See the [Authentication](#authentication) section for details on how to properly authenticate.

---

## Profile Management Mutations

### 1. Update Profile

Update the authenticated user's profile information.

**Authentication Required:** Yes

**Mutation:**
```graphql
mutation($name: String, $email: String) {
  updateProfile(name: $name, email: $email) {
    success
    message
    user {
      id
      name
      email
      updated_at
    }
  }
}
```

**Variables:**
```json
{
  "name": "John Updated",
  "email": "john.new@example.com"
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "mutation($name: String, $email: String) { updateProfile(name: $name, email: $email) { success message user { id name email updated_at } } }",
    "variables": {
      "name": "John Updated",
      "email": "john.new@example.com"
    }
  }'
```

### 2. Update Preferences

Update the authenticated user's preferences.

**Authentication Required:** Yes

**Mutation:**
```graphql
mutation($preferences: JSON!) {
  updatePreferences(preferences: $preferences) {
    success
    message
    preferences
  }
}
```

**Variables:**
```json
{
  "preferences": {
    "darkMode": true,
    "notifications": {
      "email": true,
      "push": false
    },
    "language": "pt-BR"
  }
}
```

**cURL Example:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -d '{
    "query": "mutation($preferences: JSON!) { updatePreferences(preferences: $preferences) { success message preferences } }",
    "variables": {
      "preferences": {
        "darkMode": true,
        "notifications": {
          "email": true,
          "push": false
        },
        "language": "pt-BR"
      }
    }
  }'
```

---

## Error Handling

### Common Error Responses

#### Authentication Error
```json
{
  "errors": [
    {
      "message": "Unauthenticated.",
      "extensions": {
        "category": "authentication"
      }
    }
  ]
}
```

#### Authorization Error
```json
{
  "errors": [
    {
      "message": "You do not have permission to access user management",
      "extensions": {
        "category": "authorization"
      }
    }
  ]
}
```

#### Validation Error
```json
{
  "errors": [
    {
      "message": "Validation failed for the field [createUser].",
      "extensions": {
        "validation": {
          "input.email": [
            "The email field is required."
          ],
          "input.password": [
            "The password must be at least 8 characters."
          ]
        }
      }
    }
  ]
}
```

#### Password Change Error
```json
{
  "data": {
    "changePassword": {
      "success": false,
      "message": "Your current password is incorrect."
    }
  }
}
```

---

## Examples

### Complete User Registration Flow

1. **Admin creates a user:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -d '{
    "query": "mutation { createUser(input: { name: \"New User\", email: \"newuser@example.com\", password: \"password123\", role_id: \"2\" }) { id name email } }"
  }'
```

2. **User obtains an OAuth token:**
```bash
curl -X POST http://realestate.localhost/oauth/token \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "grant_type=password&client_id=YOUR_CLIENT_ID&client_secret=YOUR_CLIENT_SECRET&username=newuser@example.com&password=password123"
```

3. **User updates their profile:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer USER_TOKEN" \
  -d '{
    "query": "mutation { updateProfile(name: \"Updated Name\") { success user { name } } }"
  }'
```

### Password Reset Flow

1. **Request password reset:**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "mutation { requestPasswordReset(email: \"user@example.com\") { success message } }"
  }'
```

2. **Reset password with token (received via email):**
```bash
curl -X POST http://realestate.localhost/graphql \
  -H "Content-Type: application/json" \
  -d '{
    "query": "mutation { resetPassword(email: \"user@example.com\", token: \"RESET_TOKEN\", password: \"newPassword123\", password_confirmation: \"newPassword123\") { success message } }"
  }'
```

---

## Notes

### Technical Architecture

- **Headless & Stateless**: This application is designed as a headless, stateless API
- **No Server Sessions**: Authentication relies entirely on JWT tokens - no server-side sessions
- **Token-Based Authentication**: All user state is contained within JWT tokens
- **Frontend Agnostic**: Can be consumed by any client (SPA, mobile apps, serverless functions)
- **Horizontal Scalability**: Stateless design enables easy scaling across multiple servers

### Data Format & Validation

- All timestamps are returned in ISO 8601 format
- The `preferences` field accepts any valid JSON object
- Password requirements: minimum 8 characters
- Email addresses must be unique across the system
- Role IDs correspond to roles defined in the `roles` table
- The `tenant_id` field is used for multi-tenant functionality (restricting user access to specific organizations)

For more information about roles and permissions, refer to the Authorization Service Pattern documentation.
