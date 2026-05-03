# Database Schema

## Entity Relationship Diagram

```mermaid
erDiagram
    users ||--o{ service_jobs : "creates as customer"
    users ||--o{ service_jobs : "assigned as employee"
    users ||--o{ model_has_permissions : ""
    users ||--o{ model_has_roles : ""

    printing_services ||--o{ service_jobs : ""
    technical_services ||--o{ service_jobs : ""

    permissions ||--o{ model_has_permissions : ""
    permissions ||--o{ role_has_permissions : ""

    roles ||--o{ model_has_roles : ""
    roles ||--o{ role_has_permissions : ""

    users {
        biginteger id PK
        string first_name
        string last_name
        string email UK
        string phone
        timestamp email_verified_at
        string password
        text two_factor_secret
        text two_factor_recovery_codes
        timestamp two_factor_confirmed_at
        string title
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    printing_services {
        biginteger id PK
        string name
        text description
        decimal price
        string image
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    technical_services {
        biginteger id PK
        string name
        text description
        decimal price
        string image
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    service_jobs {
        biginteger id PK
        string name
        text description
        string type
        biginteger customer_id FK
        biginteger employee_id FK
        biginteger service_id
        string service_type
        string status
        string priority
        timestamp started_at
        timestamp completed_at
        timestamp deadline
        text notes
        timestamp created_at
        timestamp updated_at
    }

    permissions {
        biginteger id PK
        string name
        string guard_name
        timestamp created_at
        timestamp updated_at
    }

    roles {
        biginteger id PK
        string name
        string guard_name
        timestamp created_at
        timestamp updated_at
    }

    model_has_permissions {
        biginteger permission_id FK
        string model_type
        biginteger model_id
    }

    model_has_roles {
        biginteger role_id FK
        string model_type
        biginteger model_id
    }

    role_has_permissions {
        biginteger permission_id FK
        biginteger role_id FK
    }

    password_reset_tokens {
        string email PK
        string token
        timestamp created_at
    }

    sessions {
        string id PK
        biginteger user_id FK
        string ip_address
        text user_agent
        longtext payload
        integer last_activity
    }

    jobs {
        biginteger id PK
        string queue
        longtext payload
        integer attempts
        integer reserved_at
        integer available_at
        integer created_at
    }

    cache {
        string key PK
        mediumtext value
        integer expiration
    }
```

## Table Descriptions

### users
Stores user accounts including customers and staff (printing/technical). Includes two-factor authentication support and staff title classification.

### printing_services
Catalog of printing services offered with pricing and availability status.

### technical_services
Catalog of technical services offered with pricing and availability status.

### service_jobs
Service requests/jobs linking customers to services, with employee assignments, status tracking, and priority management.

### permissions
Spatie Laravel Permission package - defines granular permissions.

### roles
Spatie Laravel Permission package - defines user roles.

### model_has_permissions
Pivot table linking permissions to models (polymorphic).

### model_has_roles
Pivot table linking roles to models (polymorphic).

### role_has_permissions
Pivot table linking roles to permissions.

### password_reset_tokens
Laravel default - stores password reset tokens.

### sessions
Laravel default - stores user session data.

### jobs
Laravel default - queue job storage.

### cache
Laravel default - cache storage.

## Relationships

- **users → service_jobs**: One user can create multiple service jobs (as customer) and be assigned to multiple jobs (as employee)
- **printing_services → service_jobs**: One printing service can be referenced by multiple service jobs
- **technical_services → service_jobs**: One technical service can be referenced by multiple service jobs
- **users → model_has_roles**: Users can have multiple roles
- **roles → model_has_roles**: Roles can be assigned to multiple users
- **roles → role_has_permissions**: Roles can have multiple permissions
- **permissions → role_has_permissions**: Permissions can be assigned to multiple roles
- **permissions → model_has_permissions**: Permissions can be directly assigned to models
