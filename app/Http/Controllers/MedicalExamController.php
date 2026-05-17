<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicalExamRequest;
use App\Models\DoctorTask;
use App\Models\MedicalExam;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MedicalExamController extends Controller
{
    public function store(StoreMedicalExamRequest $request, Pet $pet): RedirectResponse
    {
        $this->ensureCanUpload($pet);

        $validated = $request->validated();

        if (! empty($validated['medical_record_id']) && ! $pet->medicalRecords()->where('id', $validated['medical_record_id'])->exists()) {
            return back()->withErrors([
                'medical_record_id' => 'La consulta seleccionada no pertenece a esta mascota.',
            ])->withInput();
        }

        $uploadedCount = 0;

        foreach ($request->file('files', []) as $file) {
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $uniqueFileName = Str::uuid()->toString().'.'.$extension;
            $relativePath = 'medical_exams/pet_'.$pet->id;
            $storedPath = $file->storeAs($relativePath, $uniqueFileName, 'local');

            MedicalExam::create([
                'pet_id' => $pet->id,
                'medical_record_id' => $validated['medical_record_id'] ?? null,
                'medical_order_id' => $validated['medical_order_id'] ?? null,
                'uploaded_by' => (int) Auth::id(),
                'title' => $validated['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'description' => $validated['description'] ?? null,
                'category' => $validated['category'] ?? null,
                'exam_date' => $validated['exam_date'] ?? null,
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $storedPath,
                'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
                'file_size' => (int) $file->getSize(),
                'uploaded_at' => now(),
            ]);

            $uploadedCount++;
        }

        if (Auth::user()->role === 'client') {
            return redirect()->route('pets.exams', ['pet_id' => $pet->id])
                ->with('success', 'Se cargaron '.$uploadedCount.' archivo(s) de examen correctamente.');
        }

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Se cargaron '.$uploadedCount.' archivo(s) de examen correctamente.');
    }

    public function view(MedicalExam $medicalExam): BinaryFileResponse
    {
        $this->ensureCanAccessPet($medicalExam->pet);

        if (Auth::user()->role === 'doctor' && ! $medicalExam->reviewed_by_doctor_at) {
            $medicalExam->update([
                'reviewed_by_doctor_id' => (int) Auth::id(),
                'reviewed_by_doctor_at' => now(),
            ]);

            // Si está enlazado a una orden médica, marcar esa orden como completada
            if ($medicalExam->medical_order_id) {
                $medicalExam->medicalOrder()->update(['status' => 'completed']);
            }

            // Marcar la tarea automática de revisión de examen como completada
            $taskKey = 'doctor:'.Auth::id().':exam:'.$medicalExam->id.':review';
            $task = DoctorTask::where('task_key', $taskKey)
                ->where('doctor_id', Auth::id())
                ->first();

            if ($task) {
                $task->update(['status' => 'completed']);
            }
        }

        if (! Storage::disk('local')->exists($medicalExam->file_path)) {
            abort(404, 'El archivo no existe.');
        }

        $absolutePath = Storage::disk('local')->path($medicalExam->file_path);

        return response()->file($absolutePath, [
            'Content-Type' => $medicalExam->mime_type,
            'Content-Disposition' => 'inline; filename="'.addslashes($medicalExam->original_name).'"',
        ]);
    }

    public function download(MedicalExam $medicalExam): BinaryFileResponse
    {
        $this->ensureCanAccessPet($medicalExam->pet);

        if (! Storage::disk('local')->exists($medicalExam->file_path)) {
            abort(404, 'El archivo no existe.');
        }

        return response()->download(Storage::disk('local')->path($medicalExam->file_path), $medicalExam->original_name);
    }

    private function ensureCanUpload(Pet $pet): void
    {
        $user = Auth::user();
        $role = (string) ($user->role ?? '');

        if (in_array($role, ['admin', 'doctor'], true)) {
            return;
        }

        if ($role === 'client' && (int) $pet->user_id === (int) $user->id) {
            return;
        }

        if ($role === 'client') {
            abort(403, 'Solo puedes subir exámenes para tus propias mascotas.');
        }

        if (! in_array($role, ['admin', 'doctor', 'client'], true)) {
            abort(403, 'No tienes permisos para subir exámenes.');
        }
    }

    private function ensureCanAccessPet(Pet $pet): void
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        if (in_array($user->role, ['admin', 'doctor'], true)) {
            return;
        }

        if ($user->role === 'client' && (int) $pet->user_id === (int) $user->id) {
            return;
        }

        abort(403, 'No tienes permisos para acceder a este examen.');
    }
}
