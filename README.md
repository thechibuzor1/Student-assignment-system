# Project Assignment System

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
