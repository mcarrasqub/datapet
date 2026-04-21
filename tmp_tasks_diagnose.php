<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DoctorTask;
use App\Models\MedicalExam;
use App\Models\MedicalRecord;
use App\Models\User;

$doctors = User::where('role', 'doctor')->orderBy('id')->get();
echo 'doctors=' . $doctors->count() . PHP_EOL;
foreach ($doctors as $doctor) {
    echo 'doctor=' . $doctor->id . ' ' . trim($doctor->name . ' ' . ($doctor->lastname ?? '')) . PHP_EOL;
}

echo 'tasks_total=' . DoctorTask::count() . PHP_EOL;
echo 'tasks_pending=' . DoctorTask::where('status', 'pending')->count() . PHP_EOL;
echo 'tasks_in_progress=' . DoctorTask::where('status', 'in_progress')->count() . PHP_EOL;
echo 'tasks_completed=' . DoctorTask::where('status', 'completed')->count() . PHP_EOL;
echo 'tasks_overdue_status=' . DoctorTask::where('status', 'overdue')->count() . PHP_EOL;

DoctorTask::orderBy('doctor_id')->orderBy('id')->get()->each(function ($task) {
    echo implode(' | ', [
        'task=' . $task->id,
        'doctor_id=' . $task->doctor_id,
        'status=' . $task->status,
        'source=' . ($task->source_type ?? 'manual'),
        'source_id=' . ($task->source_id ?? 'null'),
        'task_key=' . ($task->task_key ?? 'null'),
    ]) . PHP_EOL;
});

echo 'unreviewed_client_exams=' . MedicalExam::whereNull('reviewed_by_doctor_at')->whereHas('uploader', function ($q) { $q->where('role', 'client'); })->count() . PHP_EOL;
MedicalExam::with(['pet','uploader'])->whereNull('reviewed_by_doctor_at')->whereHas('uploader', function ($q) { $q->where('role', 'client'); })->get()->each(function ($exam) {
    echo implode(' | ', [
        'exam=' . $exam->id,
        'pet=' . ($exam->pet?->name ?? 'null'),
        'uploaded_by=' . ($exam->uploader?->role ?? 'null'),
        'reviewed=' . ($exam->reviewed_by_doctor_at ? 'yes' : 'no'),
    ]) . PHP_EOL;
});

echo 'incomplete_records=' . MedicalRecord::where(function ($query) {
    $query->whereNull('diagnosis')->orWhere('diagnosis', '')
        ->orWhereNull('treatment')->orWhere('treatment', '')
        ->orWhereNull('notes')->orWhere('notes', '');
})->count() . PHP_EOL;
MedicalRecord::orderBy('id')->get()->each(function ($r) {
    $missing = [];
    if (empty((string) $r->diagnosis)) { $missing[] = 'diagnosis'; }
    if (empty((string) $r->treatment)) { $missing[] = 'treatment'; }
    if (empty((string) $r->notes)) { $missing[] = 'notes'; }
    echo implode(' | ', [
        'record=' . $r->id,
        'doctor_id=' . ($r->doctor_id ?? 'null'),
        'missing=' . (count($missing) ? implode(',', $missing) : 'none'),
    ]) . PHP_EOL;
});
