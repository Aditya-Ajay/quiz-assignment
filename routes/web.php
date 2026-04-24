<?php

use App\Http\Controllers\AttemptController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/quizzes');

Route::resource('quizzes', QuizController::class);

Route::post('quizzes/{quiz}/questions', [QuestionController::class, 'store'])
    ->name('questions.store');
Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])
    ->name('questions.edit');
Route::put('questions/{question}', [QuestionController::class, 'update'])
    ->name('questions.update');
Route::delete('questions/{question}', [QuestionController::class, 'destroy'])
    ->name('questions.destroy');

Route::get('quizzes/{quiz}/take', [AttemptController::class, 'start'])
    ->name('attempts.start');
Route::post('attempts/{attempt}/submit', [AttemptController::class, 'submit'])
    ->name('attempts.submit');
Route::get('attempts/{attempt}', [AttemptController::class, 'show'])
    ->name('attempts.show');
