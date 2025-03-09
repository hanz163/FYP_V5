<!DOCTYPE html>
<html lang="en">
    <head>
        @include('navigation')
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modify Questions</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <!-- Include Sortable.js -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f8f9fa;
                padding: 0;
            }

            .container {
                max-width: 1200px;
                margin: 2rem auto;
                padding: 0 1rem;
            }

            .container h2 {
                color: #1a73e8;
                text-align: center;
                font-size: 2rem;
                font-weight: 600;
            }
            .card {
                background-color: #ffffff;
                padding: 1.5rem;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin-bottom: 1.5rem;
                position: relative;
            }

            .context-container {
                background-color: #ffffff;
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin-bottom: 2rem;
                margin-top: 6rem; /* Add margin-top to push the container down */
                text-align: left;
            }

            .context-container label {
                font-weight: bold;
                color: #1a73e8;
                font-size: 1rem;
                margin-bottom: 0.5rem;
                display: block;
            }

            .context-container select {
                width: 100%;
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background-color: #fff;
                font-family: 'Poppins', sans-serif;
                font-size: 1rem;
                color: #333;
                cursor: pointer;
                transition: border-color 0.3s ease;
            }

            .context-container select:hover {
                border-color: #1a73e8;
            }

            .context-container select:focus {
                outline: none;
                border-color: #1a73e8;
                box-shadow: 0 0 5px rgba(26, 115, 232, 0.5);
            }

            .difficulty-container {
                margin-bottom: 1.5rem;
                background-color: #f1f1f1;
                padding: 1rem;
                border-radius: 12px;
            }

            .difficulty-container.easy {
                background-color: #e8f5e9; /* Light green for easy */
            }

            .difficulty-container.normal {
                background-color: #fff3e0; /* Light orange for normal */
            }

            .difficulty-container.hard {
                background-color: #ffebee; /* Light red for hard */
            }

            .difficulty-header {
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1.5rem;
                color: #1a73e8;
                margin-bottom: 1rem;
                padding: 0.5rem;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }

            .difficulty-header:hover {
                background-color: rgba(26, 115, 232, 0.1);
            }

            .sortable-list {
                list-style-type: none;
                padding: 0;
            }

            .sortable-list .card {
                cursor: grab;
            }

            .sortable-list .card:active {
                cursor: grabbing;
            }

            .question {
                margin-top: 10px;
                margin-bottom: 1.5rem;
            }

            /* Style for the question header */
            .question-header {
                display: flex;
                justify-content: space-between; /* Space out the question text and actions */
                align-items: center; /* Vertically center the content */
                cursor: pointer;
            }

            /* Style for the header actions (expand button and checkbox) */
            .header-actions {
                display: flex;
                align-items: center; /* Align the expand button and checkbox vertically */
                gap: 10px; /* Add spacing between the expand button and checkbox */
            }

            /* Style for the expand button */
            .expand-button {
                background-color: #1a73e8;
                color: #fff;
                border: none;
                border-radius: 5px;
                padding: 5px 10px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .expand-button:hover {
                background-color: #1557b0;
            }

            /* Style for the checkbox */
            .question-checkbox {
                margin: 0; /* Remove default margin */
                cursor: pointer;
            }

            .sortable-list {
                list-style-type: none;
                padding: 0;
            }

            .sortable-list .card {
                cursor: grab;
            }

            .sortable-list .card:active {
                cursor: grabbing;
            }

            /* Improved Dropdown Styling */
            select {
                width: 100%;
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                background-color: #fff;
                font-family: 'Poppins', sans-serif;
                font-size: 1rem;
                color: #333;
                cursor: pointer;
                transition: border-color 0.3s ease;
            }

            select:hover {
                border-color: #1a73e8;
            }

            select:focus {
                outline: none;
                border-color: #1a73e8;
                box-shadow: 0 0 5px rgba(26, 115, 232, 0.5);
            }

            .difficulty-container {
                margin-bottom: 1.5rem;
            }

            .difficulty-header {
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1.5rem;
                color: #1a73e8;
                margin-bottom: 1rem;
                padding: 0.5rem;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }

            .difficulty-header:hover {
                background-color: #f1f1f1;
            }

            .manual-form {
                margin-top: 1.5rem;
            }

            .manual-form .form-group {
                margin-bottom: 1rem;
            }

            .manual-form label {
                font-weight: 500;
                color: #1a73e8;
                margin-bottom: 0.5rem;
                display: block;
            }

            .manual-form input,
            .manual-form textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                font-family: 'Poppins', sans-serif;
                font-size: 1rem;
                color: #333;
            }

            .manual-form textarea {
                resize: vertical;
                min-height: 100px;
            }

            .manual-form .correct-answer {
                border-left: 4px solid #2d8b44;
                padding-left: 10px;
            }

            .manual-form .wrong-answer {
                border-left: 4px solid #dc3545;
                padding-left: 10px;
            }

            .manual-form .explanation {
                border-left: 4px solid #1a73e8;
                padding-left: 10px;
            }

            .manual-form button {
                padding: 10px 20px;
                background-color: #1a73e8;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .manual-form button:hover {
                background-color: #1557b0;
            }

            /* AI Processing Section */
            .ai-processing-section {
                margin-top: 1.5rem;
            }

            .upload-box {
                margin-bottom: 20px; /* Add this line */
                background-color: #f9f9f9;
                border: 2px dashed #1a73e8;
                border-radius: 12px;
                padding: 2rem;
                text-align: center;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .upload-box:hover {
                background-color: #e9f2ff;
            }

            .upload-box p {
                margin: 0;
                color: #666;
            }

            .upload-box input[type="file"] {
                display: none;
            }


            .question-details {
                margin-top: 1rem;
                display: none;
            }

            .question-details.show {
                display: block;
            }

            .answer {
                margin-left: 20px;
                margin-bottom: 10px;
            }

            .answer.correct {
                color: #2d8b44;
                font-weight: 500;
            }

            .answer.wrong {
                color: #dc3545;
            }

            .explanation {
                margin-top: 1rem;
                font-style: italic;
                color: #666;
            }

            .last-modified {
                margin-top: 0.5rem;
                font-size: 0.9rem;
                color: #888;
            }

            .action-buttons {
                display: flex;
                gap: 10px;
                margin-top: 1rem;
            }

            .action-buttons button {
                padding: 5px 10px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .action-buttons button.edit {
                background-color: #1a73e8;
                color: #fff;
            }

            .action-buttons button.delete {
                background-color: #dc3545;
                color: #fff;
            }

            .action-buttons button:hover {
                opacity: 0.9;
            }

            .add-question-form {
                margin-top: 2rem;
                padding: 1rem;
                background-color: #f1f1f1;
                border-radius: 8px;
            }

            .add-question-form input,
            .add-question-form textarea {
                width: 100%;
                padding: 10px;
                margin-bottom: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .add-question-form button {
                padding: 10px 20px;
                background-color: #1a73e8;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .add-question-form button:hover {
                background-color: #1557b0;
            }

            .add-question-container {
                margin-top: 2rem;
                background-color: #f1f1f1;
                border-radius: 8px;
                padding: 1rem;
            }

            .add-question-header {
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 1.5rem;
                color: #1a73e8;
                margin-bottom: 1rem;
                padding: 0.5rem;
                border-radius: 5px;
                transition: background-color 0.3s ease;
            }

            .add-question-header:hover {
                background-color: #e9f2ff;
            }

            .bulk-actions {
                margin-bottom: 1rem;
                display: flex;
                gap: 10px;
            }

            .bulk-actions button {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }

            .bulk-actions button.bulk-delete {
                background-color: #dc3545;
                color: #fff;
            }

            .bulk-actions button.save-order {
                background-color: #1a73e8;
                color: #fff;
            }

            .bulk-actions button:hover {
                opacity: 0.9;
            }

            .question-checkbox {
                position: absolute;
                top: 1rem;
                left: 1rem;
            }

            .loading-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }

            .loading-modal .spinner {
                font-size: 2rem;
                color: #fff;
            }

            .blue-bold-label {
                font-weight: bold;
                color: #1a73e8;
                font-size: 1rem;
                margin-bottom: 0.5rem;
                display: block;
            }

            /* Style for Select Method Dropdown */
            #questionMethod {
                font-weight: bold;
                color: #1a73e8;
            }

            /* Style for Difficulty Label in AI Form */
            #aiForm .form-group label {
                font-weight: bold;
                color: #1a73e8;
            }

            #questionMethod {
                font-weight: normal; /* Reset to default */
                color: #333; /* Reset to default */
            }

            .manual-form textarea#question_text {
                width: 100%;
                min-height: 100px;
                resize: vertical; /* Allow vertical resizing */
            }

            /* Make the explanation text field larger and fix the size */
            .manual-form textarea#explanation {
                width: 100%;
                min-height: 150px; /* Larger fixed height */
                resize: none; /* Disable resizing */
            }

            /* Make the question text field longer */
            .question-text {
                width: 100%; /* Take up the full width of the container */
                display: inline-block; /* Allow it to expand */
                margin-bottom: 10px; /* Add spacing below the question */
                white-space: normal; /* Allow text to wrap to the next line */
                word-wrap: break-word; /* Break long words to prevent overflow */
                overflow-wrap: break-word; /* Ensure long words break properly */
            }

            /* Make the explanation text field larger and fix the size */
            .explanation textarea.edit-field {
                width: 100%;
                min-height: 120px; /* Larger fixed height */
                resize: none; /* Disable resizing */
                font-family: 'Poppins', sans-serif; /* Consistent font */
                padding: 10px; /* Add padding for better appearance */
                border: 1px solid #ccc; /* Add a border for clarity */
                border-radius: 5px; /* Rounded corners */
            }

            /* Disable expand/collapse when editing */
            .card.editing .question-header {
                pointer-events: none; /* Disable click events on the question header */
            }

            .question {
                width: 100%; /* Adjust this value as needed */
                margin: 0 auto; /* Center the container */
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Course, Chapter, and Part Selection -->
            <div class="context-container">
                <h2>Modify Questions</h2>

                <form method="GET" action="{{ route('modify.questions') }}">
                    <label for="course">Course:</label>
                    <select name="course" id="course" onchange="this.form.submit()">
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->courseID }}" {{ $selectedCourse == $course->courseID ? 'selected' : '' }}>{{ $course->courseName }}</option>
                        @endforeach
                    </select>

                    @if($selectedCourse)
                    <label for="chapter">Chapter:</label>
                    <select name="chapter" id="chapter" onchange="this.form.submit()">
                        <option value="">Select Chapter</option>
                        @foreach($chapters as $chapter)
                        <option value="{{ $chapter->chapterID }}" {{ $selectedChapter == $chapter->chapterID ? 'selected' : '' }}>{{ $chapter->chapterName }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if($selectedChapter)
                    <label for="part">Part:</label>
                    <select name="part" id="part" onchange="this.form.submit()">
                        <option value="">Select Part</option>
                        @foreach($parts as $part)
                        <option value="{{ $part->partID }}" {{ $selectedPart == $part->partID ? 'selected' : '' }}>{{ $part->title }}</option>
                        @endforeach
                    </select>
                    @endif
                </form>
            </div>

            <div class="bulk-actions">
                <button class="save-order" onclick="saveOrder()">Save Order</button>
                <button class="bulk-delete" onclick="bulkDelete()">Bulk Delete</button>
                <button class="select-all" onclick="toggleSelectAll()">Select All</button>
            </div>

            <!-- Display Questions Grouped by Difficulty -->
            @if($selectedPart && $questions->isNotEmpty())
            @php
            $groupedQuestions = $questions->groupBy('difficulty.level');
            @endphp

            @foreach($groupedQuestions as $difficulty => $questions)
            <div class="difficulty-container {{ $difficulty }}">
                <h3 class="difficulty-header" onclick="toggleDifficulty('{{ $difficulty }}')">
                    {{ ucfirst($difficulty) }}
                </h3>
                <ul class="sortable-list" id="sortable-list-{{ $difficulty }}" style="display: none;">
                    @foreach($questions as $question)
                    <li class="card" data-question-id="{{ $question->QuestionID }}">
                        <div class="question">
                            <div class="question-header" onclick="toggleQuestionDetails('{{ $question->QuestionID }}')">
                                <span class="question-text" id="question-text-{{ $question->QuestionID }}">
                                    {{ $loop->iteration }}. {{ preg_replace('/^\d+\.\s*/', '', $question->question_text) }}
                                </span>
                                <input type="text" class="edit-field" id="question-text-input-{{ $question->QuestionID }}" value="{{ $question->question_text }}" style="display: none; width: 50%;">
                                <div class="header-actions">
                                    <button class="expand-button">Expand</button>
                                    <input type="checkbox" class="question-checkbox" value="{{ $question->QuestionID }}">
                                </div>
                            </div>
                            <div class="question-details" id="question-details-{{ $question->QuestionID }}">
                                <div class="answer correct">
                                    Correct Answer: 
                                    <span id="correct-answer-text-{{ $question->QuestionID }}">{{ $question->answers->first()->answer_text }}</span>
                                    <input type="text" class="edit-field" id="correct-answer-input-{{ $question->QuestionID }}" value="{{ $question->answers->first()->answer_text }}" style="display: none;">
                                </div>
                                <div class="answer wrong">
                                    Wrong Answer 1: 
                                    <span id="wrong-answer-1-text-{{ $question->QuestionID }}">{{ $question->answers->first()->wrong_answer_1 }}</span>
                                    <input type="text" class="edit-field" id="wrong-answer-1-input-{{ $question->QuestionID }}" value="{{ $question->answers->first()->wrong_answer_1 }}" style="display: none;">
                                </div>
                                <div class="answer wrong">
                                    Wrong Answer 2: 
                                    <span id="wrong-answer-2-text-{{ $question->QuestionID }}">{{ $question->answers->first()->wrong_answer_2 }}</span>
                                    <input type="text" class="edit-field" id="wrong-answer-2-input-{{ $question->QuestionID }}" value="{{ $question->answers->first()->wrong_answer_2 }}" style="display: none;">
                                </div>
                                <div class="answer wrong">
                                    Wrong Answer 3: 
                                    <span id="wrong-answer-3-text-{{ $question->QuestionID }}">{{ $question->answers->first()->wrong_answer_3 }}</span>
                                    <input type="text" class="edit-field" id="wrong-answer-3-input-{{ $question->QuestionID }}" value="{{ $question->answers->first()->wrong_answer_3 }}" style="display: none;">
                                </div>
                                <div class="explanation">
                                    Explanation: 
                                    <span id="explanation-text-{{ $question->QuestionID }}">{{ $question->answers->first()->explanation }}</span>
                                    <textarea class="edit-field" id="explanation-input-{{ $question->QuestionID }}" style="display: none;">{{ $question->answers->first()->explanation }}</textarea>
                                </div>
                                <div class="action-buttons">
                                    <button class="edit" id="edit-button-{{ $question->QuestionID }}" onclick="editQuestion('{{ $question->QuestionID }}')">Edit</button>
                                    <button class="delete" onclick="deleteQuestion('{{ $question->QuestionID }}')">Delete</button>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
            @else
            <div class="card">
                <p>No questions available for this part.</p>
            </div>
            @endif

            <!-- Add New Question Section -->
            <div class="add-question-container">
                <h3 class="add-question-header" onclick="toggleAddQuestionForm()">
                    Add New Question
                </h3>
                <div id="add-question-form" style="display: none;">
                    <!-- Select Method Section -->
                    <label for="questionMethod" class="blue-bold-label">Select Method</label>
                    <select id="questionMethod" onchange="toggleQuestionMethod()">
                        <option value="" disabled>Select Method</option>
                        <option value="manual">Enter Manually</option>
                        <option value="ai">Process with AI</option>
                    </select>

                    <!-- Manual Input Form -->
                    <div id="manualForm" class="manual-form">
                        <form method="POST" action="{{ route('questions.add.manual') }}">
                            @csrf
                            <input type="hidden" name="part_id" value="{{ $selectedPart }}">
                            <div class="form-group">
                                <label for="question_text" class="blue-bold-label">Question Text</label>
                                <textarea name="question_text" id="question_text" placeholder="Enter the question text" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="difficulty" class="blue-bold-label">Difficulty</label>
                                <select name="difficulty" id="difficulty" required>
                                    <option value="easy">Easy</option>
                                    <option value="normal">Normal</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div class="form-group correct-answer">
                                <label for="correct_answer" class="blue-bold-label">Correct Answer</label>
                                <input type="text" name="correct_answer" id="correct_answer" placeholder="Enter the correct answer" required>
                            </div>
                            <div class="form-group wrong-answer">
                                <label for="wrong_answer_1" class="blue-bold-label">Wrong Answer 1</label>
                                <input type="text" name="wrong_answer_1" id="wrong_answer_1" placeholder="Enter a wrong answer" required>
                            </div>
                            <div class="form-group wrong-answer">
                                <label for="wrong_answer_2" class="blue-bold-label">Wrong Answer 2</label>
                                <input type="text" name="wrong_answer_2" id="wrong_answer_2" placeholder="Enter a wrong answer" required>
                            </div>
                            <div class="form-group wrong-answer">
                                <label for="wrong_answer_3" class="blue-bold-label">Wrong Answer 3</label>
                                <input type="text" name="wrong_answer_3" id="wrong_answer_3" placeholder="Enter a wrong answer" required>
                            </div>
                            <div class="form-group explanation">
                                <label for="explanation" class="blue-bold-label">Explanation</label>
                                <textarea name="explanation" id="explanation" placeholder="Enter the explanation" required></textarea>
                            </div>
                            <button type="submit">Add Question</button>
                        </form>
                    </div>

                    <!-- AI Processing Form -->
                    <div id="aiForm" class="ai-processing-section" style="display: none;">
                        <div class="form-group">
                            <label for="aiDifficulty" class="blue-bold-label">Difficulty</label>
                            <select name="difficulty" id="aiDifficulty" required>
                                <option value="easy">Easy</option>
                                <option value="normal">Normal</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>
                        <div class="upload-box" onclick="document.getElementById('questionFile').click()">
                            <input type="file" id="questionFile" class="visually-hidden" accept=".docx" onchange="displayFileName()">
                            <p id="file-name">Click Here to Select Your Question Docx.</p>
                        </div>
                        <button class="btn btn-success mt-3 w-100" onclick="processWithAI()">Process with AI</button>
                    </div>
                </div>

                <!-- Loading Modal -->
                <div class="loading-modal" id="loadingModal">
                    <div class="spinner">Processing with AI...</div>
                </div>
            </div>
        </div>

        <script>
            function displayFileName() {
            const fileInput = document.getElementById('questionFile');
            const fileNameDisplay = document.getElementById('file-name');
            if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
            } else {
            fileNameDisplay.textContent = 'Click Here to Select Your Question Docx.';
            }
            }
            // Toggle question details
            function toggleQuestionDetails(questionID) {
            const details = document.getElementById(`question-details-${questionID}`);
            details.classList.toggle('show');
            }

            // Edit question
            function editQuestion(questionID) {
            const editButton = document.getElementById(`edit-button-${questionID}`);
            const textFields = [
                    `question-text-${questionID}`,
                    `correct-answer-text-${questionID}`,
                    `wrong-answer-1-text-${questionID}`,
                    `wrong-answer-2-text-${questionID}`,
                    `wrong-answer-3-text-${questionID}`,
                    `explanation-text-${questionID}`
            ];
            const inputFields = [
                    `question-text-input-${questionID}`,
                    `correct-answer-input-${questionID}`,
                    `wrong-answer-1-input-${questionID}`,
                    `wrong-answer-2-input-${questionID}`,
                    `wrong-answer-3-input-${questionID}`,
                    `explanation-input-${questionID}`
            ];
            if (editButton.textContent === 'Edit') {
            // Switch to edit mode
            textFields.forEach(field => document.getElementById(field).style.display = 'none');
            inputFields.forEach(field => document.getElementById(field).style.display = 'block');
            editButton.textContent = 'Save';
            } else {
            // Save changes
            const updatedData = {
            question_text: document.getElementById(`question-text-input-${questionID}`).value,
                    correct_answer: document.getElementById(`correct-answer-input-${questionID}`).value,
                    wrong_answer_1: document.getElementById(`wrong-answer-1-input-${questionID}`).value,
                    wrong_answer_2: document.getElementById(`wrong-answer-2-input-${questionID}`).value,
                    wrong_answer_3: document.getElementById(`wrong-answer-3-input-${questionID}`).value,
                    explanation: document.getElementById(`explanation-input-${questionID}`).value
            };
            fetch(`/questions/${questionID}/update`, {
            method: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(updatedData)
            }).then(response => {
            if (response.ok) {
            // Update displayed text
            document.getElementById(`question-text-${questionID}`).textContent = updatedData.question_text;
            document.getElementById(`correct-answer-text-${questionID}`).textContent = updatedData.correct_answer;
            document.getElementById(`wrong-answer-1-text-${questionID}`).textContent = updatedData.wrong_answer_1;
            document.getElementById(`wrong-answer-2-text-${questionID}`).textContent = updatedData.wrong_answer_2;
            document.getElementById(`wrong-answer-3-text-${questionID}`).textContent = updatedData.wrong_answer_3;
            document.getElementById(`explanation-text-${questionID}`).textContent = updatedData.explanation;
            // Switch back to view mode
            textFields.forEach(field => document.getElementById(field).style.display = 'inline');
            inputFields.forEach(field => document.getElementById(field).style.display = 'none');
            editButton.textContent = 'Edit';
            alert('Question updated successfully!');
            } else {
            response.json().then(data => {
            console.error("Error response:", data);
            alert('Failed to update the question. Please try again.');
            });
            }
            }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the question.');
            });
            }
            }

            // Delete question
            function deleteQuestion(questionID) {
            if (confirm('Are you sure you want to delete this question?')) {
            fetch(`/questions/${questionID}/delete/modify`, {
            method: 'DELETE',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                    }
            }).then(response => {
            if (response.ok) {
            alert('Question deleted successfully!');
            window.location.reload(); // Reload the page to reflect the changes
            } else {
            response.json().then(data => {
            console.error("Error response:", data); // Debugging
            alert('Failed to delete the question. Please try again.');
            });
            }
            }).catch(error => {
            console.error('Error:', error); // Debugging
            alert('An error occurred while deleting the question.');
            });
            }
            }


            // Bulk delete
            function bulkDelete() {
            const checkboxes = document.querySelectorAll('.question-checkbox:checked');
            const questionIDs = Array.from(checkboxes).map(checkbox => checkbox.value);
            if (questionIDs.length === 0) {
            alert('Please select at least one question to delete.');
            return;
            }

            if (confirm('Are you sure you want to delete the selected questions?')) {
            fetch('/questions/bulk-delete', {
            method: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ questionIDs })
            }).then(response => {
            if (response.ok) {
            alert('Selected questions deleted successfully!');
            window.location.reload();
            } else {
            alert('Failed to delete the questions. Please try again.');
            }
            });
            }
            }


            // Initialize Sortable.js for drag-and-drop reordering
            document.querySelectorAll('.sortable-list').forEach(sortableList => {
            new Sortable(sortableList, {
            animation: 150,
                    group: 'questions', // Allow dragging between lists
                    onEnd: function (evt) {
                    console.log('Question reordered');
                    }
            });
            });
            // Save order
            function saveOrder() {
            const difficultyContainers = document.querySelectorAll('.difficulty-container');
            const orderData = [];
            difficultyContainers.forEach(container => {
            const difficulty = container.querySelector('.difficulty-header').textContent.trim().toLowerCase();
            const questionIDs = Array.from(container.querySelectorAll('.card')).map(card => card.getAttribute('data-question-id'));
            orderData.push({ difficulty, questionIDs });
            });
            fetch('/questions/reorder', {
            method: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ orderData })
            }).then(response => {
            if (response.ok) {
            alert('Order saved successfully!');
            } else {
            alert('Failed to save order. Please try again.');
            }
            }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the order.');
            });
            }


            // Toggle difficulty section
            function toggleDifficulty(difficulty) {
            const sortableList = document.getElementById(`sortable-list-${difficulty}`);
            if (sortableList.style.display === 'none') {
            sortableList.style.display = 'block';
            } else {
            sortableList.style.display = 'none';
            }
            }

            function toggleQuestionMethod() {
            const method = document.getElementById('questionMethod').value;
            document.getElementById('manualForm').style.display = method === 'manual' ? 'block' : 'none';
            document.getElementById('aiForm').style.display = method === 'ai' ? 'block' : 'none';
            }

            // Process question with AI
            // Process question with AI
            async function processWithAI() {
            const fileInput = document.getElementById('questionFile');
            if (!fileInput.files.length) {
            alert('Please select a file first.');
            return;
            }

            // Retrieve selected course, chapter, part, and difficulty
            const courseID = document.getElementById('course').value;
            const chapterID = document.getElementById('chapter').value;
            const partID = document.querySelector('input[name="part_id"]').value;
            const difficulty = document.getElementById('aiDifficulty').value;
            if (!courseID || !chapterID || !partID || !difficulty) {
            alert('Please ensure all fields (Course, Chapter, Part, and Difficulty) are selected.');
            return;
            }

            // Show loading modal
            document.getElementById('loadingModal').style.display = 'flex';
            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('courseID', courseID);
            formData.append('chapterID', chapterID);
            formData.append('partID', partID);
            formData.append('difficulty', difficulty);
            try {
            const response = await fetch('/questions/add/ai', {
            method: 'POST',
                    headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
            });
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            if (data.error) {
            alert(data.error);
            } else {
            alert('Questions processed and saved successfully!');
            window.location.reload();
            }
            } else {
            const text = await response.text();
            alert('Unexpected response: ' + text);
            }
            } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while processing the file.');
            } finally {
            // Hide loading modal
            document.getElementById('loadingModal').style.display = 'none';
            }
            }

            // Toggle select all checkboxes
            function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.question-checkbox');
            const selectAllButton = document.querySelector('.select-all');
            const isAllSelected = Array.from(checkboxes).every(checkbox => checkbox.checked);
            checkboxes.forEach(checkbox => {
            checkbox.checked = !isAllSelected;
            });
            selectAllButton.textContent = isAllSelected ? 'Select All' : 'Deselect All';
            }

            document.querySelector('form').addEventListener('submit', function (event) {
            const selectedDifficulty = document.getElementById('difficulty').value;
            console.log('Selected Difficulty:', selectedDifficulty); // Debugging
            // Ensure the form submits with the correct difficulty
            });
            function toggleAddQuestionForm() {
            const form = document.getElementById('add-question-form');
            if (form.style.display === 'none') {
            form.style.display = 'block';
            } else {
            form.style.display = 'none';
            }
            }

            function toggleQuestionMethod() {
            const method = document.getElementById('questionMethod').value;
            document.getElementById('manualForm').style.display = method === 'manual' ? 'block' : 'none';
            document.getElementById('aiForm').style.display = method === 'ai' ? 'block' : 'none';
            }
        </script>
    </body>
</html>