# FINEDICA APP



Finedica App
The Finedica App is designed to empower users on their financial journey by providing a personalized and guided experience towards achieving their monetary goals. It leverages a unique approach that combines self-reflection, behavioral analysis, and future visualization to help users make informed financial decisions.

About Finedica
Finedica guides users through a comprehensive four-phase process to cultivate a robust money philosophy and achieve specific financial aspirations. The app aims to transform abstract financial goals into tangible, actionable plans.

Key Phases of the Finedica App
The Finedica App's core functionality is built around four distinct and interconnected phases:

Data Gathering to Build the Future Self Avatar: This initial phase focuses on collecting essential user data, including financial habits, aspirations, and personal information. This data forms the foundation for creating a personalized "Future Self Avatar," a visual representation of the user's financial goals and the future they envision.
Analyzing Previous Behavior: Finedica delves into a user's past financial activities. By understanding historical spending patterns, saving habits, and investment choices, the app provides insights into current behaviors that may impact future financial success. This analysis is crucial for identifying areas for improvement and reinforcing positive habits.
Deciding Money Philosophy: This phase encourages users to define their core values and beliefs regarding money. Through guided exercises and psychometric tests, Finedica helps users articulate their personal "money philosophy," which serves as a guiding principle for all subsequent financial decisions and goal setting.
Specific Goal Setting and Implementation: With a clear understanding of their financial past and a defined money philosophy, users can then set concrete and achievable financial goals. Whether it's buying your first home, saving for retirement, or any other specific financial objective, Finedica assists in breaking down these goals into actionable steps and provides tools for tracking progress and facilitating implementation.
Project Structure Overview
The chiemela-tech-src-new/ directory houses the entire Finedica App codebase, structured for modularity and maintainability.

└── chiemela-tech-src-new/
    ├── README.md
    ├── chatbot.php                 # PHP script for chatbot interaction
    ├── chatbot.py                  # Python script for chatbot logic (likely NLTK-based)
    ├── chatbot_model.pth           # Trained chatbot model
    ├── chatbotstyle.css            # Styling for the chatbot interface
    ├── composer.json               # PHP dependency management
    ├── composer.lock               # PHP dependency lock file
    ├── data_loader.py              # Python script for data loading (likely for chatbot/ML)
    ├── dimensions.json             # Configuration for dimensions (potentially for psychometric tests or avatar generation)
    ├── face_image.php              # PHP script for handling face image uploads
    ├── face_image_responses.php    # PHP for processing face image responses
    ├── face_image_style.css        # Styling for face image upload interface
    ├── future_self_style.css       # Styling for the future self visualization
    ├── futureself.php              # PHP script for future self visualization logic
    ├── futureself_responses.php    # PHP for processing future self responses
    ├── intents.json                # Defines chatbot intents and responses
    ├── main.css                    # Main application stylesheet
    ├── model.py                    # Python script for the chatbot's neural network model
    ├── psychometric_test.php       # PHP script for the psychometric test
    ├── psychometric_test_script.js # JavaScript for psychometric test interactivity
    ├── psychometric_test_style.css # Styling for the psychometric test
    ├── questions.json              # Psychometric test questions
    ├── regenerate_avatar_cleanup.php # PHP script for cleaning up regenerated avatars
    ├── requirements.txt            # Python dependencies
    ├── save_psychometric_results.php # PHP for saving psychometric test results
    ├── tfidf_vocab.json            # TF-IDF vocabulary (likely for chatbot)
    ├── train.py                    # Python script for training the chatbot model
    ├── 2020FC/                     # User authentication and core frontend (Legacy/Older version?)
    │   ├── config.php
    │   ├── login.php
    │   ├── session.php
    │   ├── signup.php
    │   └── src/
    │       ├── css/
    │       │   └── main.css
    │       ├── html/
    │       │   ├── avatar.html
    │       │   ├── chatbot.html
    │       │   ├── index.html
    │       │   ├── index1.html
    │       │   └── questionnaire.html
    │       └── js/
    │           ├── auth.js
    │           └── main.js
    ├── expenditure/                # Expenditure tracking module
    │   ├── database.db
    │   ├── expenditure_app.py
    │   ├── expenditure_index.html
    │   ├── expenditure_script.js
    │   ├── expenditure_style.css
    │   ├── expenditure_static/
    │   │   └── expenditure_style.css
    │   ├── expenditure_templates/
    │   │   ├── expenditure_index.html
    │   │   └── expenditure_results.html
    │   └── venv/                   # Python virtual environment for expenditure module
    │       └── pyvenv.cfg
    ├── psychometric_test/          # Dedicated psychometric test module
    │   ├── psychometric_test.py
    │   ├── psychometrictest_test_app.py
    │   ├── .DS_Store
    │   ├── psychometric_test_static/
    │   │   ├── .DS_Store
    │   │   └── css/
    │   │       └── style.css
    │   └── psychometric_test_templates/
    │       ├── psychometric_test.html
    │       └── result.html
    ├── src/                        # Core application source files
    │   ├── Avatar_test/            # Avatar generation testing
    │   │   ├── app.py
    │   │   ├── avatar_display.php
    │   │   ├── create_avatar.php
    │   │   ├── create_avatar.py
    │   │   └── dreambooth_aging_avatar.py # Script for aging avatar using Dreambooth
    │   ├── avatars/                # Directory for generated avatars
    │   ├── chatbot/                # Chatbot specific files (duplicate of root level?)
    │   │   ├── chatbot.php
    │   │   ├── chatbot.py
    │   │   ├── chatbot_model.pth
    │   │   ├── chatbotstyle.css
    │   │   ├── data_loader.py
    │   │   ├── dimensions.json
    │   │   ├── intents.json
    │   │   ├── main.css
    │   │   ├── model.py
    │   │   ├── requirements.txt
    │   │   ├── train.py
    │   │   └── __pycache__/
    │   ├── css/                    # Centralized CSS files
    │   │   ├── avatarstyle.css
    │   │   ├── chatbotstyle.css
    │   │   ├── expenditurestyle.css
    │   │   ├── futureselfstyle.css
    │   │   └── main.css
    │   ├── expenditure/            # Expenditure module (duplicate of root level?)
    │   │   ├── expenditure_app.py
    │   │   ├── expenditure_database.db
    │   │   ├── expenditure_index.php
    │   │   ├── expenditure_script.js
    │   │   ├── expenditure_style.css
    │   │   ├── expenditure_static/
    │   │   │   └── style.css
    │   │   ├── expenditure_templates/
    │   │   │   ├── index.html
    │   │   │   └── results.html
    │   │   └── expenditure_venv/
    │   │       ├── pyvenv.cfg
    │   │       ├── bin/
    │   │       └── lib/
    │   │           └── python3.9/
    │   │               └── site-packages/
    │   ├── js/                     # Centralized JavaScript files
    │   │   ├── aged_avatar_gen.js
    │   │   ├── auth.js
    │   │   ├── avatar.js
    │   │   ├── chatbot.js
    │   │   ├── expenditurescript.js
    │   │   └── main.js
    │   ├── php/                    # Centralized PHP scripts
    │   │   ├── aged_avatar_gen.php
    │   │   ├── avatar1.php
    │   │   ├── avatar_frontpage.php
    │   │   ├── avatar_googlecloud.php
    │   │   ├── chatbot.php
    │   │   ├── config.php
    │   │   ├── db_config.php
    │   │   ├── db_connect.php
    │   │   ├── debug_log.txt
    │   │   ├── error_log.txt
    │   │   ├── exec_log.txt
    │   │   ├── expenditure.php
    │   │   ├── face_image.php
    │   │   ├── face_image_responses.php
    │   │   ├── frontend_psychometric_test.php
    │   │   ├── futureself.php
    │   │   ├── futureself_responses.php
    │   │   ├── generate_avatar.php
    │   │   ├── get_avatar.php
    │   │   ├── google_drive_upload.php
    │   │   ├── home.php
    │   │   ├── index.php
    │   │   ├── login.php
    │   │   ├── logout.php
    │   │   ├── process_psychometric_test.php
    │   │   ├── psychometric_test_api.php
    │   │   ├── questionnaire.php
    │   │   ├── session.php
    │   │   ├── signup.php
    │   │   ├── test.php
    │   │   ├── test_db.php
    │   │   ├── upload.php
    │   │   ├── upload1.php
    │   │   └── upload_image.php
    │   ├── psychometric_test/      # Psychometric test module (duplicate of root level?)
    │   │   ├── psychometric_test.py
    │   │   ├── psychometric_test_app.py
    │   │   ├── .DS_Store
    │   │   ├── psychometric_test_static/
    │   │   │   ├── .DS_Store
    │   │   │   └── css/
    │   │   │       └── psychometric_test_style.css
    │   │   └── psychometric_test_templates/
    │   │       ├── psychometric_test.html
    │   │       └── psychometrict_test_result.html
    │   ├── python/                 # Centralized Python scripts
    │   │   ├── aged_avatar_gen.py
    │   │   ├── chatbot.py
    │   │   ├── generate_avatar.py
    │   │   ├── psychometric_test.py
    │   │   ├── test_nltk.py
    │   │   ├── user_reg_db.db
    │   │   ├── data/
    │   │   └── data_preproc/
    │   ├── uploads/                # Directory for user uploads (e.g., face images)
    │   └── .vscode/                # VS Code configuration files
    │       ├── launch.json
    │       └── settings.json
    └── vendor/                     # Composer dependencies (PHP)
        ├── autoload.php
        ├── bin/
        ├── brick/
        ├── composer/
        ├── firebase/
        ├── google/
        │   ├── auth/
        │   ├── cloud-core/
        │   └── cloud-storage/
Website Creation Components
The development of the Finedica website encompasses several key components:

Creating the Interface: The user interface is designed to be intuitive and engaging, guiding users seamlessly through each phase of the Finedica journey. HTML, CSS, and JavaScript files (found throughout src/html, src/css, and src/js) are used to create the visual and interactive elements.
Register and Login Pages: Secure user authentication is managed through login.php, signup.php, and session.php within the 2020FC/ and src/php directories, ensuring user data privacy.
Database to Handle User Data: User information, psychometric test results, and financial data are stored and managed through database interactions, likely via the Python user_reg_db.db and the expenditure module's database.db, accessed by various PHP and Python scripts.
Psychometric Tests: A crucial element for understanding user behavior and philosophy. The psychometric test logic is handled by psychometric_test.php and psychometric_test.py, with questions defined in questions.json.
Upload Image & Conceptualizing Future Self: Users can upload images (face_image.php, upload_image.php) which are then used in the conceptualization of their "Future Self Avatar" through scripts like generate_avatar.py and aged_avatar_gen.py.
Avatar: The core visual representation of the user's financial future. The avatar generation process, including aging effects (likely through dreambooth_aging_avatar.py), is a central feature.
Space Bubbles: This refers to visual elements within the app that represent the chatbot area

-----

## Technologies Used

  * **Backend:**
      * PHP
      * Python (with libraries like PyTorch for the chatbot, Flask for web apps)
  * **Frontend:**
      * HTML5
      * CSS3
      * JavaScript
  * **Database:**
      * SQLite (for expenditure)
      * Potentially MySQL/PostgreSQL for user management
  * **Cloud Services:**
      * Google Cloud Platform (Authentication, Storage, potentially others)
  * **Dependency Management:**
      * Composer (PHP)
      * pip (Python)

-----

## Contributing

Contributions are welcome\! Please follow these steps to contribute:

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/AmazingFeature`).
3.  Make your changes and commit them (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.

-----

## License

This project is licensed under the MIT License - see the `LICENSE` file in the `vendor/google/auth/` and `vendor/firebase/php-jwt/` directories for details on included third-party licenses. A project-wide license should also be specified in the root `LICENSE` file if it exists.

-----