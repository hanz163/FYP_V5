<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Difficulty;
use App\Models\Answer;
use App\Models\Part;
use App\Services\OpenAIService;
use App\Services\DocxService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller {

    protected $openAIService;
    protected $docxService;

    public function __construct(OpenAIService $openAIService, DocxService $docxService) {
        $this->openAIService = $openAIService;
        $this->docxService = $docxService;
    }

    public function create($partID) {
        $part = Part::findOrFail($partID);
        $chapter = $part->chapter;
        $course = $chapter->course;
        $difficulties = Difficulty::all();

        return view('uploadQuestion', compact('part', 'chapter', 'course', 'difficulties'));
    }

    public function store(Request $request) {
        $request->validate([
            'part_id' => 'required|exists:parts,partID',
            'difficulty_id' => 'required|exists:difficulties,DifficultyID',
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'explanation' => 'required|string',
        ]);

        $newQuestionID = $this->generateCustomID(Question::class, 'QuestionID', 'Q');
        $newAnswerID = $this->generateCustomID(Answer::class, 'AnswerID', 'A');

        DB::transaction(function () use ($request, $newQuestionID, $newAnswerID) {
            $question = Question::create([
                        'QuestionID' => $newQuestionID,
                        'DifficultyID' => $request->difficulty_id,
                        'question_text' => $request->question_text,
            ]);

            Answer::create([
                'AnswerID' => $newAnswerID,
                'QuestionID' => $newQuestionID,
                'answer_text' => $request->correct_answer,
                'explanation' => $request->explanation,
            ]);
        });

        return back()->with('success', 'Question uploaded successfully!');
    }

    public function edit($questionID) {
        $question = Question::with('answer', 'difficulty.part.chapter.course')->findOrFail($questionID);
        $part = $question->difficulty->part;
        $chapter = $part->chapter;
        $course = $chapter->course;

        return view('editQuestion', compact('question', 'part', 'chapter', 'course'));
    }

    public function update(Request $request, $questionID) {
        $request->validate([
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'wrong_answer_1' => 'required|string',
            'wrong_answer_2' => 'required|string',
            'wrong_answer_3' => 'required|string',
            'explanation' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $questionID) {
            // Update the question
            $question = Question::findOrFail($questionID);
            $question->update(['question_text' => $request->question_text]);

            // Update the answer
            $answer = Answer::where('QuestionID', $questionID)->first();
            if ($answer) {
                $answer->update([
                    'answer_text' => $request->correct_answer,
                    'wrong_answer_1' => $request->wrong_answer_1,
                    'wrong_answer_2' => $request->wrong_answer_2,
                    'wrong_answer_3' => $request->wrong_answer_3,
                    'explanation' => $request->explanation,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function destroy($questionID) {
        Question::where('QuestionID', $questionID)->delete();
        return back()->with('success', 'Question deleted successfully!');
    }

    public function categorizeQuestion(Request $request) {
        $request->validate([
            'question' => 'required|string',
            'partID' => 'required|exists:parts,partID'
        ]);

        $questionText = $request->question;
        $partID = $request->partID;
        $difficultyLevel = $this->openAIService->levelQuestion($questionText, 'gpt-3.5-turbo');

        if ($difficultyLevel === 'Medium') {
            $difficultyLevel = 'Normal';
        }

        $difficulty = Difficulty::firstOrCreate(
                        ['partID' => $partID, 'level' => $difficultyLevel],
                        ['DifficultyID' => $this->generateCustomID(Difficulty::class, 'DifficultyID', 'D')]
        );

        return response()->json([
                    'message' => 'Question categorized successfully',
                    'difficulty_level' => $difficultyLevel,
                    'difficulty_id' => $difficulty->DifficultyID
        ]);
    }

    public function processQuestions(Request $request) {
        $request->validate(['file' => 'required|mimes:docx|max:5120']);
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');
        $absolutePath = storage_path('app/public/' . $path);

        if (!file_exists($absolutePath)) {
            return response()->json(['error' => 'File upload issue. Try again.'], 400);
        }

        $questions = $this->docxService->extractQuestions($absolutePath);
        if (empty($questions)) {
            return response()->json(['error' => 'No questions extracted from the file'], 400);
        }

        $categorizedQuestions = [
            'easy' => [],
            'normal' => [],
            'hard' => [],
        ];

        foreach ($questions as $question) {
            $difficultyLevel = $this->openAIService->levelQuestion($question['question']);
            $categorizedQuestions[strtolower($difficultyLevel)][] = [
                'question' => $question['question'],
                'correct_answer' => $question['correct_answer'],
                'wrong_answer_1' => $question['wrong_answers'][0] ?? '',
                'wrong_answer_2' => $question['wrong_answers'][1] ?? '',
                'wrong_answer_3' => $question['wrong_answers'][2] ?? '',
                'explanation' => $question['explanation'] ?? '',
            ];
        }

        // Store categorized questions in session
        session(['categorized_questions' => $categorizedQuestions]);

        return response()->json(['categorized_questions' => $categorizedQuestions]);
    }

    private function generateCustomID($model, $column, $prefix) {
        $latestRecord = $model::orderByRaw("CAST(SUBSTRING($column, 2) AS UNSIGNED) DESC")->first();
        $nextID = $latestRecord ? ((int) substr($latestRecord->$column, 1)) + 1 : 1;
        return $prefix . str_pad($nextID, 5, '0', STR_PAD_LEFT);
    }

    public function showUploadForm($courseID = null, $chapterID = null, $partID = null) {
        $courses = Course::with('chapters.parts')->get();

        $course = $courseID ? Course::find($courseID) : null;
        $chapter = $chapterID ? Chapter::find($chapterID) : null;
        $part = $partID ? Part::find($partID) : null;
        $parts = Part::where('chapterID', $chapterID)->get();

        return view('UploadQuestion', compact('courses', 'course', 'chapter', 'part', 'parts'));
    }

    public function saveQuestion(Request $request) {
        try {
            $request->validate([
                'questions' => 'required|array',
                'questions.*.question_text' => 'required|string',
                'questions.*.difficulty_level' => 'required|string',
                'questions.*.partID' => 'required|exists:parts,partID',
                'questions.*.correct_answer' => 'required|string',
                'questions.*.wrong_answer_1' => 'required|string',
                'questions.*.wrong_answer_2' => 'required|string',
                'questions.*.wrong_answer_3' => 'required|string',
                'questions.*.explanation' => 'required|string',
            ]);

            $questions = $request->input('questions');

            foreach ($questions as $questionData) {
                $difficulty = Difficulty::firstOrCreate(
                                ['partID' => $questionData['partID'], 'level' => ucfirst($questionData['difficulty_level'])],
                                ['DifficultyID' => $this->generateCustomID(Difficulty::class, 'DifficultyID', 'D')]
                );

                $question = Question::create([
                            'QuestionID' => $this->generateCustomID(Question::class, 'QuestionID', 'Q'),
                            'question_text' => $questionData['question_text'],
                            'DifficultyID' => $difficulty->DifficultyID,
                            'partID' => $questionData['partID'],
                ]);

                Answer::create([
                    'AnswerID' => $this->generateCustomID(Answer::class, 'AnswerID', 'A'),
                    'QuestionID' => $question->QuestionID,
                    'answer_text' => $questionData['correct_answer'],
                    'wrong_answer_1' => $questionData['wrong_answer_1'],
                    'wrong_answer_2' => $questionData['wrong_answer_2'],
                    'wrong_answer_3' => $questionData['wrong_answer_3'],
                    'explanation' => $questionData['explanation'],
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error saving questions: ' . $e->getMessage());
            return response()->json([
                        'success' => false,
                        'error' => 'An error occurred while saving the questions.',
                            ], 500);
        }
    }

    public function showQuestions($partID, Request $request) {
        // Fetch the part with its questions, answers, difficulty, and part relationship
        $part = Part::with(['questions.answers', 'questions.difficulty', 'chapter.course'])
                ->findOrFail($partID);

        // Fetch the chapter and course for display
        $chapter = $part->chapter;
        $course = $chapter->course;

        // Get the selected difficulty from the request
        $selectedDifficulty = $request->query('difficulty');

        // Filter questions based on the selected difficulty
        $filteredQuestions = $part->questions->filter(function ($question) use ($selectedDifficulty) {
            return strtolower($question->difficulty->level) === strtolower($selectedDifficulty);
        });

        // Pass the filtered questions to the view
        return view('AnswerQuestion', [
            'part' => $part,
            'chapter' => $chapter,
            'course' => $course,
            'questions' => $filteredQuestions, // Pass the filtered questions
        ]);
    }

    public function showModifyQuestions(Request $request) {
        $courses = Course::all();
        $selectedCourse = $request->query('course');
        $selectedChapter = $request->query('chapter');
        $selectedPart = $request->query('part');

        $chapters = $selectedCourse ? Chapter::where('courseID', $selectedCourse)->get() : collect();
        $parts = $selectedChapter ? Part::where('chapterID', $selectedChapter)->get() : collect();
        $questions = $selectedPart ? Question::where('partID', $selectedPart)->with('answers', 'difficulty')->get() : collect();

        $course = $selectedCourse ? Course::find($selectedCourse) : null;
        $chapter = $selectedChapter ? Chapter::find($selectedChapter) : null;
        $part = $selectedPart ? Part::find($selectedPart) : null;

        return view('ModifyQuestion', compact(
                        'courses', 'chapters', 'parts', 'questions',
                        'selectedCourse', 'selectedChapter', 'selectedPart',
                        'course', 'chapter', 'part'
        ));
    }

    public function bulkDelete(Request $request) {
        $request->validate([
            'questionIDs' => 'required|array',
            'questionIDs.*' => 'exists:questions,QuestionID',
        ]);

        Question::whereIn('QuestionID', $request->questionIDs)->delete();

        return response()->json(['success' => true]);
    }

    public function reorder(Request $request) {
        $request->validate([
            'orderData' => 'required|array',
            'orderData.*.difficulty' => 'required|string',
            'orderData.*.questionIDs' => 'required|array',
            'orderData.*.questionIDs.*' => 'exists:questions,QuestionID',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->orderData as $data) {
                $difficulty = Difficulty::where('level', ucfirst($data['difficulty']))->first();
                if (!$difficulty) {
                    continue; // Skip if difficulty not found
                }

                // Update the order and difficulty for each question
                foreach ($data['questionIDs'] as $index => $questionID) {
                    Question::where('QuestionID', $questionID)->update([
                        'order' => $index + 1, // Recalculate order based on the new position
                        'DifficultyID' => $difficulty->DifficultyID, // Update the difficulty
                    ]);
                }
            }
        });

        return response()->json(['success' => true]);
    }

    public function addQuestionManually(Request $request) {
        $request->validate([
            'part_id' => 'required|exists:parts,partID',
            'difficulty' => 'required|in:easy,normal,hard', // Ensure difficulty is validated
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'wrong_answer_1' => 'required|string',
            'wrong_answer_2' => 'required|string',
            'wrong_answer_3' => 'required|string',
            'explanation' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // Generate IDs
            $newQuestionID = $this->generateCustomID(Question::class, 'QuestionID', 'Q');
            $newAnswerID = $this->generateCustomID(Answer::class, 'AnswerID', 'A');

            // Find or create the difficulty
            $difficulty = Difficulty::firstOrCreate(
                            ['partID' => $request->part_id, 'level' => ucfirst($request->difficulty)], // Use the selected difficulty
                            ['DifficultyID' => $this->generateCustomID(Difficulty::class, 'DifficultyID', 'D')]
            );

            // Create the question
            $question = Question::create([
                        'QuestionID' => $newQuestionID,
                        'DifficultyID' => $difficulty->DifficultyID, // Assign the correct DifficultyID
                        'partID' => $request->part_id,
                        'question_text' => $request->question_text,
            ]);

            // Create the answer
            Answer::create([
                'AnswerID' => $newAnswerID,
                'QuestionID' => $newQuestionID,
                'answer_text' => $request->correct_answer,
                'wrong_answer_1' => $request->wrong_answer_1,
                'wrong_answer_2' => $request->wrong_answer_2,
                'wrong_answer_3' => $request->wrong_answer_3,
                'explanation' => $request->explanation,
            ]);
        });

        return back()->with('success', 'Question added successfully!');
    }

    public function addQuestionWithAI(Request $request) {
        $request->validate([
            'file' => 'required|mimes:docx|max:5120',
            'courseID' => 'required|exists:courses,courseID',
            'chapterID' => 'required|exists:chapters,chapterID',
            'partID' => 'required|exists:parts,partID',
            'difficulty' => 'required|in:easy,normal,hard', // Ensure difficulty is validated
        ]);

        // Store the uploaded file
        $file = $request->file('file');
        $path = $file->store('uploads', 'public');
        $absolutePath = storage_path('app/public/' . $path);

        // Verify the file exists
        if (!file_exists($absolutePath)) {
            return response()->json(['error' => 'File upload issue. Try again.'], 400);
        }

        // Extract questions from the DOCX file
        $questions = $this->docxService->extractQuestions($absolutePath);

        if (empty($questions)) {
            return response()->json(['error' => 'No questions extracted from the file.'], 400);
        }

        // Save questions to the database
        DB::beginTransaction();
        try {
            foreach ($questions as $questionData) {
                // Find or create the difficulty level
                $difficulty = Difficulty::firstOrCreate(
                                ['partID' => $request->partID, 'level' => ucfirst($request->difficulty)],
                                ['DifficultyID' => $this->generateCustomID(Difficulty::class, 'DifficultyID', 'D')]
                );

                // Generate IDs for the question and answer
                $newQuestionID = $this->generateCustomID(Question::class, 'QuestionID', 'Q');
                $newAnswerID = $this->generateCustomID(Answer::class, 'AnswerID', 'A');

                // Save the question
                Question::create([
                    'QuestionID' => $newQuestionID,
                    'DifficultyID' => $difficulty->DifficultyID,
                    'partID' => $request->partID,
                    'question_text' => $questionData['question'],
                ]);

                // Save the answer
                Answer::create([
                    'AnswerID' => $newAnswerID,
                    'QuestionID' => $newQuestionID,
                    'answer_text' => $questionData['correct_answer'],
                    'wrong_answer_1' => $questionData['wrong_answers'][0] ?? '',
                    'wrong_answer_2' => $questionData['wrong_answers'][1] ?? '',
                    'wrong_answer_3' => $questionData['wrong_answers'][2] ?? '',
                    'explanation' => $questionData['explanation'] ?? '',
                ]);
            }

            // Commit the transaction
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Questions processed and saved successfully!']);
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            \Log::error('Error saving questions: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                        'success' => false,
                        'error' => 'An error occurred while saving the questions.',
                            ], 500);
        }
    }

    // Edit Question - Specific to Modify Questions Page
    public function editQuestionModify(Request $request, $questionID) {
        $request->validate([
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'wrong_answer_1' => 'required|string',
            'wrong_answer_2' => 'required|string',
            'wrong_answer_3' => 'required|string',
            'explanation' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $questionID) {
            // Update the question
            $question = Question::findOrFail($questionID);
            $question->update(['question_text' => $request->question_text]);

            // Update the answer
            $answer = Answer::where('QuestionID', $questionID)->first();
            if ($answer) {
                $answer->update([
                    'answer_text' => $request->correct_answer,
                    'wrong_answer_1' => $request->wrong_answer_1,
                    'wrong_answer_2' => $request->wrong_answer_2,
                    'wrong_answer_3' => $request->wrong_answer_3,
                    'explanation' => $request->explanation,
                ]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function deleteQuestionModify($questionID) {
        DB::transaction(function () use ($questionID) {
            Answer::where('QuestionID', $questionID)->delete();
            Question::where('QuestionID', $questionID)->delete();
        });

        return response()->json(['success' => true]);
    }
}
