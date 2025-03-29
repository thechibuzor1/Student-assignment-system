# Project Assignment System
# DEMO: https://csc415grp3.wuaze.com/

A web application for automating the assignment of students to project supervisors based on a ranking system. This project uses a PHP backend with a frontend built on HTML, Tailwind CSS (via CDN), and vanilla JavaScript.

## Overview

The Department of Computer Science uses a ranking-based system to assign students to project supervisors. This application automates the process, ensuring fairness and efficiency by:
- Managing lecturer and student rankings.
- Running a round-robin assignment algorithm.
- Displaying and exporting assignment results.

## Features

- **Ranking Management**
  - Input and manage lecturer and student rankings.
- **Assignment Algorithm**
  - Automatically assigns students to lecturers using a round-robin method.
- **Assignment Display and Export**
  - View assignments on different dashboards (Admin, Lecturer, Student).
  - Export assignments as CSV (or extend to PDF/Excel).
- **Dynamic Content**
  - Generate content dynamically using PHP and optionally update parts of the page using AJAX.

## Technologies

- **Frontend:** HTML, Tailwind CSS (via CDN), and vanilla JavaScript.
- **Backend:** PHP for server-side logic.
- **Database:** MySQL for storing rankings and assignments.

## Setup / Installation

1. **Clone the Repository:**

```bash
git clone https://github.com/thechibuzor1/Student-assignment-system.git
cd project-assignment-system
```

2. **Configure the Database:**

Create a MySQL database and set up the required tables (lecturer_rankings, student_rankings, assignments).
Update the database credentials in backend/config.php.

3. **Start the PHP Server:**

```bash
php -S localhost:8000
```

4. **Access the Application:**

Open your browser and navigate to http://localhost:8000.



## PROJECT DOCUMENTATION
# Student-Lecturer Assignment System

## Overview
The **Student-Lecturer Assignment System** automates the process of assigning students to lecturers based on a ranking mechanism. The system ensures fairness, transparency, and efficient management of assignments by admins.

## User Roles
### Students
- Sign up and enter their details.
- View their assigned lecturer (supervisor).

### Lecturers
- Sign up and enter their details.
- View students assigned to them.
- Ranked based on a rating system (1-5).

### Admins
- Manage the system and oversee assignments.
- Rank lecturers.
- Trigger the student assignment process.
- Admins are pre-defined in the database and are invited via email.

## System Features
### Signup and Authentication
- **Student Signup**: Students register and provide required details.
- **Lecturer Signup**: Lecturers register and provide their details.
- **Admin Invitation**: Admins are invited via an existing admin (no independent signup).
- Authentication supports external providers like Google and Straightfy.

### Ranking System
Lecturers are ranked on a scale from 1 to 5 based on admin evaluations:
- **4.5 - 5** → Rank 5
- **3.5 - 4.49** → Rank 4
- **2.5 - 3.49** → Rank 3
- **1.99 - 2.49** → Rank 2
- **<1.99** → Rank 1

### Assignment Algorithm
- Assignments are only created if both students and lecturers exist.
- Students are grouped based on marks.
- Assignments can be reassigned manually by the admin.
- Assignments follow a **hash function modulo** and **round-robin strategy** to ensure fairness.

### Concurrency Handling
- Transaction locks prevent race conditions when multiple admins manage assignments.

## Database Design
### Conceptual DB Design
Entities:
- **Student** (assigned to a Lecturer)
- **Lecturer**
- **Admin**
- **Rating** (1-5 scale)

### Internal DB Schema
#### Students Table
| Column        | Data Type     | Description                      |
|--------------|--------------|----------------------------------|
| MatricNumber | VARCHAR(40)   | Unique Identifier for student   |
| Name         | VARCHAR(50)   | Student’s full name            |
| Level        | VARCHAR(20)   | Student’s academic level       |
| Email        | VARCHAR(50)   | Student’s email address        |
| SupervisorID | Reference     | Assigned Lecturer              |

#### Lecturers Table
| Column | Data Type   | Description              |
|--------|------------|--------------------------|
| ID     | VARCHAR(40)| Unique Identifier       |
| Name   | VARCHAR(50)| Lecturer’s full name    |
| Email  | VARCHAR(50)| Lecturer’s email address|
| Rank   | INT(1-5)   | Lecturer’s ranking      |

#### Admins Table
| Column | Data Type   | Description              |
|--------|------------|--------------------------|
| Name   | VARCHAR(50)| Admin’s full name       |
| Email  | VARCHAR(50)| Admin’s email address   |

## System Workflow
1. **User Registration**
   - Students and lecturers register and provide details.
   - Admins invite other admins via email.
2. **Lecturer Ranking**
   - Admins rank lecturers on a scale of 1-5.
3. **Student Assignment**
   - System assigns students to lecturers using a ranking-based round-robin strategy.
   - Admins can manually trigger reassignment.
4. **Concurrency Control**
   - Transaction locks prevent conflicts when multiple admins are active.

## Future Enhancements
- **Automated ranking** system based on student feedback.
- **Email notifications** for assignment updates.
- **Dashboard** for real-time monitoring of assignments and rankings.




