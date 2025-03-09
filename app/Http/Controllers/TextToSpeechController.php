<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Aws\Polly\PollyClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TextToSpeechController extends Controller
{
    public function convertTextToSpeech(Request $request)
    {
        $text = $request->input('text');
        if (!$text) {
            return response()->json(['error' => 'Text parameter is required'], 400);
        }

        // Generate a unique cache key for the text
        $cacheKey = 'tts_' . md5($text);
        $cachePath = 'tts_cache/' . $cacheKey . '.mp3';

        // Check if the audio is already cached
        if (Storage::exists($cachePath)) {
            Log::info('Serving cached audio for text:', ['text' => $text]);
            return response(Storage::get($cachePath))
                ->header('Content-Type', 'audio/mpeg')
                ->header('Content-Disposition', 'inline; filename="audio.mp3"');
        }

        Log::info('Converting text to speech using AWS Polly:', ['text' => $text]);

        try {
            // Initialize the AWS Polly client
            $pollyClient = new PollyClient([
                'version' => 'latest',
                'region'  => env('AWS_DEFAULT_REGION', 'us-east-1'),
                'credentials' => [
                    'key'    => env('AWS_ACCESS_KEY_ID'),
                    'secret' => env('AWS_SECRET_ACCESS_KEY'),
                ],
            ]);

            // Wrap the text in SSML to control speed and pitch
            $ssmlText = "<speak><prosody rate='slow' pitch='medium'>" . $text . "</prosody></speak>";

            // Split the text into chunks of 3000 characters
            $textChunks = str_split($ssmlText, 3000);
            $audioContent = '';

            // Process chunks in parallel using async requests
            $promises = [];
            foreach ($textChunks as $chunk) {
                $promises[] = $pollyClient->synthesizeSpeechAsync([
                    'Text' => $chunk,
                    'OutputFormat' => 'mp3',
                    'VoiceId' => 'Joanna', // Change the voice as needed
                    'TextType' => 'ssml', // Specify that the text is SSML
                ]);
            }

            // Wait for all promises to complete
            $results = \GuzzleHttp\Promise\Utils::settle($promises)->wait();

            // Combine the audio content
            foreach ($results as $result) {
                if ($result['state'] === 'fulfilled') {
                    $audioContent .= $result['value']['AudioStream']->getContents();
                } else {
                    throw new \Exception('Failed to synthesize speech for a chunk.');
                }
            }

            // Cache the audio file
            Storage::put($cachePath, $audioContent);

            Log::info('Text-to-speech conversion successful');
            return response($audioContent)
                ->header('Content-Type', 'audio/mpeg')
                ->header('Content-Disposition', 'inline; filename="audio.mp3"');
        } catch (\Exception $e) {
            Log::error('Error converting text to speech using AWS Polly:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}