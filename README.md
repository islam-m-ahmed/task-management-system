# 📋 Task Management System API

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

**A professional, enterprise-grade RESTful API for Task Management**

[Features](#-key-features) • [Installation](#-installation--setup) • [API Docs](#-api-endpoints) • [ERD](#-database-schema) • [Postman](#-postman-collection)

</div>

---

## 📌 Overview

This Task Management System is a robust RESTful API built with **Laravel 12** using a scalable **N-Tier Architecture** (Service-Repository pattern). It provides complete task lifecycle management with role-based access control, task dependencies, and comprehensive filtering capabilities.

---

## ✨ Key Features

| Feature | Description |
|---------|-------------|
| 🔐 **Authentication** | Stateless token-based auth using Laravel Sanctum |
| 👥 **Role-Based Access Control** | Granular permissions via Spatie Laravel Permission |
| 📋 **Task Management** | Full CRUD operations with status tracking |
| 🔗 **Task Dependencies** | Smart dependency graph with circular detection |
| 🔍 **Advanced Filtering** | Filter by status, date range, and assigned user |
| 📊 **API Versioning** | Future-proof design with `/api/v1/` prefix |
| ✅ **Data Validation** | Strict Form Request validation on all endpoints |
| 🛡️ **Error Handling** | Centralized error responses with proper HTTP codes |

---

## 🏗️ Architecture Design

The application implements a refined **N-Tier Architecture** designed for enterprise-scale development:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         PRESENTATION LAYER                               │
│  Controllers (V1) → Middleware → Form Requests → API Resources          │
├─────────────────────────────────────────────────────────────────────────┤
│                         BUSINESS LOGIC LAYER                             │
│  Services (TaskService, AuthService) → Business Rules & Validation       │
├─────────────────────────────────────────────────────────────────────────┤
│                         DATA ACCESS LAYER                                │
│  Repositories (TaskRepository, UserRepository) → Eloquent Queries        │
├─────────────────────────────────────────────────────────────────────────┤
│                         DATABASE LAYER                                   │
│  MySQL → Migrations → Seeders                                            │
└─────────────────────────────────────────────────────────────────────────┘
```

### Project Structure

```
app/
├── Architecture/
│   ├── Repositories/           # Data Access Layer
│   │   ├── Interfaces/         # Repository contracts
│   │   └── Classes/            # Eloquent implementations
│   ├── Services/               # Business Logic Layer
│   │   ├── Interfaces/         # Service contracts
│   │   └── Classes/            # Business rules
│   ├── Responder/              # Unified API Response handling
│   └── Injector/               # Dependency Injection providers
├── Enums/
│   └── TaskStatus.php          # Task status enumeration
├── Http/
│   ├── Controllers/Api/V1/     # Versioned API Controllers
│   ├── Middleware/             # Custom middleware (TaskAccess)
│   ├── Requests/               # Form Request validation
│   └── Resources/              # API Resource transformers
├── Models/                     # Eloquent Models
└── Providers/                  # Service Providers
```

---

## 🔐 Role-Based Access Control (RBAC)

### Implementation with Spatie Laravel Permission

| Role | Permissions | Description |
|------|-------------|-------------|
| **Manager** | `task:view-all`, `task:create`, `task:update`, `task:assign`, `task:update-status` | Full access to all tasks |
| **User** | `task:view-assigned`, `task:update-status` | Limited to assigned tasks only |

### Authorization Matrix

| Action | Manager | User |
|--------|:-------:|:----:|
| View all tasks | ✅ | ❌ |
| View assigned tasks | ✅ | ✅ |
| Create task | ✅ | ❌ |
| Update task details | ✅ | ❌ |
| Assign task to user | ✅ | ❌ |
| Update task status | ✅ | ✅ (own tasks only) |
| Add dependencies | ✅ | ❌ |

---

## 🚀 Installation & Setup

### Prerequisites

- **PHP** 8.2 or higher
- **Composer** 2.x
- **MySQL** 8.0 or higher
- **Git**

### Step 1: Clone & Install Dependencies

```bash
git clone <repository-url>
cd Task-management-system
composer install
```

### Step 2: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### Step 3: Database Setup

Update your `.env` file with database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations and seeders:

```bash
php artisan migrate:fresh --seed
```

### Step 4: Start the Server

```bash
php artisan serve
```

🎉 **API is now running at:** `http://localhost:8000/api/v1`

---

## 🔑 Test Credentials

| Role | Email | Password |
|------|-------|----------|
| 👔 Manager | `manager@example.com` | `password123` |
| 👔 Manager | `jane.manager@example.com` | `password123` |
| 👤 User | `user@example.com` | `password123` |
| 👤 User | `alice.user@example.com` | `password123` |
| 👤 User | `charlie.user@example.com` | `password123` |

---

## 📡 API Endpoints

**Base URL:** `http://localhost:8000/api/v1`

### 🔐 Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|:-------------:|
| `POST` | `/auth/login` | Login and receive access token | ❌ |
| `POST` | `/auth/logout` | Revoke current access token | ✅ |

### 📋 Task Endpoints

| Method | Endpoint | Description | Role |
|--------|----------|-------------|------|
| `GET` | `/tasks` | List all tasks (with filters) | Manager: All, User: Own |
| `GET` | `/tasks/{id}` | Get task details with dependencies | Any (with access) |
| `POST` | `/tasks` | Create a new task | Manager only |
| `PUT` | `/tasks/{id}` | Update task details | Manager only |
| `PATCH` | `/tasks/{id}/status` | Update task status | Assigned user |
| `POST` | `/tasks/{id}/dependencies` | Add task dependency | Manager only |

### 🔍 Query Parameters for `GET /tasks`

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `status` | string | Filter by status | `pending`, `in_progress`, `completed`, `canceled` |
| `due_date_from` | date | Filter from date | `2026-01-01` |
| `due_date_to` | date | Filter until date | `2026-12-31` |
| `assigned_user_id` | integer | Filter by assigned user | `3` |


---

## 📝 Complete API Documentation

### 1️⃣ Login

Authenticate and receive an access token.

**Request:**
```http
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "manager@example.com",
    "password": "password123"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Manager",
            "email": "manager@example.com",
            "roles": ["manager"],
            "created_at": "2026-02-08T10:00:00.000000Z"
        },
        "access_token": "1|abc123xyz789...",
        "token_type": "Bearer"
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Invalid credentials",
    "errors": []
}
```

---

### 2️⃣ Logout

Revoke the current access token.

**Request:**
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully from current session",
    "data": []
}
```

---

### 3️⃣ List All Tasks

Get all tasks with optional filters. Managers see all tasks, Users see only their assigned tasks.

**Request:**
```http
GET /api/v1/tasks
Authorization: Bearer {token}
```

**With Filters:**
```http
GET /api/v1/tasks?status=pending&due_date_from=2026-01-01&due_date_to=2026-12-31&assigned_user_id=3&per_page=10
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Tasks retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Complete API Documentation",
            "description": "Write comprehensive API docs",
            "status": "pending",
            "due_date": "2026-03-15",
            "created_at": "2026-02-08T10:00:00.000000Z"
        },
        {
            "id": 2,
            "title": "Review Code Changes",
            "description": "Review PR #42",
            "status": "in_progress",
            "due_date": "2026-02-20",
            "created_at": "2026-02-07T14:30:00.000000Z"
        }
    ]
}
```

---

### 4️⃣ Get Task Details

Get a specific task with its dependencies.

**Request:**
```http
GET /api/v1/tasks/1
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Task retrieved successfully",
    "data": {
        "id": 1,
        "title": "Deploy to Production",
        "description": "Deploy the application to production server",
        "status": "pending",
        "due_date": "2026-03-01",
        "assigned_user": {
            "id": 3,
            "name": "Bob User",
            "email": "user@example.com"
        },
        "created_by": {
            "id": 1,
            "name": "John Manager",
            "email": "manager@example.com"
        },
        "dependencies": [
            {
                "id": 2,
                "title": "Complete Testing",
                "status": "in_progress"
            },
            {
                "id": 3,
                "title": "Code Review",
                "status": "completed"
            }
        ],
        "dependents": [
            {
                "id": 5,
                "title": "Send Release Notes",
                "status": "pending"
            }
        ],
        "created_at": "2026-02-08T10:00:00.000000Z"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Task not found",
    "errors": []
}
```

**Error Response (403 - User accessing unassigned task):**
```json
{
    "success": false,
    "message": "You do not have access to this task",
    "errors": []
}
```

---

### 5️⃣ Create Task

Create a new task. **Manager only.**

**Request:**
```http
POST /api/v1/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Complete API Documentation",
    "description": "Write comprehensive API docs for the project",
    "due_date": "2026-03-15",
    "assigned_user_id": 3
}
```

**Validation Rules:**
| Field | Rules |
|-------|-------|
| `title` | Required, string, max 255 characters |
| `description` | Optional, string, max 5000 characters |
| `due_date` | Optional, valid date, must be today or future |
| `assigned_user_id` | Optional, must exist in users table |

**Success Response (201):**
```json
{
    "success": true,
    "message": "Task created successfully",
    "data": {
        "id": 10,
        "title": "Complete API Documentation",
        "description": "Write comprehensive API docs for the project",
        "status": "pending",
        "due_date": "2026-03-15",
        "assigned_user": {
            "id": 3,
            "name": "Bob User",
            "email": "user@example.com"
        },
        "created_by": {
            "id": 1,
            "name": "John Manager",
            "email": "manager@example.com"
        },
        "created_at": "2026-02-08T14:30:00.000000Z"
    }
}
```

**Validation Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "title": ["Task title is required"],
        "due_date": ["Due date must be today or in the future"],
        "assigned_user_id": ["The selected user does not exist"]
    }
}
```

---

### 6️⃣ Update Task

Update task details. **Manager only.**

**Request:**
```http
PUT /api/v1/tasks/1
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Task Title",
    "description": "Updated description",
    "due_date": "2026-04-01",
    "assigned_user_id": 4,
    "status": "in_progress"
}
```

**Validation Rules:**
| Field | Rules |
|-------|-------|
| `title` | Optional (if provided: required, string, max 255) |
| `description` | Optional, string, max 5000 characters |
| `due_date` | Optional, valid date |
| `assigned_user_id` | Optional, must exist in users table |
| `status` | Optional, must be: `pending`, `in_progress`, `completed`, `canceled` |

**Success Response (200):**
```json
{
    "success": true,
    "message": "Task updated successfully",
    "data": {
        "id": 1,
        "title": "Updated Task Title",
        "description": "Updated description",
        "status": "in_progress",
        "due_date": "2026-04-01",
        "assigned_user": {
            "id": 4,
            "name": "Alice User",
            "email": "alice.user@example.com"
        },
        "created_by": {
            "id": 1,
            "name": "John Manager",
            "email": "manager@example.com"
        },
        "dependencies": [],
        "dependents": [],
        "created_at": "2026-02-08T10:00:00.000000Z"
    }
}
```

---

### 7️⃣ Update Task Status

Update only the status of a task. **Available for assigned users on their own tasks.**

**Request:**
```http
PATCH /api/v1/tasks/1/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "completed"
}
```

**Validation Rules:**
| Field | Rules |
|-------|-------|
| `status` | Required, must be: `pending`, `in_progress`, `completed`, `canceled` |

**Success Response (200):**
```json
{
    "success": true,
    "message": "Task status updated successfully",
    "data": {
        "id": 1,
        "title": "Complete API Documentation",
        "status": "completed",
        "due_date": "2026-03-15",
        "assigned_user": {
            "id": 3,
            "name": "Bob User",
            "email": "user@example.com"
        },
        "created_by": {
            "id": 1,
            "name": "John Manager",
            "email": "manager@example.com"
        },
        "created_at": "2026-02-08T10:00:00.000000Z"
    }
}
```

**Error Response (422 - Dependencies not completed):**
```json
{
    "success": false,
    "message": "Blocking Dependencies: Prerequisite tasks must be COMPLETED before this task can be finished.",
    "errors": []
}
```

---

### 8️⃣ Add Task Dependency

Add a dependency relationship between tasks. **Manager only.**

**Request:**
```http
POST /api/v1/tasks/5/dependencies
Authorization: Bearer {token}
Content-Type: application/json

{
    "depends_on_task_id": 3
}
```

This means: **Task 5 depends on Task 3** (Task 3 must be completed before Task 5 can be completed).

**Validation Rules:**
| Field | Rules |
|-------|-------|
| `depends_on_task_id` | Required, integer, must exist in tasks table |

**Success Response (200):**
```json
{
    "success": true,
    "message": "Dependency mapped successfully",
    "data": {
        "id": 5,
        "title": "Deploy to Production",
        "status": "pending",
        "dependencies": [
            {
                "id": 3,
                "title": "Complete Testing",
                "status": "in_progress"
            }
        ],
        "dependents": []
    }
}
```

**Error Response (422 - Self dependency):**
```json
{
    "success": false,
    "message": "A task cannot depend on itself",
    "errors": []
}
```

**Error Response (422 - Circular dependency):**
```json
{
    "success": false,
    "message": "Adding this dependency would create a circular loop",
    "errors": []
}
```

**Error Response (409 - Duplicate dependency):**
```json
{
    "success": false,
    "message": "This dependency relationship already exists",
    "errors": []
}
```

---

## ⚙️ Business Rules

### Task Status Flow

```
┌─────────┐    ┌─────────────┐    ┌───────────┐
│ PENDING │───▶│ IN_PROGRESS │───▶│ COMPLETED │
└─────────┘    └─────────────┘    └───────────┘
      │              │                   
      ▼              ▼                   
┌─────────┐    ┌─────────┐              
│CANCELED │    │CANCELED │              
└─────────┘    └─────────┘              
```

### Task Status Values

| Status | Description |
|--------|-------------|
| `pending` | Task is created but not started |
| `in_progress` | Task is currently being worked on |
| `completed` | Task is finished (requires all dependencies completed) |
| `canceled` | Task has been canceled |

### Dependency Rules

1. **Completion Blocking**: A task cannot be marked as `completed` if any of its dependencies are not yet `completed`.
2. **Circular Prevention**: The system prevents circular dependencies (Task A → Task B → Task A).
3. **Self-Reference Prevention**: A task cannot depend on itself.
4. **Duplicate Prevention**: The same dependency relationship cannot be added twice.

---

## 🛠️ Tech Stack

| Technology | Purpose |
|------------|---------|
| **Laravel 12** | PHP Framework |
| **Laravel Sanctum** | API Authentication |
| **Spatie Permission** | Role-Based Access Control |
| **MySQL 8** | Database |
| **PHP 8.2+** | Runtime |

---

## 📬 Postman Collection

A Postman collection is included in the project root: `Task_Management_API.postman_collection.json`

### Import Collection

1. Open Postman
2. Click **Import** → Select the `Task_Management_API.postman_collection.json` file
3. The collection will be imported with all API endpoints

### 🔑 Automatic Token Extraction

To automatically save the access token after login, add this script in the **Tests** tab of your Login request:

```javascript
// Parse the JSON response
var jsonData = pm.response.json();

// Check if login was successful and token exists
if (jsonData.code === 200 && jsonData.data && jsonData.data.access_token) {
    // Combine token_type and access_token into one variable
    var fullToken = jsonData.data.token_type + " " + jsonData.data.access_token;
    
    pm.collectionVariables.set("access_token", fullToken);
    
    console.log("Token saved: " + fullToken);
} else {
    console.log("Login failed or token not found in response");
}
```

### Using the Token

After running the login request, use the saved token in other requests:

**In the Headers tab:**

| Key | Value |
|-----|-------|
| `Authorization` | `{{access_token}}` |

The token will automatically include the "Bearer" prefix, e.g., `Bearer 1|abc123xyz...`

---

## 🗄️ Database Schema

### Entity Relationship Diagram

```
┌──────────────────┐       ┌──────────────────┐       ┌──────────────────┐
│      Users       │       │      Tasks       │       │ TaskDependencies │
├──────────────────┤       ├──────────────────┤       ├──────────────────┤
│ id (PK)          │◀──┐   │ id (PK)          │◀──────│ id (PK)          │
│ name             │   │   │ title            │       │ task_id (FK)     │
│ email            │   ├───│ assigned_user_id │       │ depends_on_id(FK)│
│ password         │   │   │ created_by_id    │───┐   │ created_at       │
│ created_at       │   │   │ description      │   │   │ updated_at       │
│ updated_at       │   │   │ status           │   │   └──────────────────┘
└──────────────────┘   │   │ due_date         │   │
                       │   │ created_at       │   │
                       │   │ updated_at       │   │
                       │   └──────────────────┘   │
                       │                          │
                       └──────────────────────────┘
```

### Tables Overview

| Table | Description |
|-------|-------------|
| `users` | User accounts with authentication credentials |
| `tasks` | Task records with status, dates, and assignments |
| `task_dependencies` | Many-to-many relationship for task dependencies |
| `personal_access_tokens` | Sanctum API tokens for authentication |
| `roles` | Spatie roles (manager, user) |
| `permissions` | Spatie permissions for RBAC |
| `model_has_roles` | User-role assignments |
| `role_has_permissions` | Role-permission assignments |

---

<div align="center">

**Built with ❤️ using Laravel by Eng Islam Fadlallah**

</div>
