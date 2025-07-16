# ER21 School Management System - Comprehensive Documentation

## Table of Contents
1. [System Overview](#1-system-overview)
2. [Architecture Overview](#2-architecture-overview)
3. [Core Features & Modules](#3-core-features--modules)
4. [Multi-Tenant SaaS Architecture](#4-multi-tenant-saas-architecture)
5. [Database Design](#5-database-design)
6. [API Documentation](#6-api-documentation)
7. [Zoom Integration API](#7-zoom-integration-api)
8. [Payment & Subscription System](#8-payment--subscription-system)
9. [Real-time Communication](#9-real-time-communication)
10. [Security Features](#10-security-features)
11. [Deployment Guide](#11-deployment-guide)
12. [Technical Specifications](#12-technical-specifications)
13. [Troubleshooting](#13-troubleshooting)

---

## 1. System Overview

**ER21** is a comprehensive **SaaS-based School Management System** built with Laravel 10 that provides educational institutions with a complete digital solution for managing academic, administrative, and financial operations.

### Key Characteristics:
- **Multi-tenant SaaS Architecture**: Supports unlimited schools with isolated databases
- **Role-based Access Control**: Super Admin, School Admin, Teacher, Student, Parent/Guardian, Staff
- **Cross-platform**: Web application + Mobile API for iOS/Android apps
- **Real-time Features**: WebSocket-based chat and notifications
- **Payment Integration**: Stripe, Razorpay, Paystack, Flutterwave
- **Subscription Management**: Feature-based package management
- **Multi-language Support**: RTL/LTR language support
- **Cloud-ready**: Designed for deployment on platforms like Railway, AWS, etc.

---

## 2. Architecture Overview

### 2.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                          CLIENT LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  Web Interface  │  Mobile Apps  │  Admin Dashboard │  APIs     │
│  (Blade/JS)     │  (Flutter)    │  (Laravel)       │  (REST)   │
└─────────────────────────────────────────────────────────────────┘
                                  │
┌─────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER                          │
├─────────────────────────────────────────────────────────────────┤
│ Controllers │ Middleware │ Services │ Repositories │ Events     │
│ ├─ Auth     │ ├─ 2FA     │ ├─ Cache │ ├─ Student   │ ├─ Notify  │
│ ├─ Student  │ ├─ RBAC    │ ├─ Upload│ ├─ Teacher   │ ├─ Payment │
│ ├─ Payment  │ ├─ Multi-DB│ ├─ Mail  │ ├─ School    │ └─ Webhook │
│ └─ API      │ └─ Demo    │ └─ SMS   │ └─ Fees      │            │
└─────────────────────────────────────────────────────────────────┘
                                  │
┌─────────────────────────────────────────────────────────────────┐
│                        DATA LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│  Master DB (MySQL)     │  School DBs (MySQL)    │  Storage      │
│  ├─ System Settings    │  ├─ Students/Teachers   │  ├─ Files     │
│  ├─ Schools            │  ├─ Academic Data       │  ├─ Images    │
│  ├─ Packages           │  ├─ Attendance          │  ├─ Documents │
│  ├─ Subscriptions      │  ├─ Exams/Results       │  └─ Backups   │
│  └─ Users (Super)      │  └─ Local Settings      │               │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Technology Stack

**Backend Framework:**
- Laravel 10.x (PHP 8.1+)
- MySQL 8.0+ (Multi-database setup)
- Redis (Caching & Sessions)
- WebSocket (Ratchet/ReactPHP)

**Frontend:**
- Blade Templates
- Bootstrap 5
- jQuery/JavaScript
- Chart.js for analytics

**Mobile Integration:**
- RESTful APIs
- Laravel Sanctum (Authentication)
- FCM (Push Notifications)

**Payment Gateways:**
- Stripe
- Razorpay
- Paystack
- Flutterwave

**External Services:**
- Google APIs (Calendar, Drive, reCAPTCHA)
- Firebase (Notifications)
- Email Services (SMTP)
- SMS Integration

---

## 3. Core Features & Modules

### 3.1 User Management System

**Role Hierarchy:**
```
Super Admin (System Level)
├── School Admin (School Level)
    ├── Teacher (Academic Level)
    ├── Staff (Administrative Level)
    ├── Student (Academic Level)
    └── Guardian/Parent (Guardian Level)
```

**Features:**
- Multi-role user management
- Profile management with photo upload
- Password reset functionality
- Two-factor authentication (2FA)
- Email verification
- Bulk user import/export

### 3.2 Academic Management

**Class & Section Management:**
- Multiple mediums (Languages)
- Streams (Science, Commerce, Arts)
- Semesters/Academic years
- Class sections with teacher assignments

**Subject Management:**
- Core and elective subjects
- Subject-teacher assignments
- Semester-wise subject allocation

**Timetable Management:**
- Drag & drop timetable builder
- Period-wise schedule
- Teacher availability management
- Conflict detection

### 3.3 Student Management

**Admission System:**
- Online admission forms
- Custom field configuration
- Document upload
- Application status tracking
- Bulk student import

**Academic Tracking:**
- Student profiles with complete details
- Guardian/parent associations
- Academic history
- Promotion/transfer management

### 3.4 Examination System

**Offline Exams:**
- Exam creation and scheduling
- Grade configuration
- Mark entry by teachers
- Result generation
- Report cards with custom templates

**Online Exams:**
- Question bank management
- Multiple question types (MCQ, True/False, Short Answer)
- Timed examinations
- Automatic evaluation
- Detailed result analysis

### 3.5 Attendance Management

**Features:**
- Daily attendance marking
- Multiple attendance modes
- Attendance reports
- Parent notifications
- Statistical analysis

### 3.6 Assignment System

**Teacher Features:**
- Assignment creation with files
- Due date management
- Subject-wise assignments

**Student Features:**
- Assignment submission
- File upload support
- Status tracking
- Grade viewing

### 3.7 Fee Management

**Fee Structure:**
- Class-wise fee configuration
- Compulsory and optional fees
- Installment support
- Late fee calculation

**Payment Processing:**
- Multiple payment gateways
- Online payment integration
- Payment receipts
- Fee reminder notifications

### 3.8 Communication System

**Real-time Chat:**
- Teacher-Student communication
- Parent-Teacher communication
- Staff communication
- File sharing in chats

**Announcements:**
- School-wide announcements
- Class-specific announcements
- Role-based notifications
- Email/SMS integration

### 3.9 Library & Resource Management

**Lesson Management:**
- Lesson planning
- Topic-wise content
- File attachments
- Video links (YouTube integration)

**Digital Library:**
- Resource uploads
- Category management
- Search functionality

### 3.10 Staff Management

**HR Features:**
- Staff profiles
- Role management
- Attendance tracking
- Leave management

**Payroll System:**
- Salary configuration
- Allowances & deductions
- Payroll generation
- Salary slips

---

## 4. Multi-Tenant SaaS Architecture

### 4.1 Database Isolation Strategy

The system uses a **Database-per-Tenant** approach for maximum data isolation:

```
┌─────────────────────────────────────────────────────────────┐
│                    MASTER DATABASE                          │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  • schools                                          │   │
│  │  • users (super admin only)                        │   │
│  │  • packages                                         │   │
│  │  • subscriptions                                    │   │
│  │  • features                                         │   │
│  │  • system_settings                                  │   │
│  │  • payment_configurations                          │   │
│  │  • payment_transactions                            │   │
│  └─────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              │
        ┌─────────────────────┼─────────────────────┐
        │                     │                     │
┌───────▼──────┐    ┌─────────▼──────┐    ┌────────▼──────┐
│ SCHOOL_1_DB  │    │ SCHOOL_2_DB    │    │ SCHOOL_N_DB   │
│              │    │                │    │               │
│ • students   │    │ • students     │    │ • students    │
│ • teachers   │    │ • teachers     │    │ • teachers    │
│ • classes    │    │ • classes      │    │ • classes     │
│ • subjects   │    │ • subjects     │    │ • subjects    │
│ • attendance │    │ • attendance   │    │ • attendance  │
│ • exams      │    │ • exams        │    │ • exams       │
│ • fees       │    │ • fees         │    │ • fees        │
│ • settings   │    │ • settings     │    │ • settings    │
└──────────────┘    └────────────────┘    └───────────────┘
```

### 4.2 Dynamic Database Switching

**Database Naming Convention:**
```php
$database_name = 'eschool_saas_' . $school_id . '_' . strtolower(strtok($school_name, " "));
// Example: eschool_saas_1_greenfield
```

**Middleware Implementation:**
- `SwitchDatabase`: For web routes
- `APISwitchDatabase`: For API routes
- `CheckSchoolStatus`: Validates school status and database access

**School Code System:**
Each school gets a unique code for mobile app access:
```
School Code Format: XXXXX (5-digit alphanumeric)
Used in API headers: 'school-code: ABC123'
```

### 4.3 School Registration Process

1. **Super Admin creates school**
2. **Database created automatically** (`eschool_saas_{id}_{name}`)
3. **School tables migrated** (separate migration files)
4. **Default roles created** (School Admin, Teacher, Student, Guardian)
5. **Permissions assigned**
6. **School admin user created**

---

## 5. Database Design

### 5.1 Master Database Tables

**Core System Tables:**
- `schools` - School information and database mapping
- `users` - Super admin users only
- `packages` - Subscription packages
- `features` - System features
- `subscriptions` - School subscriptions
- `subscription_bills` - Billing information
- `payment_transactions` - Payment records
- `system_settings` - Global system configuration

### 5.2 School Database Tables

**User Management:**
- `users` - All school users (admins, teachers, students, parents)
- `students` - Student-specific information
- `roles` - School-specific roles
- `model_has_roles` - User-role assignments

**Academic Structure:**
- `mediums` - Languages of instruction
- `streams` - Academic streams
- `classes` - Class definitions
- `sections` - Class sections
- `class_sections` - Class-section relationships
- `subjects` - Subject definitions
- `class_subjects` - Class-subject relationships

**Academic Operations:**
- `session_years` - Academic years
- `timetables` - Class schedules
- `attendance` - Daily attendance
- `exams` - Examination definitions
- `exam_timetables` - Exam schedules
- `exam_marks` - Student marks
- `assignments` - Assignment definitions
- `assignment_submissions` - Student submissions

**Financial:**
- `fees_types` - Fee categories
- `fees` - Fee structures
- `fees_paid` - Payment records
- `payment_configurations` - Payment gateway settings

### 5.3 Relationships & Constraints

**Key Relationships:**
```sql
-- School-User Relationship
users.school_id → schools.id

-- Academic Hierarchy
class_sections.class_id → classes.id
class_sections.section_id → sections.id
class_subjects.class_id → classes.id
class_subjects.subject_id → subjects.id

-- Student-Academic Relationship
students.class_section_id → class_sections.id
attendance.student_id → students.id
exam_marks.student_id → students.id

-- Fee Structure
fees.class_id → classes.id
fees_paid.student_id → students.id
fees_paid.fees_id → fees.id
```

---

## 6. API Documentation

### 6.1 Authentication System

**School-based Authentication:**
```http
POST /api/student/login
Headers:
  school-code: ABC123
  Content-Type: application/json

Body:
{
  "email": "student@school.com",
  "password": "password"
}

Response:
{
  "error": false,
  "message": "User logged-in!",
  "data": {
    "user": {...},
    "token": "bearer_token_here"
  }
}
```

**API Authentication Headers:**
```http
Authorization: Bearer {token}
school-code: {school_code}
Content-Type: application/json
```

### 6.2 Student API Endpoints

**Academic Information:**
```http
GET /api/student/class-subjects
GET /api/student/timetable
GET /api/student/attendance
GET /api/student/exam-marks
```

**Assignments:**
```http
GET /api/student/assignments
POST /api/student/submit-assignment
DELETE /api/student/delete-assignment-submission
```

**Online Exams:**
```http
GET /api/student/get-online-exam-list
GET /api/student/get-online-exam-questions?exam_id={id}
POST /api/student/submit-online-exam-answers
GET /api/student/get-online-exam-result
```

### 6.3 Teacher API Endpoints

**Class Management:**
```http
GET /api/teacher/subjects
GET /api/teacher/student-list
GET /api/teacher/get-attendance
POST /api/teacher/submit-attendance
```

**Content Management:**
```http
GET /api/teacher/get-lesson
POST /api/teacher/create-lesson
PUT /api/teacher/update-lesson
DELETE /api/teacher/delete-lesson

GET /api/teacher/get-assignment
POST /api/teacher/create-assignment
GET /api/teacher/get-assignment-submission
POST /api/teacher/update-assignment-submission
```

### 6.4 Parent API Endpoints

**Child Information:**
```http
GET /api/parent/class-subjects?child_id={id}
GET /api/parent/attendance?child_id={id}
GET /api/parent/exam-marks?child_id={id}
GET /api/parent/assignments?child_id={id}
```

**Communication:**
```http
GET /api/parent/teachers
GET /api/parent/announcements
```

**Fees:**
```http
GET /api/parent/fees
POST /api/parent/fees/compulsory/pay
POST /api/parent/fees/optional/pay
GET /api/parent/fees/receipt
```

### 6.5 Common API Endpoints

**Messaging:**
```http
GET /api/message
POST /api/message
POST /api/delete/message
POST /api/message/read
```

**Profile Management:**
```http
POST /api/update-profile
POST /api/change-password
```

---

## 7. Zoom Integration API

### 7.1 Overview

The ER21 School Management System includes comprehensive Zoom integration that allows teachers to schedule and manage online classes through their web panel, while students can join sessions and track attendance through mobile applications.

### 7.2 Zoom API Endpoints for Mobile Apps

**Base URL:** `https://your-domain.com/api/student/`

**Authentication:** All Zoom API endpoints require authentication using Laravel Sanctum tokens.

**Required Headers:**
```http
Authorization: Bearer {your_sanctum_token}
Content-Type: application/json
Accept: application/json
```

### 7.3 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/zoom/upcoming-classes` | Get upcoming Zoom classes |
| GET | `/zoom/join-session` | Join a Zoom session |
| POST | `/zoom/mark-attendance` | Mark attendance for Zoom class |
| GET | `/zoom/attendance-history` | Get Zoom attendance history |
| POST | `/zoom/send-notifications` | Send Zoom class notifications |

### 7.4 Detailed API Documentation

#### **Get Upcoming Zoom Classes**

**Endpoint:** `GET /api/student/zoom/upcoming-classes`

**Description:** Retrieves all upcoming Zoom classes for the authenticated student.

**Request Parameters:** None

**Response Format:**
```json
{
    "error": false,
    "message": "Upcoming Zoom Classes Fetched Successfully",
    "data": [
        {
            "id": 1,
            "title": "Mathematics - Algebra Basics",
            "description": "Introduction to algebraic expressions and equations",
            "meeting_id": "123456789",
            "join_url": "https://zoom.us/j/123456789?pwd=...",
            "start_time": "2025-01-20T10:00:00.000000Z",
            "end_time": "2025-01-20T11:00:00.000000Z",
            "duration": 60,
            "status": "scheduled",
            "teacher": {
                "id": 4,
                "first_name": "John",
                "last_name": "Doe",
                "full_name": "John Doe"
            },
            "subject": {
                "id": 1,
                "name": "Mathematics",
                "code": "MATH101",
                "bg_color": "#FF5733"
            },
            "class_section": {
                "id": 1,
                "name": "Class 10-A"
            }
        }
    ]
}
```

#### **Join Zoom Session**

**Endpoint:** `GET /api/student/zoom/join-session`

**Description:** Provides the join URL and meeting details for a specific Zoom class.

**Request Parameters:**
```json
{
    "class_id": 1  // Required: Zoom online class ID
}
```

**Response Format:**
```json
{
    "error": false,
    "message": "Zoom Session Details Retrieved Successfully",
    "data": {
        "class_id": 1,
        "meeting_id": "123456789",
        "password": "meeting_password",
        "join_url": "https://zoom.us/j/123456789?pwd=...",
        "title": "Mathematics - Algebra Basics",
        "teacher_name": "John Doe",
        "subject_name": "Mathematics",
        "start_time": "2025-01-20T10:00:00.000000Z",
        "end_time": "2025-01-20T11:00:00.000000Z",
        "duration": 60,
        "status": "live"
    }
}
```

#### **Mark Zoom Attendance**

**Endpoint:** `POST /api/student/zoom/mark-attendance`

**Description:** Marks attendance for a student in a Zoom class session.

**Request Parameters:**
```json
{
    "class_id": 1,  // Required: Zoom online class ID
    "join_time": "2025-01-20T10:05:00.000000Z",  // Optional: Auto-set to current time if not provided
    "remarks": "Joined on time"  // Optional: Additional remarks
}
```

**Response Format:**
```json
{
    "error": false,
    "message": "Zoom Attendance Marked Successfully",
    "data": {
        "attendance_id": 15,
        "class_id": 1,
        "student_id": 25,
        "join_time": "2025-01-20T10:05:00.000000Z",
        "status": "present",
        "remarks": "Joined on time",
        "class_title": "Mathematics - Algebra Basics",
        "teacher_name": "John Doe"
    }
}
```

#### **Get Zoom Attendance History**

**Endpoint:** `GET /api/student/zoom/attendance-history`

**Description:** Retrieves the attendance history for Zoom classes for the authenticated student.

**Request Parameters:**
```json
{
    "month": 1,        // Optional: Filter by month (1-12)
    "year": 2025,      // Optional: Filter by year
    "subject_id": 1,   // Optional: Filter by subject
    "limit": 20,       // Optional: Number of records per page (default: 20)
    "page": 1          // Optional: Page number (default: 1)
}
```

**Response Format:**
```json
{
    "error": false,
    "message": "Zoom Attendance History Fetched Successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 15,
                "join_time": "2025-01-20T10:05:00.000000Z",
                "leave_time": "2025-01-20T10:55:00.000000Z",
                "duration": 50,
                "status": "present",
                "remarks": "Joined on time",
                "online_class": {
                    "id": 1,
                    "title": "Mathematics - Algebra Basics",
                    "start_time": "2025-01-20T10:00:00.000000Z",
                    "end_time": "2025-01-20T11:00:00.000000Z",
                    "teacher": {
                        "full_name": "John Doe"
                    },
                    "subject": {
                        "name": "Mathematics",
                        "code": "MATH101"
                    }
                }
            }
        ],
        "first_page_url": "http://your-domain.com/api/student/zoom/attendance-history?page=1",
        "from": 1,
        "last_page": 3,
        "last_page_url": "http://your-domain.com/api/student/zoom/attendance-history?page=3",
        "next_page_url": "http://your-domain.com/api/student/zoom/attendance-history?page=2",
        "path": "http://your-domain.com/api/student/zoom/attendance-history",
        "per_page": 20,
        "prev_page_url": null,
        "to": 20,
        "total": 45
    }
}
```

### 7.5 Zoom Data Models

#### **ZoomOnlineClass Model**
```php
{
    "id": integer,
    "school_id": integer,
    "teacher_id": integer,
    "class_section_id": integer,
    "subject_id": integer,
    "title": string,
    "description": text,
    "meeting_id": string,
    "password": string,
    "join_url": string,
    "start_url": string,
    "start_time": datetime,
    "end_time": datetime,
    "duration": integer, // in minutes
    "is_recurring": boolean,
    "recurrence_type": string, // daily, weekly, monthly
    "recurring_interval": integer,
    "status": string, // scheduled, live, completed, cancelled
    "session_year_id": integer,
    "created_at": datetime,
    "updated_at": datetime,
    "deleted_at": datetime
}
```

#### **ZoomAttendance Model**
```php
{
    "id": integer,
    "zoom_online_class_id": integer,
    "student_id": integer,
    "join_time": datetime,
    "leave_time": datetime,
    "duration": integer, // in minutes
    "status": string, // present, absent, late
    "remarks": string,
    "created_at": datetime,
    "updated_at": datetime
}
```

#### **ZoomSetting Model**
```php
{
    "id": integer,
    "school_id": integer,
    "api_key": string,
    "api_secret": string,
    "account_id": string,
    "client_id": string,
    "client_secret": string,
    "access_token": string,
    "refresh_token": string,
    "token_expires_at": datetime,
    "is_active": boolean,
    "created_at": datetime,
    "updated_at": datetime
}
```

### 7.6 Mobile App Integration Examples

#### **Authentication Flow**
```javascript
// Login with school code
const loginResponse = await fetch('/api/student/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        gr_number: 'student_gr_number',
        password: 'password',
        school_code: 'SCH20251'
    })
});

const { token } = await loginResponse.json();
// Store token for subsequent requests
```

#### **Fetching Upcoming Classes**
```javascript
const getUpcomingClasses = async () => {
    const response = await fetch('/api/student/zoom/upcoming-classes', {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    const data = await response.json();
    return data.data; // Array of upcoming classes
};
```

#### **Joining a Zoom Session**
```javascript
const joinZoomClass = async (classId) => {
    const response = await fetch(`/api/student/zoom/join-session?class_id=${classId}`, {
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    });
    
    const data = await response.json();
    if (!data.error) {
        // Open Zoom app or web client with join_url
        window.open(data.data.join_url, '_blank');
        
        // Mark attendance
        await markAttendance(classId);
    }
};
```

#### **Marking Attendance**
```javascript
const markAttendance = async (classId) => {
    const response = await fetch('/api/student/zoom/mark-attendance', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            class_id: classId,
            remarks: 'Joined via mobile app'
        })
    });
    
    return await response.json();
};
```

### 7.7 Push Notifications

#### **Notification Types**
1. **zoom_class_reminder**: Sent 15-30 minutes before class starts
2. **zoom_class_started**: Sent when class goes live
3. **zoom_class_cancelled**: Sent when class is cancelled

#### **Notification Data Structure**
```json
{
    "title": "Zoom Class Starting Soon",
    "body": "Your Mathematics class with John Doe starts in 15 minutes",
    "type": "zoom_class_reminder",
    "custom_data": {
        "class_id": 1,
        "subject_name": "Mathematics",
        "teacher_name": "John Doe",
        "start_time": "2025-01-20T10:00:00",
        "join_url": "https://zoom.us/j/123456789?pwd=..."
    }
}
```

#### **Handling Notifications in Mobile App**
```javascript
// Firebase Cloud Messaging (FCM) handler
messaging.onMessage((payload) => {
    const { notification, data } = payload;
    
    if (data.type === 'zoom_class_reminder') {
        // Show notification with join button
        showZoomClassNotification({
            title: notification.title,
            body: notification.body,
            classId: data.class_id,
            joinUrl: data.join_url
        });
    }
});
```

### 7.8 Testing Examples

#### **cURL Test Commands**

**Test 1: Get Upcoming Classes**
```bash
curl -X GET "https://your-domain.com/api/student/zoom/upcoming-classes" \
  -H "Authorization: Bearer your_token" \
  -H "Accept: application/json"
```

**Test 2: Join Session**
```bash
curl -X GET "https://your-domain.com/api/student/zoom/join-session?class_id=1" \
  -H "Authorization: Bearer your_token" \
  -H "Accept: application/json"
```

**Test 3: Mark Attendance**
```bash
curl -X POST "https://your-domain.com/api/student/zoom/mark-attendance" \
  -H "Authorization: Bearer your_token" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "class_id": 1,
    "remarks": "Test attendance"
  }'
```

### 7.9 Error Handling

#### **Common Error Codes**
- `400`: Bad Request - Invalid parameters
- `401`: Unauthorized - Invalid or missing token
- `403`: Forbidden - Access denied
- `404`: Not Found - Resource not found
- `422`: Validation Error - Invalid input data
- `500`: Internal Server Error

#### **Error Response Format**
```json
{
    "error": true,
    "message": "Error description",
    "data": null,
    "code": 400
}
```

### 7.10 Security Considerations

1. **Authentication**: All endpoints require valid Sanctum tokens
2. **Authorization**: Students can only access their own class data
3. **Rate Limiting**: Implement rate limiting for API endpoints
4. **Data Validation**: All input data is validated server-side
5. **HTTPS**: Always use HTTPS in production
6. **Token Expiry**: Implement proper token refresh mechanisms

---

## 8. Payment & Subscription System

### 8.1 Subscription Model

**Package Types:**
- **Prepaid**: Pay before service period
- **Postpaid**: Pay after service period
- **Trial**: Free packages for testing

**Feature-based Subscriptions:**
```php
Available Features:
- Student Management
- Academics Management  
- Teacher Management
- Attendance Management
- Exam Management
- Fees Management
- Assignment Management
- Online Exam Management
- Staff Leave Management
- Website Management
- Chat Module
- ID Card & Certificate Generation
```

### 7.2 Payment Gateway Integration

**Supported Gateways:**
1. **Stripe**
   - Credit/Debit cards
   - Webhook support
   - International payments

2. **Razorpay**
   - India-focused
   - UPI, Net Banking, Wallets
   - Webhook integration

3. **Paystack**
   - Africa-focused payments
   - Multiple payment methods
   - Webhook support

4. **Flutterwave**
   - Multi-country support
   - Mobile money integration
   - Webhook support

**Payment Flow:**
```
1. User selects package
2. Payment gateway creates session/order
3. User completes payment
4. Webhook confirms payment
5. Subscription activated
6. Features enabled for school
```

### 8.3 Billing System

**Subscription Bills:**
- Generated based on package type
- Include student/staff counts
- Support partial payments
- Generate payment receipts

**Billing Cycle:**
- Monthly/Yearly subscriptions
- Automatic renewal
- Grace period for payments
- Suspension for non-payment

---

## 9. Real-time Communication

### 9.1 WebSocket Implementation

**Technology Stack:**
- Ratchet/ReactPHP for WebSocket server
- Custom WebSocket controller
- Port 8090 for WebSocket connections

**WebSocket Server Features:**
```php
Commands Supported:
- register: User registration with WebSocket
- subscribe: Channel subscription
- message: Direct messaging
- groupchat: Group communication
```

**Starting WebSocket Server:**
```bash
php artisan websocket:init
```

### 8.2 Chat System

**Chat Features:**
- One-to-one messaging
- File sharing
- Message read receipts
- Real-time notifications
- Chat history

**Supported Communications:**
- Parent ↔ Teacher
- Student ↔ Teacher  
- Teacher ↔ Staff
- Admin ↔ All roles

### 8.3 Push Notifications

**Firebase Cloud Messaging (FCM):**
- Mobile app notifications
- Web browser notifications
- Targeted messaging
- Bulk notifications

**Notification Types:**
- Assignment submissions
- Exam results
- Fee reminders
- Announcements
- Chat messages
- Attendance alerts

---

## 10. Security Features

### 9.1 Authentication & Authorization

**Multi-layer Security:**
- Role-based access control (RBAC)
- Permission-based feature access
- Database-level isolation
- API authentication tokens

**Two-Factor Authentication (2FA):**
- Email-based verification
- Time-limited codes
- Mandatory for sensitive operations

### 9.2 Data Protection

**Database Security:**
- Separate databases per school
- Encrypted sensitive data
- Regular backups
- Access logging

**File Security:**
- Secure file uploads
- Virus scanning
- File type restrictions
- Storage access controls

### 9.3 API Security

**Security Measures:**
- Rate limiting
- Request validation
- SQL injection prevention
- XSS protection
- CSRF protection

**School Isolation:**
- School code validation
- Database switching middleware
- User context verification

---

## 11. Deployment Guide

### 11.1 System Requirements

**Server Requirements:**
- PHP 8.1 or higher
- MySQL 8.0 or higher
- Web Server (Apache/Nginx)
- Composer (latest version)
- Node.js & NPM
- Redis (recommended)

**PHP Extensions:**
- PDO MySQL
- cURL
- ZIP
- OpenSSL
- Mbstring
- Tokenizer
- XML
- GD/Imagick
- JSON

**Server Specifications:**
- Memory: Minimum 4GB RAM (8GB+ recommended)
- Storage: Minimum 50GB available space
- CPU: 2+ cores (4+ recommended)
- Network: Reliable internet connection

### 10.2 Installation Steps

**1. Download & Setup:**
```bash
# Clone repository
git clone <repository-url>
cd ER21

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install
npm run production
```

**2. Environment Configuration:**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database and other settings in .env
```

**3. Database Setup:**
```bash
# Run migrations for master database
php artisan migrate --force

# Seed system data
php artisan db:seed --class=InstallationSeeder
php artisan db:seed --class=AddSuperAdminSeeder

# Setup school databases (if schools exist)
php artisan migrate:school
```

**4. Storage & Permissions:**
```bash
# Create storage symlink
php artisan storage:link

# Set permissions (Linux/Unix)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**5. Optimization:**
```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear unnecessary caches
php artisan cache:clear
```

### 10.3 Railway Deployment

**Railway-specific Configuration:**

**1. Start Command:**
```bash
mkdir -p storage/framework/views && php artisan migrate --force && php artisan db:seed --class=InstallationSeeder --force && php artisan db:seed --class=AddSuperAdminSeeder --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
```

**2. Environment Variables:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-domain.railway.app

# Database (Railway MySQL)
DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

**3. Build Configuration (nixpacks.toml):**
```toml
[phases.setup]
cmds = ['composer install --no-dev --optimize-autoloader', 'npm install', 'npm run build']

[phases.build]
cmds = ['php artisan config:cache', 'php artisan route:cache', 'php artisan view:cache']

[start]
cmd = 'php artisan serve --host=0.0.0.0 --port=$PORT'
```

### 10.4 Production Optimization

**Performance Enhancements:**
```bash
# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000

# Configure Redis for caching
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Enable compression
COMPRESS_ENABLED=true
```

**Security Hardening:**
```apache
# Apache .htaccess additions
Header set X-XSS-Protection "1; mode=block"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-Content-Type-Options "nosniff"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

---

## 12. Technical Specifications

### 12.1 Framework Details

**Laravel Framework:**
- Version: 10.x
- PHP Version: 8.1+
- Architecture: MVC with Repository Pattern
- Authentication: Laravel Sanctum
- Authorization: Spatie Laravel Permission

**Key Packages:**
```json
{
  "laravel/framework": "^10.0",
  "laravel/sanctum": "^3.2",
  "spatie/laravel-permission": "^5.5",
  "barryvdh/laravel-dompdf": "^2.0",
  "maatwebsite/excel": "^3.1",
  "intervention/image": "^2.7",
  "stripe/stripe-php": "^10.0",
  "razorpay/razorpay": "2.*",
  "cboden/ratchet": "^0.4.4"
}
```

### 11.2 Database Schema

**Master Database Size:** ~50 tables
**School Database Size:** ~80+ tables per school
**Estimated Storage:** 100MB-1GB per school (depending on usage)

**Indexing Strategy:**
- Primary keys on all tables
- Foreign key indexes
- Composite indexes for queries
- Full-text search indexes for content

### 11.3 API Performance

**Rate Limiting:**
- 60 requests per minute per user
- Burst handling for mobile apps
- Graceful degradation

**Response Times:**
- API endpoints: <200ms average
- Database queries: <50ms average
- File uploads: Depends on size/connection

### 11.4 Scalability Considerations

**Horizontal Scaling:**
- Load balancer compatible
- Session storage in Redis
- File storage on shared filesystem/CDN
- Database read replicas support

**Vertical Scaling:**
- Optimized database queries
- Eager loading relationships
- Caching at multiple levels
- Image optimization

---

## 13. Troubleshooting

### 12.1 Common Issues

**Database Connection Issues:**
```bash
# Check database credentials
php artisan tinker
DB::connection()->getPdo();

# Test school database switching
php artisan tinker
DB::setDefaultConnection('school');
```

**File Permission Issues:**
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Recreate storage link
php artisan storage:link
```

**School Database Issues:**
```bash
# Recreate school databases
php artisan migrate:school

# Reseed school data
php artisan db:seed:school
```

### 12.2 Performance Issues

**Slow Loading:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Memory Issues:**
```php
// Increase memory limits in .env
MEMORY_LIMIT=512M

// Optimize database queries
// Use eager loading: with(['relation'])
// Implement pagination for large datasets
```

### 12.3 Deployment Issues

**Railway "Application failed to respond":**
```bash
# Issue: Missing web server in start command
# Solution: Add php artisan serve to start command

# Correct start command:
mkdir -p storage/framework/views && 
php artisan migrate --force && 
php artisan config:cache && 
php artisan serve --host=0.0.0.0 --port=$PORT
```

**Environment Issues:**
```bash
# Check environment variables
php artisan tinker
echo env('APP_URL');
echo env('DB_HOST');

# Verify .env file loading
php artisan config:show database
```

### 12.4 API Issues

**School Code Problems:**
```bash
# Verify school exists
php artisan tinker
App\Models\School::where('code', 'ABC123')->first();

# Check database name
$school->database_name;
```

**Authentication Failures:**
```bash
# Clear expired tokens
php artisan sanctum:prune-expired --hours=24

# Check token validity
php artisan tinker
Laravel\Sanctum\PersonalAccessToken::findToken('token_here');
```

### 12.5 Maintenance Commands

**Regular Maintenance:**
```bash
# Database optimization
php artisan db:optimize

# Clear old logs
php artisan log:clear

# Backup databases
php artisan backup:run

# Update system
php artisan system-update
```

**Emergency Commands:**
```bash
# Reset to safe state
php artisan down
php artisan cache:clear
php artisan config:clear
php artisan up

# Database repair
php artisan migrate:status
php artisan migrate:rollback
php artisan migrate
```

---

## Conclusion

The ER21 School Management System is a comprehensive, scalable solution designed for educational institutions of all sizes. Its multi-tenant SaaS architecture ensures data isolation while providing powerful features for academic, administrative, and financial management.

The system's modular design allows for easy customization and feature expansion, while its robust API enables mobile app integration and third-party connections. With proper deployment and maintenance, ER21 can serve as a complete digital transformation solution for educational institutions.

For additional support and customization, refer to the codebase documentation and consider engaging with the development team for specific requirements.

---

**Documentation Version:** 1.0  
**Last Updated:** January 2025  
**System Version:** ER21 v1.5.4 