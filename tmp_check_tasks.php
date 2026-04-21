<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MedicalExam;
use App\Models\DoctorTask;

$exams = MedicalExam::with(['uploader', 'pet.medicalRecords'])->get();
foreach ($exams as $exam) {
    $doctorIds = $exam->pet?->medicalRecords?->pluck('doctor_id')->unique()->values()->all() ?? [];
    echo "exam={$exam->id} uploaded_by_role=" . ($exam->uploader?->role ?? 'none') . " reviewed=" . ($exam->reviewed_by_doctor_at ? 'yes' : 'no') . " doctors=" . implode(',', $doctorIds) . PHP_EOL;
}
echo 'tasks=' . DoctorTask::count() . PHP_EOL;
