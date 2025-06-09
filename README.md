# FINEDICA APP

---

The Finedica App is designed to empower users on their financial journey by providing a personalized and guided experience towards achieving their monetary goals. It leverages a unique approach that combines self-reflection, behavioral analysis, and future visualization to help users make informed financial decisions.

---

## About Finedica

Finedica guides users through a comprehensive four-phase process to cultivate a robust money philosophy and achieve specific financial aspirations. The app aims to transform abstract financial goals into tangible, actionable plans.

---

## Key Phases of the Finedica App

The Finedica App's core functionality is built around four distinct and interconnected phases:

1.  **Data Gathering to Build the Future Self Avatar**: This initial phase focuses on collecting essential user data, including financial habits, aspirations, and personal information. This data forms the foundation for creating a personalized "Future Self Avatar," a visual representation of the user's financial goals and the future they envision.
2.  **Analyzing Previous Behavior**: Finedica delves into a user's past financial activities. By understanding historical spending patterns, saving habits, and investment choices, the app provides insights into current behaviors that may impact future financial success. This analysis is crucial for identifying areas for improvement and reinforcing positive habits.
3.  **Deciding Money Philosophy**: This phase encourages users to define their core values and beliefs regarding money. Through guided exercises and psychometric tests, Finedica helps users articulate their personal "money philosophy," which serves as a guiding principle for all subsequent financial decisions and goal setting.
4.  **Specific Goal Setting and Implementation**: With a clear understanding of their financial past and a defined money philosophy, users can then set concrete and achievable financial goals. Whether it's buying your first home, saving for retirement, or any other specific financial objective, Finedica assists in breaking down these goals into actionable steps and provides tools for tracking progress and facilitating implementation.

---

## Table of Contents

* [Features](#features)
* [Installation](#installation)
    * [Prerequisites](#prerequisites)
    * [Setup Steps](#setup-steps)
* [Usage](#usage)
* [Project Structure](#project-structure)
* [Technologies Used](#technologies-used)
* [Contributing](#contributing)
* [License](#license)
* [Contact](#contact)

---

## Features

* **Chatbot**: An intelligent conversational agent powered by Python, capable of understanding and responding to user queries.
* **Avatar Generation**: Users can generate and potentially age avatars using DreamBooth, providing a dynamic visual representation.
* **Psychometric Test**: A web-based psychometric test designed to assess user traits, with results saved and processed.
* **Expenditure Tracker**: A personal finance tool to help users monitor and manage their expenditures.
* **User Authentication**: Secure user registration and login system.
* **Google Cloud Integration**: Utilizes Google Cloud services for various functionalities, including potential image storage and batch processing.

---

## Installation

### Prerequisites

Before you begin, ensure you have the following installed:

* **Web Server**: Apache or Nginx
* **PHP**: Version 7.4 or higher
* **Composer**: PHP dependency manager
* **Python**: Version 3.8 or higher
* **pip**: Python package installer
* **Node.js & npm** (optional): For front-end asset management if applicable
* **Database**: (Likely SQLite for `expenditure/database.db`, and potentially MySQL/PostgreSQL for user registration).

### Setup Steps

1.  **Clone the Repository**:

    ```bash
    git clone [https://github.com/your-username/chiemela-tech-src-new.git](https://github.com/your-username/chiemela-tech-src-new.git)
    cd chiemela-tech-src-new
    ```

2.  **PHP Dependencies**:
    Install PHP dependencies using Composer.

    ```bash
    composer install
    ```

3.  **Python Dependencies**:
    Navigate to the relevant Python directories and install dependencies from `requirements.txt`.

    ```bash
    # For Chatbot
    cd src/chatbot/
    pip install -r requirements.txt
    cd ../../

    # For main project requirements (if any global requirements.txt exists)
    pip install -r requirements.txt # Located in the root of chiemela-tech-src-new/
    ```

    You may also need to install dependencies for `expenditure_app.py` and `psychometric_test_app.py` if they have separate `requirements.txt` files within their respective venv directories, or activate their virtual environments.

4.  **Database Setup**:

    * For the expenditure tracker, ensure the `expenditure/database.db` file is accessible and writable by the web server.
    * For user authentication, configure your database connection in `src/php/db_config.php` and run any necessary SQL schema scripts if not already present.

5.  **Web Server Configuration**:
    Configure your web server (Apache/Nginx) to point its document root to the `chiemela-tech-src-new/` directory, or a sub-directory containing your primary `index.php` or `index.html` file. Ensure PHP is correctly configured and enabled.

6.  **Google Cloud Configuration**:
    If using Google Cloud services, ensure your service account credentials are set up correctly and accessible to the PHP and Python scripts (e.g., via environment variables or a `keyfile.json`). Refer to the Google Cloud documentation for specific service configurations (e.g., Google Cloud Storage, Google Batch).

---

## Usage

Once installed, access the application through your web server.

* **Homepage**: Navigate to `http://localhost/` or your configured domain.
* **Login/Signup**: Access `http://localhost/src/php/login.php` or `http://localhost/src/php/signup.php` to manage user accounts.
* **Chatbot**: The chatbot interface is likely accessible via `http://localhost/chatbot.php` or `http://localhost/src/php/chatbot.php`, or embedded within an HTML page.
* **Avatar Generation**: Functionality for creating and regenerating avatars would be available through `http://localhost/face_image.php` or `http://localhost/src/php/generate_avatar.php`.
* **Psychometric Test**: Start the test at `http://localhost/psychometric_test.php` or `http://localhost/src/php/frontend_psychometric_test.php`.
* **Expenditure App**: Access the expenditure tracker at `http://localhost/expenditure/expenditure_index.html` or `http://localhost/src/php/expenditure.php`.

---

## Project Structure

The project is organized into several key modules:

src-new/
├── README.md                      # This README file
├── chatbot.php                    # PHP interface for the chatbot
├── chatbot.py                     # Main Python chatbot logic
├── chatbot_model.pth              # Trained chatbot model
├── chatbotstyle.css               # Chatbot specific styling
├── ...                            # Other root-level PHP, Python, and CSS files
├── 2020FC/                        # Older or separate authentication module
│   ├── config.php
│   ├── login.php
│   ├── signup.php
│   └── src/                       # HTML, CSS, JS for 2020FC
├── expenditure/                   # Expenditure tracking application
│   ├── database.db                # SQLite database for expenditures
│   ├── expenditure_app.py         # Python backend for expenditure
│   ├── expenditure_index.html     # Frontend for expenditure app
│   └── ...                        # Associated static files and templates
├── psychometric_test/             # Psychometric test application (standalone Python Flask app)
│   ├── psychometric_test.py
│   ├── psychometric_test_app.py
│   └── ...                        # Static files and templates
├── src/                           # Main source code directory
│   ├── Avatar_test/               # Python scripts for avatar generation/aging
│   │   ├── app.py
│   │   ├── create_avatar.py
│   │   └── dreambooth_aging_avatar.py
│   ├── avatars/                   # Directory to store generated avatars
│   ├── chatbot/                   # Dedicated chatbot module (Python/PHP)
│   │   ├── chatbot.php
│   │   ├── chatbot.py
│   │   ├── chatbot_model.pth
│   │   └── ...
│   ├── css/                       # Global and module-specific CSS files
│   ├── expenditure/               # Expenditure module (PHP/Python integration)
│   │   ├── expenditure_app.py
│   │   ├── expenditure_database.db
│   │   └── ...
│   ├── js/                        # Frontend JavaScript files
│   ├── php/                       # Core PHP scripts for various functionalities
│   │   ├── login.php
│   │   ├── generate_avatar.php
│   │   ├── process_psychometric_test.php
│   │   └── ...
│   ├── psychometric_test/         # Psychometric test module (Python/PHP integration)
│   │   ├── psychometric_test.py
│   │   └── ...
│   ├── python/                    # General Python utilities and scripts
│   │   ├── aged_avatar_gen.py
│   │   ├── generate_avatar.py
│   │   └── user_reg_db.db
│   ├── uploads/                   # Directory for user uploads (e.g., face images)
│   └── .vscode/                   # VS Code configuration files
└── vendor/                        # Composer PHP dependencies
├── autoload.php
├── firebase/php-jwt/          # JWT library for authentication
├── google/auth/               # Google Cloud authentication library
├── google/cloud-core/         # Core Google Cloud client libraries
├── google/cloud-storage/      # Google Cloud Storage client library
└── ...


---

## Technologies Used

* **Backend**:
    * PHP
    * Python (with libraries like PyTorch for the chatbot, Flask for web apps)
* **Frontend**:
    * HTML5
    * CSS3
    * JavaScript
* **Database**:
    * SQLite (for expenditure)
    * Potentially MySQL/PostgreSQL for user management
* **Cloud Services**:
    * Google Cloud Platform (Authentication, Storage, potentially others)
* **Dependency Management**:
    * Composer (PHP)
    * pip (Python)

---

## Contributing

Contributions are welcome! Please follow these steps to contribute:

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/AmazingFeature`).
3.  Make your changes and commit them (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

---

## License

This project is licensed under the MIT License - see the `LICENSE` file in the `vendor/google/auth/` and `vendor/firebase/php-jwt/` directories for details on included third-party licenses. A project-wide license should also be specified in the root `LICENSE` file if it exists.

---