<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalRecordRequest;
use App\Http\Requests\UpdateMedicalRecordRequest;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function index(): View
    {
        $this->ensureDoctorOrAdmin();

        $pets = Pet::with('medicalRecords.doctor', 'medicalRecords.observations.doctor')->get();

        $selectedPet = $pets->first();
        $medicalRecords = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->get() : collect();
        $lastVisit = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->first() : null;
        $medicalExams = $selectedPet
            ? $selectedPet->medicalExams()->with(['uploader', 'medicalRecord'])->orderByDesc('uploaded_at')->get()
            : collect();
        $vaccinations = $selectedPet
            ? $selectedPet->vaccinations()->with('doctor')->orderByDesc('vaccinated_at')->get()
            : collect();

        $viewData = [];
        $viewData['pets'] = $pets;
        $viewData['selectedPet'] = $selectedPet;
        $viewData['medicalRecords'] = $medicalRecords;
        $viewData['lastVisit'] = $lastVisit;
        $viewData['medicalExams'] = $medicalExams;
        $viewData['vaccinations'] = $vaccinations;

        return view('medical_records.medical_records', $viewData);
    }

    public function show(Pet $pet): View
    {
        $this->ensureDoctorOrAdmin();

        $pets = Pet::with('medicalRecords.doctor', 'medicalRecords.observations.doctor')->get();
        $medicalRecords = $pet->medicalRecords()->orderByDesc('visited_at')->get();
        $lastVisit = $pet->medicalRecords()->orderByDesc('visited_at')->first();
        $medicalExams = $pet->medicalExams()
            ->with(['uploader', 'medicalRecord'])
            ->orderByDesc('uploaded_at')
            ->get();
        $vaccinations = $pet->vaccinations()
            ->with('doctor')
            ->orderByDesc('vaccinated_at')
            ->get();

        $viewData = [];
        $viewData['pets'] = $pets;
        $viewData['selectedPet'] = $pet;
        $viewData['medicalRecords'] = $medicalRecords;
        $viewData['lastVisit'] = $lastVisit;
        $viewData['medicalExams'] = $medicalExams;
        $viewData['vaccinations'] = $vaccinations;

        return view('medical_records.medical_records', $viewData);
    }

    public function create(Pet $pet): View
    {
        $this->ensureDoctorOrAdmin();

        $viewData = [];
        $viewData['pet'] = $pet;

        return view('medical_records.create', $viewData);
    }

    public function store(StoreMedicalRecordRequest $request, Pet $pet): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $validated = $request->validated();
        $validated['notes'] = $validated['observation'] ?? null;
        unset($validated['observation']);

        // Procesar fotos
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo) {
                    $path = $photo->store('medical_records', 'public');
                    $photos[] = $path;
                }
            }
        }

        $validated['pet_id'] = $pet->id;
        $validated['doctor_id'] = Auth::id();
        $validated['photos'] = $photos;

        MedicalRecord::create($validated);

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Registro médico creado con éxito');
    }

    public function edit(MedicalRecord $medicalRecord): View
    {
        $this->ensureDoctorOrAdmin();

        $viewData = [];
        $viewData['record'] = $medicalRecord;
        $viewData['pet'] = $medicalRecord->pet;

        return view('medical_records.edit', $viewData);
    }

    public function update(UpdateMedicalRecordRequest $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $validated = $request->validated();
        $validated['notes'] = $validated['observation'] ?? null;
        unset($validated['observation']);

        // Procesar fotos
        $photos = $medicalRecord->photos ?? [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo && count($photos) < 3) {
                    $path = $photo->store('medical_records', 'public');
                    $photos[] = $path;
                }
            }
        }

        $validated['photos'] = $photos;
        $medicalRecord->update($validated);

        return redirect()->route('medical_records.show', $medicalRecord->pet)
            ->with('success', 'Registro médico actualizado con éxito');
    }

    public function destroy(MedicalRecord $medicalRecord): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        // Eliminar fotos
        if ($medicalRecord->photos) {
            foreach ($medicalRecord->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }

        $petId = $medicalRecord->pet_id;
        $medicalRecord->delete();

        return redirect()->route('medical_records.show', $petId)
            ->with('success', 'Registro médico eliminado con éxito');
    }

    private function ensureDoctorOrAdmin(): void
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }
}
