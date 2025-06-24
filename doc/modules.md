# Modules Documentation Index

This document provides links to documentation for all application modules.

## Available Modules

### 🏢 Organization Module
**Location**: `modules/Organization/`

The Organization module provides a generic foundation for managing organizations of any type, with support for members, addresses, and extensible organization types.

**Documentation**:
- 📖 [Module README](../modules/Organization/README.md) - Overview and development guide
- 🌐 [GraphQL API](../modules/Organization/doc/GraphQL_API.md) - Complete API reference with examples
- 📁 [Documentation Index](../modules/Organization/doc/README.md) - All module documentation

**Key Features**:
- Generic organization management
- Multi-type organization support
- Member management with roles
- Address management
- GraphQL API with full CRUD operations
- OAuth authentication integration

---

### 🏠 Real Estate Module
**Location**: `modules/RealEstate/`

Extends the Organization module to provide real estate specific functionality.

**Documentation**:
- 📖 [Module README](../modules/RealEstate/README.md) - Overview and setup

**Key Features**:
- Real estate organization management
- Integration with Organization module
- Property management capabilities

---

### 👤 User Management Module
**Location**: `modules/UserManagement/`

Handles user authentication, roles, and permissions.

**Documentation**:
- 📖 [Module README](../modules/UserManagement/README.md) - User management guide

**Key Features**:
- User authentication
- Role-based permissions
- OAuth integration
- User profile management

---

### 🔐 BFF Auth Module
**Location**: `modules/BFFAuth/`

Backend for Frontend authentication module.

**Documentation**:
- 📖 [Module README](../modules/BFFAuth/README.md) - Authentication setup

---

### 🛡️ Security Module
**Location**: `modules/Security/`

Security and audit functionality.

**Documentation**:
- 📖 [Module README](../modules/Security/README.md) - Security features

---

## Getting Started

1. **Setup**: Follow the main [project README](../README.md) for initial setup
2. **Authentication**: Use the Organization module documentation for OAuth setup
3. **Development**: Each module follows the same structure and patterns
4. **Testing**: All modules include comprehensive test suites

## Development Guidelines

- All modules follow **English-only** coding standards
- GraphQL-first API development
- Modular architecture with clear boundaries
- Comprehensive testing requirements
- Docker-based development environment

## Quick Links

- 🏗️ [Architectural Decision Records](architectural-decision-records/) - Technical decisions and rationale
- 📋 [Development Tasks](tasks.md) - Current and planned development work
- 📝 [Coding Standards](architectural-decision-records/0006-padroes-de-codigo-e-psr.md) - Code style and quality guidelines

---

**Note**: This documentation is actively maintained. For the most up-to-date information, always refer to the individual module documentation.
