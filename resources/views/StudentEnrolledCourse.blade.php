<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Enrolled Courses</title>

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

        <style>
            body {
                background-color: #f4f4f4; /* Very light grey */
            }

            /* Green button styling */
            .green-button {
                background-color: #28a745; /* Green background */
                color: white; /* White text */
                padding: 10px 20px; /* Padding */
                border-radius: 8px; /* Rounded corners */
                font-weight: medium; /* Medium font weight */
                transition: background-color 0.3s ease; /* Smooth hover transition */
            }

            .green-button:hover {
                background-color: #218838; /* Darker green on hover */
            }

            /* Change cursor to grab when hovering over draggable items */
            .course-item {
                cursor: grab;
            }

            /* Change cursor to grabbing when dragging */
            .course-item:active, .course-item.dragging {
                cursor: grabbing;
            }

            /* No border button styling */
            .no-border-button {
                background-color: transparent;
                border: none;
                color: #3b82f6; /* Blue color */
                text-decoration: underline;
                cursor: pointer;
                padding: 0;
                font-size: 14px;
            }

            .no-border-button:hover {
                color: #2563eb; /* Darker blue on hover */
            }

            .grey-button {
                background-color: #6c757d; /* Grey background */
                color: white; /* White text */
                padding: 10px 20px; /* Padding */
                border-radius: 8px; /* Rounded corners */
                font-weight: medium; /* Medium font weight */
                transition: background-color 0.3s ease; /* Smooth hover transition */
            }

            .grey-button:hover {
                background-color: #5a6268; /* Darker grey on hover */
            }

        </style>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
            // Initialize sortable list for enrolled courses
            let sortable = new Sortable(document.getElementById("enrolledCoursesList"), {
            animation: 150,
                    ghostClass: "dragging",
                    handle: ".course-item",
                    onStart: function (evt) {
                    evt.item.classList.add("dragging"); // Add class when dragging starts
                    },
                    onEnd: function (evt) {
                    evt.item.classList.remove("dragging"); // Remove class when dragging ends
                    console.log("Moved item from index", evt.oldIndex, "to", evt.newIndex);
                    }
            });
            // Initialize sortable list for the arrange modal
            let arrangeSortable = new Sortable(document.getElementById("sortableCourses"), {
            animation: 150,
                    ghostClass: "dragging",
                    handle: ".course-item", // Ensure only course items are draggable
                    onStart: function (evt) {
                    evt.item.classList.add("dragging"); // Add class when dragging starts
                    },
                    onEnd: function (evt) {
                    evt.item.classList.remove("dragging"); // Remove class when dragging ends
                    }
            });
            // Add event listener for category filter
            document.getElementById("categoryFilter").addEventListener("change", function () {
            filterCoursesByCategory(this.value);
            });
            });
// Function to filter courses by category
            function filterCoursesByCategory(category) {
            const allCourseItems = document.querySelectorAll("#allEnrolledCoursesContainer .course-item");
            const enrolledCoursesList = document.getElementById("enrolledCoursesList");
            // Clear the current list
            enrolledCoursesList.innerHTML = "";
            // Filter and append matching courses
            allCourseItems.forEach(item => {
            const itemCategory = item.getAttribute("data-category");
            if (category === "all" || itemCategory === category) {
            const clonedItem = item.cloneNode(true); // Clone the item
            enrolledCoursesList.appendChild(clonedItem); // Append to the list
            }
            });
            }
            // Function to archive a course
            function archiveCourse() {
            const courseID = document.getElementById("selectedCourseName").getAttribute("data-course-id");
            if (confirm("Are you sure you want to archive this course?")) {
            fetch(`/course/${courseID}/archive`, {
            method: "POST",
                    headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                    },
            })
                    .then(response => response.json())
                    .then(data => {
                    if (data.success) {
                    alert("Course archived successfully!");
                    location.reload(); // Refresh the page to reflect the changes
                    } else {
                    alert("Failed to archive course.");
                    }
                    })
                    .catch(error => console.error("Error:", error));
            }
            }
            // Function to display course details in the right panel
            function showCourseDetails(courseID, courseName, category, description, studentCount, capacityOffered, progress, image) {
            document.getElementById("selectedCourseName").innerText = courseName;
            document.getElementById("selectedCourseName").setAttribute("data-course-id", courseID); // Add this line
            document.getElementById("selectedCategory").innerText = category;
            document.getElementById("selectedDescription").innerText = description;
            document.getElementById("studentCount").innerText = studentCount + " students enrolled";
            document.getElementById("capacityOffered").innerText = "Capacity: " + capacityOffered;
            document.getElementById("selectedCourseImage").src = image;
            // Ensure progress is within valid range (0-100)
            let progressValue = parseInt(progress, 10) || 0;
            progressValue = Math.min(Math.max(progressValue, 0), 100);
            // Update progress bar in the right section
            let progressBar = document.getElementById("progressBar");
            progressBar.style.width = progressValue + "%";
            document.getElementById("progressText").innerText = progressValue + "% Progress";
            // Hide default message and show course details
            document.getElementById("defaultMessage").classList.add("hidden");
            document.getElementById("courseDetails").classList.remove("hidden");
            // Update progress bar in the left section dynamically
            let leftProgressBar = document.getElementById(`progressBar_${courseID}`);
            if (leftProgressBar) {
            leftProgressBar.style.width = progressValue + "%";
            }

            // Update the "Start Learning" button link
            document.getElementById("startLearningLink").href = `/course/${courseID}/chapters/student`;
            }

            // Function to open the arrange modal
            function arrangeCourses() {
            document.getElementById("arrangeCoursesModal").classList.remove("hidden");
            }

            // Function to close the arrange modal
            function closeArrangeModal() {
            document.getElementById("arrangeCoursesModal").classList.add("hidden");
            }

            // Function to save the new course order
            function saveCourseOrder() {
            let sortedItems = document.querySelectorAll("#sortableCourses .course-item");
            let newOrder = Array.from(sortedItems).map((item, index) => ({
            courseID: item.getAttribute("data-course-id"),
                    newPosition: index + 1
            }));
            fetch("{{ route('student.courses.reorder') }}", {
            method: "POST",
                    headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ order: newOrder })
            })
                    .then(response => response.json())
                    .then(data => {
                    if (data.success) {
                    alert("Order saved successfully!");
                    closeArrangeModal();
                    location.reload(); // Refresh the page to reflect the new order
                    } else {
                    alert("Failed to save order.");
                    }
                    })
                    .catch(error => console.error("Error:", error));
            }

            // Function to toggle the visibility of archived courses
            function toggleArchivedCourses() {
            const archivedCoursesList = document.getElementById("archivedCoursesList");
            const showArchivedBtn = document.getElementById("showArchivedCoursesBtn");
            if (archivedCoursesList.classList.contains("hidden")) {
            archivedCoursesList.classList.remove("hidden");
            showArchivedBtn.innerText = "Hide Archived Courses";
            } else {
            archivedCoursesList.classList.add("hidden");
            showArchivedBtn.innerText = "Show Archived Courses";
            }
            }

// Function to unarchive a course
            function unarchiveCourse(courseID) {
            if (confirm("Are you sure you want to unarchive this course?")) {
            fetch(`/course/${courseID}/unarchive`, {
            method: "POST",
                    headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Content-Type": "application/json",
                    },
            })
                    .then(response => response.json())
                    .then(data => {
                    if (data.success) {
                    alert("Course unarchived successfully!");
                    location.reload(); // Refresh the page to reflect the changes
                    } else {
                    alert("Failed to unarchive course.");
                    }
                    })
                    .catch(error => console.error("Error:", error));
            }
            }
        </script>
    </head>
    <body>
        @include('navigation')

        <div class="max-w-6xl mx-auto mt-8 p-4">
            <div class="grid grid-cols-3 gap-6">
                <!-- Left Sidebar: Enrolled Courses -->
                <div class="col-span-1 bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-bold mb-4">Enrolled Courses</h2>

                    <!-- Category Filter Dropdown -->
                    <div class="mb-4">
                        <label for="categoryFilter" class="block text-sm font-medium text-gray-700">Sort by Category</label>
                        <select id="categoryFilter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="all">All Categories</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Arrange Courses Button -->
                    <button id="arrangeCoursesBtn" class="green-button font-medium py-1.5 px-3 text-sm mb-4" onclick="arrangeCourses()">
                        Arrange Courses
                    </button>

                    <div id="enrolledCoursesList" class="space-y-4">
                        @foreach ($enrolledCourses as $course)
                        @php
                        $courseImage = $course->image ? Storage::url($course->image) : asset('images/course-placeholder.jpg');
                        @endphp
                        <div class="course-item bg-gray-100 p-4 rounded-lg flex items-center space-x-4"
                             data-category="{{ $course->category }}"
                             onclick="showCourseDetails('{{ $course->courseID }}', '{{ $course->courseName }}', '{{ ucfirst($course->category) }}', '{{ $course->description }}', '{{ $course->studentCount }}', '{{ $course->capacityOffered }}', '{{ $course->progress }}', '{{ $courseImage }}')">
                            <img src="{{ $courseImage }}" alt="Course Image" class="w-12 h-12 rounded-full">
                            <div>
                                <p class="text-lg font-semibold">{{ $course->courseName }}</p>
                                <p class="text-sm text-gray-600">Category : {{ ucfirst($course->category) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Show Archived Courses Button -->
                    <div class="mt-4">
                        <button id="showArchivedCoursesBtn" class="no-border-button" onclick="toggleArchivedCourses()">
                            Show Archived Courses
                        </button>
                    </div>

                    <!-- Archived Courses List (Hidden by Default) -->
                    <div id="archivedCoursesList" class="space-y-4 hidden">
                        @foreach ($archivedCourses as $course)
                        @php
                        $courseImage = $course->image ? Storage::url($course->image) : asset('images/course-placeholder.jpg');
                        @endphp
                        <div class="course-item bg-gray-100 p-4 rounded-lg"
                             onclick="showCourseDetails('{{ $course->courseID }}', '{{ $course->courseName }}', '{{ ucfirst($course->category) }}', '{{ $course->description }}', '{{ $course->studentCount }}', '{{ $course->capacityOffered }}', '{{ $course->progress }}', '{{ $courseImage }}')">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $courseImage }}" alt="Course Image" class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="text-lg font-semibold">{{ $course->courseName }}</p>
                                    <p class="text-sm text-gray-600">Category : {{ ucfirst($course->category) }}</p>
                                </div>
                            </div>
                            <!-- Unarchive Button -->
                            <div class="mt-2">
                                <button class="text-sm text-blue-500 hover:text-blue-700" onclick="unarchiveCourse('{{ $course->courseID }}')">
                                    Unarchive
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $enrolledCourses->links() }}
                    </div>
                </div>

                <!-- Right Section: Course Details -->
                <!-- Right Section: Course Details -->
                <div id="courseDetails" class="col-span-2 bg-white p-6 rounded-lg shadow hidden">
                    <img id="selectedCourseImage" src="{{ asset('images/selected-course.jpg') }}" class="w-full h-60 object-cover rounded-lg" alt="Course Image">
                    <h2 class="text-2xl font-bold mt-4" id="selectedCourseName"></h2>
                    <p class="text-gray-500 text-sm mt-1" id="selectedCategory"></p>

                    <h3 class="text-lg font-bold mt-4">Course Overview</h3>
                    <p class="text-gray-600 mt-0.5" id="selectedDescription"></p>

                    <p class="text-gray-600 mt-2" id="studentCount"></p>
                    <p class="text-gray-600 mt-1" id="capacityOffered"></p>

                    <div class="progress-bar mt-4">
                        <div class="progress-fill" id="progressBar" style="width: 0%;"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1" id="progressText">0% Progress</p>

                    <!-- Start Learning Button -->
                    <div class="mt-6">
                        <a id="startLearningLink" href="#" class="green-button text-white px-6 py-2 w-40">
                            Start Learning
                        </a>
                    </div>

                    <!-- Archive Course Button -->
                    <div class="mt-4">
                        <button id="archiveCourseBtn" class="grey-button text-center text-white px-4 py-2 w-40 " onclick="archiveCourse()">
                            Archive Course
                        </button>
                    </div>
                </div>

                <!-- Default Message (Visible Initially) -->
                <div id="defaultMessage" class="col-span-2 bg-white p-6 rounded-lg shadow flex flex-col justify-center items-center h-64">
                    <img src="{{ asset('photo/selectIcon.png') }}" alt="Select Course Icon" class="w-24 h-24 mb-4">
                    <h2 class="text-2xl font-semibold text-gray-500">Select a Course to View Details</h2>
                </div>
            </div>
        </div>

        <!-- Arrange Courses Modal -->
        <div id="arrangeCoursesModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                <h2 class="text-lg font-bold mb-4">Arrange Courses</h2>
                <ul id="sortableCourses" class="space-y-2">
                    @foreach ($allEnrolledCourses as $course)
                    <li class="course-item bg-gray-100 p-3 rounded flex items-center justify-between"
                        data-course-id="{{ $course->courseID }}">
                        <span>{{ $course->courseName }}</span>
                        <span class="cursor-move">☰</span>
                    </li>
                    @endforeach
                </ul>
                <div class="mt-4 flex justify-end space-x-2">
                    <button onclick="closeArrangeModal()" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                    <button onclick="saveCourseOrder()" class="green-button">Save Order</button>
                </div>
            </div>
        </div>

        <!-- Hidden container for all enrolled courses (used for filtering) -->
        <div id="allEnrolledCoursesContainer" class="hidden">
            @foreach ($allEnrolledCoursesForFilter as $course)
            @php
            $courseImage = $course->image ? Storage::url($course->image) : asset('images/course-placeholder.jpg');
            @endphp
            <div class="course-item bg-gray-100 p-4 rounded-lg flex items-center space-x-4"
                 data-category="{{ $course->category }}"
                 onclick="showCourseDetails('{{ $course->courseID }}', '{{ $course->courseName }}', '{{ ucfirst($course->category) }}', '{{ $course->description }}', '{{ $course->studentCount }}', '{{ $course->capacityOffered }}', '{{ $course->progress }}', '{{ $courseImage }}')">
                <img src="{{ $courseImage }}" alt="Course Image" class="w-12 h-12 rounded-full">
                <div>
                    <p class="text-lg font-semibold">{{ $course->courseName }}</p>
                    <p class="text-sm text-gray-600">Category : {{ ucfirst($course->category) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </body>
</html>