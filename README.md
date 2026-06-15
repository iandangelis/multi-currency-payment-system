# Multi-Currency Payment System

A Laravel-based payment request management system that allows employees to submit payment requests in their local currency while finance users can review, approve, or reject them.

The system automatically retrieves and stores exchange rates at the time of request creation, ensuring historical accuracy even if rates change later.

---

## Features

* User Registration
* User Authentication with Laravel Sanctum
* Create Payment Requests
* List Payment Requests
* View Payment Request Details
* Approve Payment Requests
* Reject Payment Requests
* Automatic Exchange Rate Retrieval
* Exchange Rate Persistence and Auditability
* Automatic Expiration of Pending Requests After 48 Hours
* Role-Based Authorization (Employee / Finance)
* Unit and Feature Test Coverage

---

## Tech Stack

* PHP 8.3+
* Laravel 12
* Laravel Sanctum
* MySQL
* PHPUnit

---

## Installation

Clone the repository:

```bash
git clone <https://github.com/iandangelis/multi-currency-payment-system.git>
cd multi-currency-payment-system
```

Install dependencies:

```bash
composer install
```

Copy environment variables:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure your database credentials in the `.env` file.

Run migrations:

```bash
php artisan migrate
```

Seed the database:

```bash
php artisan db:seed
```

Start the application:

```bash
php artisan serve
```

---

## Running Tests

Run the full test suite:

```bash
php artisan test
```

---

## Authentication

Authentication is implemented using Laravel Sanctum.

Protected endpoints require:

```http
Authorization: Bearer {token}
Accept: application/json
```

---

## Roles

### Employee

* Create payment requests
* View own payment requests

### Finance

* View all payment requests
* Approve payment requests
* Reject payment requests

---

## Payment Request Lifecycle

A payment request can have one of the following statuses:

* Pending
* Approved
* Rejected
* Expired

Pending requests older than 48 hours are automatically marked as expired by a scheduled command.

---

## Exchange Rate Integration

When a payment request is created:

1. The user's registered currency is used when no currency is provided in the request.
2. The system retrieves the current EUR exchange rate.
3. The exchange rate is stored with the payment request.
4. The exchange rate source is stored.
5. The exchange rate timestamp is stored.
6. The converted EUR amount is calculated and returned.

Stored exchange rates remain immutable after request creation.

---

## API Documentation

Base URL:

The API is exposed under the `/api` prefix.

Example local URL:

```http
http://127.0.0.1:8000/api

### Authentication

| Method | Endpoint    | Description                                      | Auth Required |
| ------ | ----------- | ------------------------------------------------ | ------------- |
| POST   | `/register` | Register a new user account                      | No            |
| POST   | `/login`    | Authenticate a user and generate an access token | No            |
| POST   | `/logout`   | Invalidate the current access token              | Yes           |

### Payment Requests

| Method | Endpoint                         | Description               | Auth Required |
| ------ | -------------------------------- | ------------------------- | ------------- |
| POST   | `/payment-requests`              | Create a payment request  | Yes           |
| GET    | `/payment-requests`              | List payment requests     | Yes           |
| GET    | `/payment-requests/{id}`         | Show a payment request    | Yes           |
| PATCH  | `/payment-requests/{id}/approve` | Approve a payment request | Yes (Finance) |
| PATCH  | `/payment-requests/{id}/reject`  | Reject a payment request  | Yes (Finance) |

---

## Request Examples

### Register

```http
POST /api/register
```

```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password",
    "country": "Brazil",
    "currency": "BRL"
}
```

All newly registered users are assigned the Employee role by default.

For demonstration and testing purposes, a Finance user is available through the database seeder:

Email: finance@example.test
Password: password

This account can be used to test finance-specific operations such as approving and rejecting payment requests.

### Login

```http
POST /api/login
```

```json
{
    "email": "john@example.com",
    "password": "password"
}
```

### Create Payment Request

```http
POST /api/payment-requests
```

```json
{
    "amount": 1000
}
```

---

## Scheduled Tasks

The application includes a scheduled command responsible for automatically expiring payment requests that remain pending for more than 48 hours.

For local development:

```bash
php artisan schedule:work
```

For production:

```bash
php artisan schedule:run
```

should be executed by the server scheduler (cron).

---

## Postman Documentation

The Postman collection and documentation are included with this project.

Collection:

```text
docs/postman/Multi-Currency-Payment-System.postman_collection.json
```

Published Documentation:

```text
<https://documenter.getpostman.com/view/34198998/2sBXwtooct>
```

---

## Assumptions

* EUR is the target currency used by the system.
* User currency is defined during registration.
* Exchange rates are stored at creation time and never recalculated.
* Only finance users may approve or reject payment requests.

---

## Future Improvements

* Swagger / OpenAPI documentation
* API versioning
* Exchange rate caching
* Queue processing for external integrations
* Notification system for request status updates
* Enhanced auditing and activity logs
* Introduce an Admin role responsible for user administration and role assignment.
* Support additional role types and permission-based access control.
* Implement a scalable RBAC (Role-Based Access Control) system for fine-grained authorization.
