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

        $pets = Pet::with(['medicalRecords.doctor', 'medicalRecords.observations.doctor', 'appointments.doctor', 'kardexEntries.doctor', 'medicalFormulas.doctor', 'medicalOrders.doctor'])->get();

        $selectedPet = $pets->first();
        $medicalRecords = $selectedPet
            ? $selectedPet->medicalRecords()->with(['doctor', 'observations.doctor'])->orderByDesc('visited_at')->get()
            : collect();
        $lastVisit = $selectedPet ? $selectedPet->medicalRecords()->orderByDesc('visited_at')->first() : null;
        $medicalExams = $selectedPet
            ? $selectedPet->medicalExams()->with(['uploader', 'medicalRecord'])->orderByDesc('uploaded_at')->get()
            : collect();
        $vaccinations = $selectedPet
            ? $selectedPet->vaccinations()->with('doctor')->orderByDesc('vaccinated_at')->get()
            : collect();
        $appointments = $selectedPet
            ? $selectedPet->appointments()->with('doctor')->orderByDesc('date')->orderByDesc('start_time')->get()
            : collect();
        $kardexEntries = $selectedPet
            ? $selectedPet->kardexEntries()->with('doctor')->orderByDesc('entry_date')->orderByDesc('created_at')->get()
            : collect();
        $medicalFormulas = $selectedPet
            ? $selectedPet->medicalFormulas()->with('doctor')->orderByDesc('formula_date')->orderByDesc('created_at')->get()
            : collect();
        $medicalOrders = $selectedPet
            ? $selectedPet->medicalOrders()->with('doctor')->orderByDesc('order_date')->orderByDesc('created_at')->get()
            : collect();

        $viewData = [];
        $viewData['pets'] = $pets;
        $viewData['selectedPet'] = $selectedPet;
        $viewData['medicalRecords'] = $medicalRecords;
        $viewData['lastVisit'] = $lastVisit;
        $viewData['medicalExams'] = $medicalExams;
        $viewData['vaccinations'] = $vaccinations;
        $viewData['appointments'] = $appointments;
        $viewData['kardexEntries'] = $kardexEntries;
        $viewData['medicalFormulas'] = $medicalFormulas;
        $viewData['medicalOrders'] = $medicalOrders;

        return view('medical_records.medical_records', $viewData);
    }

    public function show(Pet $pet): View
    {
        $this->ensureDoctorOrAdmin();

        $pets = Pet::with(['medicalRecords.doctor', 'medicalRecords.observations.doctor', 'appointments.doctor', 'kardexEntries.doctor', 'medicalFormulas.doctor', 'medicalOrders.doctor'])->get();
        $medicalRecords = $pet->medicalRecords()->with(['doctor', 'observations.doctor'])->orderByDesc('visited_at')->get();
        $lastVisit = $pet->medicalRecords()->orderByDesc('visited_at')->first();
        $medicalExams = $pet->medicalExams()
            ->with(['uploader', 'medicalRecord'])
            ->orderByDesc('uploaded_at')
            ->get();
        $vaccinations = $pet->vaccinations()
            ->with('doctor')
            ->orderByDesc('vaccinated_at')
            ->get();
        $appointments = $pet->appointments()
            ->with('doctor')
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get();
        $kardexEntries = $pet->kardexEntries()
            ->with('doctor')
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->get();
        $medicalFormulas = $pet->medicalFormulas()
            ->with('doctor')
            ->orderByDesc('formula_date')
            ->orderByDesc('created_at')
            ->get();
        $medicalOrders = $pet->medicalOrders()
            ->with('doctor')
            ->orderByDesc('order_date')
            ->orderByDesc('created_at')
            ->get();

        $viewData = [];
        $viewData['pets'] = $pets;
        $viewData['selectedPet'] = $pet;
        $viewData['medicalRecords'] = $medicalRecords;
        $viewData['lastVisit'] = $lastVisit;
        $viewData['medicalExams'] = $medicalExams;
        $viewData['vaccinations'] = $vaccinations;
        $viewData['appointments'] = $appointments;
        $viewData['kardexEntries'] = $kardexEntries;
        $viewData['medicalFormulas'] = $medicalFormulas;
        $viewData['medicalOrders'] = $medicalOrders;

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

    public function updatePet(\Illuminate\Http\Request $request, Pet $pet): RedirectResponse
    {
        $this->ensureDoctorOrAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|max:255',
            'breed' => 'nullable|string|max:255',
            'age' => 'nullable|integer|min:0|max:200',
            'gender' => 'required|in:male,female,unknown',
            'weight' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'reproductive_status' => 'nullable|string|max:255',
            'is_deceased' => 'required|boolean',
            'emotional_support' => 'required|boolean',
            'service_animal' => 'required|boolean',
            'diet' => 'nullable|string|max:1000',
            'diet_quantity' => 'nullable|string|max:255',
            'diet_frequency' => 'nullable|string|max:255',
            'housing' => 'nullable|string|max:1000',
            'bath_frequency' => 'nullable|string|max:255',
            'bath_products' => 'nullable|string|max:255',
            'other_pets' => 'nullable|string|max:255',
            'last_heat' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($pet->getPhoto()) {
                Storage::disk('public')->delete($pet->getPhoto());
            }
            $validated['photo'] = $request->file('photo')->store('pets', 'public');
        }

        $pet->update($validated);

        return redirect()->route('medical_records.show', $pet)
            ->with('success', 'Datos de la mascota actualizados exitosamente.');
    }

    private function ensureDoctorOrAdmin(): void
    {
        $role = (string) (Auth::user()->role ?? '');

        if (! in_array($role, ['admin', 'doctor'], true)) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }
}
