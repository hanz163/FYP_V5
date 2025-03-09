<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $chapter->chapterName }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f4f4f4;
            }
            .card {
                background-color: white;
                border-radius: 12px;
                padding: 20px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }
            .button-green {
                background-color: #22c55e;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                text-align: center;
                font-weight: bold;
                display: inline-block;
                transition: 0.3s;
            }
            .button-green:hover {
                background-color: #16a34a;
            }
            .button-outline {
                border: 2px solid #22c55e;
                color: #22c55e;
                padding: 12px 20px;
                border-radius: 8px;
                text-align: center;
                font-weight: bold;
                display: inline-block;
                transition: 0.3s;
            }
            .button-outline:hover {
                background-color: #22c55e;
                color: white;
            }
            .icon-button {
                background-color: #22c55e;
                color: white;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                font-size: 18px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .icon-button:hover {
                background-color: #2d8b44;
            }
            .hidden {
                display: none;
            }
            .part-card {
                background-color: #f9f9f9;
                border-radius: 12px;
                padding: 15px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: 0.3s;
            }
            .part-card:hover {
                background-color: #f3f3f3;
                transform: translateY(-2px);
            }
            .part-card:not(:last-child) {
                margin-bottom: 10px;
            }
            .course-image {
                max-width: 100%;
                height: auto;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                object-fit: cover;
                transition: transform 0.3s ease;
            }
            .button-green {
                background-color: #34a853;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                text-align: center;
                font-weight: bold;
                display: inline-block;
                transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out;
            }
            .button-green:hover {
                background-color: #2d8b44;
                transform: translateY(-2px);
            }
            .button-outline-green {
                border: 2px solid #34a853;
                color: #34a853;
                padding: 12px 20px;
                border-radius: 8px;
                text-align: center;
                font-weight: bold;
                display: inline-block;
                transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
            }
            .button-outline-green:hover {
                background-color: #34a853;
                color: white;
            }
            .iframe-container {
                width: 100%;
                height: 400px;
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                margin-top: 10px;
            }
            .iframe-container iframe {
                width: 100%;
                height: 100%;
                border: none;
            }

            .iframe-container {
                width: 100%;
                height: 70vh; /* Use 70% of the viewport height */
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                margin-top: 10px;
            }

            .iframe-container iframe {
                width: 100%;
                height: 100%;
                border: none;
            }

            .iframe-container {
                width: 100%;
                height: 70vh; /* Use 70% of the viewport height */
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                margin-top: 10px;
            }

            .iframe-container iframe {
                width: 100%;
                height: 100%;
                border: none;
            }
        </style>
    </head>
    <body>
        @include('navigation')

        <div class="max-w-6xl mx-auto mt-10 grid grid-cols-2 gap-8">
            <!-- Left Section: Chapter Details -->
            <div class="card">
                @if ($courseImage)
                <img src="{{ asset('storage/' . $courseImage) }}" alt="Course Image" class="course-image">
                @else
                <p>No image available.</p>
                @endif

                <h2 class="text-2xl font-bold mt-4 text-[#34A853]">{{ $chapter->chapterName }}</h2>

                <div class="flex items-center space-x-2 text-gray-500 mt-2">
                    <span class="font-semibold">Instructor: {{ $teacherName }}</span>
                </div>

                <h3 class="text-lg font-bold mt-4">Chapter Details</h3>
                <p class="text-gray-600 mt-1">{{ $chapter->description }}</p>
                <!-- TTS Button for Chapter Description -->
                <button onclick="convertTextToSpeech('{{ $chapter->description }}')" class="button-green mt-4">
                    Listen to Chapter Description
                </button>
            </div>

            <!-- Right Section: Chapter Overview -->
            <div>
                <div class="card">
                    <h3 class="text-xl font-bold mb-4 text-[#34A853]">Chapter Overview</h3>

                    @if ($parts->isNotEmpty())
                    @foreach ($parts as $part)
                    <div class="mb-2">
                        <!-- Clickable Part Section -->
                        <div onclick="toggleDropdown('dropdown-{{ $part->partID }}')" 
                             class="flex justify-between items-center p-4 border-b border-gray-300 part-card cursor-pointer">
                            <div>
                                <h4 class="text-lg font-semibold">{{ $part->title }}</h4>
                                <p class="text-gray-600 text-sm">
                                    {{ $part->lectureNotes->count() }} Lecture Note{{ $part->lectureNotes->count() > 1 ? 's' : '' }} & 
                                    {{ $part->lectureVideos->count() }} Video{{ $part->lectureVideos->count() > 1 ? 's' : '' }}
                                </p>
                            </div>
                            <div class="icon-button">
                                <img src="{{ asset('icon/right-arrow.png') }}" alt="rightArrow"/>
                            </div>
                        </div>

                        <!-- Hidden Dropdown Content -->
                        <div id="dropdown-{{ $part->partID }}" class="dropdown-content bg-gray-100 rounded-lg p-4 hidden">
                            <h5 class="text-md font-semibold text-green-700">Lecture Notes</h5>
                            <ul class="list-disc list-inside text-gray-700">
                                @foreach ($part->lectureNotes as $note)
                                <li>
                                    <a href="#" onclick="openIframeModal('{{ asset('storage/' . $note->file_path) }}', '{{ $note->title }}')" class="text-[#3a3b3c] no-underline hover:underline">
                                        {{ $note->title }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>

                            <h5 class="text-md font-semibold text-green-700 mt-2">Lecture Videos</h5>
                            <ul class="list-disc list-inside text-gray-700">
                                @foreach ($part->lectureVideos as $video)
                                <li>
                                    <a href="{{ url('/lecture-videos/' . $video->id) }}" class="text-[#3a3b3c] no-underline hover:underline" target="_blank">
                                        {{ $video->title }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-gray-500 text-center">No parts available for this chapter.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Button -->
        <div class="text-center mt-6">
            <button onclick="openDifficultyQuestionModal()" class="button-green w-full max-w-lg block mx-auto">Answer Question</button>
        </div>

        <!-- Difficulty and Question Modal -->
        <div id="difficultyQuestionModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="text-xl font-bold text-[#34A853]">Select Difficulty and Question</h2>
                    <button onclick="closeDifficultyQuestionModal()" class="text-red-500 text-xl font-bold">&times;</button>
                </div>

                <!-- Course (Disabled) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Course:</label>
                    <input type="text" value="{{ $course->courseName }}" class="w-full border rounded-lg p-2 mt-1 bg-gray-100" disabled>
                </div>

                <!-- Chapter (Disabled) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Chapter:</label>
                    <input type="text" value="{{ $chapter->chapterName }}" class="w-full border rounded-lg p-2 mt-1 bg-gray-100" disabled>
                </div>

                <!-- Part Selection (Dropdown) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Part:</label>
                    <select class="w-full border rounded-lg p-2 mt-1">
                        @foreach ($parts as $part)
                        <option value="{{ $part->partID }}">{{ $part->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Difficulty Selection (Radio Buttons) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Difficulty:</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="difficulty" value="Easy" class="form-radio">
                            <span class="ml-2">Easy</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio" name="difficulty" value="Normal" class="form-radio">
                            <span class="ml-2">Normal</span>
                        </label>
                        <label class="inline-flex items-center ml-6">
                            <input type="radio" name="difficulty" value="Hard" class="form-radio">
                            <span class="ml-2">Hard</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-between mt-6">
                    <button onclick="closeDifficultyQuestionModal()" class="button-outline-green">Back</button>
                    <button onclick="startQuestions()" class="button-green">Start</button>
                </div>
            </div>
        </div>

        <!-- Iframe Modal -->
        <div id="iframeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-2/3">
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="text-xl font-bold text-[#34A853]">Lecture Content</h2>
                    <button onclick="closeIframeModal()" class="text-red-500 text-xl font-bold">&times;</button>
                </div>
                <div class="iframe-container mt-4">
                    <iframe id="modalIframe" src="" frameborder="0"></iframe>
                </div>
                <!-- Hidden element to store the extracted text -->
                <div id="extractedText" class="hidden"></div>
                <!-- TTS Button inside the Iframe Modal -->
                <button id="listenToNoteButton" class="button-green mt-4">
                    Listen to Note
                </button>
            </div>
        </div>

        <script>
            // Toggle dropdown visibility
            function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');
            }

            // Open difficulty question modal
            function openDifficultyQuestionModal() {
            document.getElementById('difficultyQuestionModal').classList.remove('hidden');
            }

            // Close difficulty question modal
            function closeDifficultyQuestionModal() {
            document.getElementById('difficultyQuestionModal').classList.add('hidden');
            }

            // Start questions based on selected part and difficulty
            function startQuestions() {
            const selectedPart = document.querySelector('#difficultyQuestionModal select').value;
            const selectedDifficulty = document.querySelector('#difficultyQuestionModal input[name="difficulty"]:checked').value;
            window.location.href = `/answer-questions/${selectedPart}?difficulty=${selectedDifficulty}`;
            }

            // Split text into logical chunks (e.g., sentences or paragraphs)
            function splitTextIntoChunks(text, chunkSize = 3000) {
            const chunks = [];
            let start = 0;
            while (start < text.length) {
            let end = start + chunkSize;
            if (end >= text.length) {
            end = text.length;
            } else {
            // Find the nearest sentence or paragraph boundary
            while (end > start && text[end] !== '.' && text[end] !== '\n') {
            end--;
            }
            if (end === start) {
            end = start + chunkSize; // Fallback to fixed chunk size
            }
            }
            chunks.push(text.slice(start, end));
            start = end;
            }
            return chunks;
            }

            // Convert a single chunk of text to speech with retries
            async function convertChunkToSpeech(chunk, retries = 3) {
            for (let i = 0; i < retries; i++) {
            try {
            const response = await fetch('/convert-text-to-speech', {
            method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ text: chunk })
            });
            if (!response.ok) throw new Error('Failed to convert text to speech');
            return await response.blob();
            } catch (error) {
            console.error(`Attempt ${i + 1} failed:`, error);
            if (i === retries - 1) throw error; // Re-throw error if all retries fail
            }
            }
            }

            // Convert large text to speech by splitting into chunks
            async function convertLargeTextToSpeech(text) {
            const chunks = splitTextIntoChunks(text);
            const audioBlobs = [];
            // Show a loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.innerText = 'Processing... Please wait.';
            loadingIndicator.style.position = 'fixed';
            loadingIndicator.style.top = '20px';
            loadingIndicator.style.left = '50%';
            loadingIndicator.style.transform = 'translateX(-50%)';
            loadingIndicator.style.padding = '10px 20px';
            loadingIndicator.style.backgroundColor = '#34A853';
            loadingIndicator.style.color = 'white';
            loadingIndicator.style.borderRadius = '8px';
            loadingIndicator.style.zIndex = '1000';
            document.body.appendChild(loadingIndicator);
            try {
            for (const chunk of chunks) {
            const blob = await convertChunkToSpeech(chunk);
            audioBlobs.push(blob);
            }

            // Combine all audio blobs into a single blob
            const combinedBlob = new Blob(audioBlobs, { type: 'audio/mpeg' });
            const audioUrl = URL.createObjectURL(combinedBlob);
            const audio = new Audio(audioUrl);
            audio.play();
            } catch (error) {
            console.error('Error:', error);
            alert('Unable to convert text to speech. Please try again later.');
            } finally {
            // Remove the loading indicator
            document.body.removeChild(loadingIndicator);
            }
            }

            // Convert text to speech (handles both small and large texts)
            function convertTextToSpeech(text) {
            if (text.length > 3000) {
            convertLargeTextToSpeech(text);
            } else {
            fetch('/convert-text-to-speech', {
            method: 'POST',
                    headers: {
                    'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ text: text })
            })
                    .then(response => {
                    if (!response.ok) throw new Error('Failed to convert text to speech');
                    return response.blob();
                    })
                    .then(blob => {
                    const audioUrl = URL.createObjectURL(blob);
                    const audio = new Audio(audioUrl);
                    audio.play();
                    })
                    .catch(error => {
                    console.error('Error:', error);
                    alert('Unable to convert text to speech.');
                    });
            }
            }

            // Open iframe modal and load content
            function openIframeModal(url, title) {
            const iframe = document.getElementById('modalIframe');
            const extractedTextElement = document.getElementById('extractedText');
            const proxyUrl = `/proxy?url=${encodeURIComponent(url)}`;
            console.log('Opening iframe modal for URL:', url);
            iframe.src = url;
            document.getElementById('iframeModal').classList.remove('hidden');
            // Automatically play the title when the iframe opens
            convertTextToSpeech(title);
            // Fetch the extracted text from the proxy
            fetch(proxyUrl)
                    .then(response => {
                    if (!response.ok) throw new Error('Failed to fetch extracted text');
                    return response.text();
                    })
                    .then(text => {
                    console.log('Extracted text:', text);
                    extractedTextElement.innerText = text; // Store the extracted text
                    })
                    .catch(error => {
                    console.error('Error fetching extracted text:', error);
                    alert('Unable to fetch the extracted text.');
                    });
            // Add event listener to the "Listen to Note" button
            const listenButton = document.getElementById('listenToNoteButton');
            listenButton.onclick = function () {
            const extractedText = extractedTextElement.innerText;
            console.log('Extracted text for TTS:', extractedText);
            if (extractedText) {
            convertTextToSpeech(extractedText); // Use the extracted text for TTS
            } else {
            alert('No extracted text available.');
            }
            };
            }

            // Close iframe modal
            function closeIframeModal() {
            document.getElementById('modalIframe').src = '';
            document.getElementById('extractedText').innerText = '';
            document.getElementById('iframeModal').classList.add('hidden');
            }
        </script>
    </body>
</html>

