# Booking System

This is a **Laravel Booking System** project that allows users to view and reserve books. The system includes user registration, book status tracking (Available/Booked), and an admin role with full permissions for managing books.

## Project Overview

- **Backend**: Laravel
- **Database**: MySQL
- **Authentication & Authorization**: Spatie Laravel-Permission (Roles & Permissions)
- **Booking System**: Users can reserve books if the status is available. Admin has full control over the book data.

## Features

### 1. User Registration & Authentication

- Users can register for an account.
- After registration, they can view the list of books.
- Books can have two statuses:
  - **Available**
  - **Booked**

### 2. Book Management

- Books have a `status` field that can be either **available** or **booked**.
- If the status of a book is **available**, a "Reserve" button is shown.
- When the user reserves a book, they can specify a **start date** and **end date** for the reservation.

### 3. Admin Role & Permissions

- **Admin**: Can view, create, and edit book details.
- Admin has full access to manage all books in the system.
- Admin role is handled using the [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission/v5) package.
- Admin can only see the **Edit** button and can change book details such as title, author, and status.

### 4. Automated Status Reset

- A scheduled task runs daily to check if the **end date** of a book reservation is less than today's date. If so, the book’s status will automatically be changed back to **available**.

## Project Setup

### 1. Clone the Repository

```bash
git clone https://github.com/MostafaLotfy00/book-borrowing-system.git
cd book-borrowing-system

### 2. Install Dependencies

Run the following commands to install the necessary dependencies.

```bash
composer install
npm install


### 3. Set Up the Environment File
cp .env.example .env

### 4. Database Setup

php artisan migrate
php artisan db:seed

### 4. Database Setup

php artisan serve