<?php

use App\Http\Controllers\ExerciseController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes for Exercises
 * 
 * Endpoints:
 * POST   /api/exercises              - Criar novo exercício
 * GET    /api/exercises              - Listar todos os exercícios
 * GET    /api/exercises/{id}         - Obter exercício específico
 * PUT    /api/exercises/{id}         - Atualizar exercício
 * DELETE /api/exercises/{id}         - Deletar exercício
 */

Route::apiResource('exercises', ExerciseController::class);
