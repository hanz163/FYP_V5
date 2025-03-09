<?php

require 'vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;

$textToSpeechClient = new TextToSpeechClient([
    'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
]);

$input = new SynthesisInput();
$input->setText('Hello, world!');

$voice = new VoiceSelectionParams();
$voice->setLanguageCode('en-US');

$audioConfig = new AudioConfig();
$audioConfig->setAudioEncoding(AudioEncoding::MP3);

$response = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);
file_put_contents('output.mp3', $response->getAudioContent());

echo 'Audio content written to output.mp3';