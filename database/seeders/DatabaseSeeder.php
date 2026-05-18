<?php

namespace Database\Seeders;

use App\Models\AdoptionRequest;
use App\Models\Appointment;
use App\Models\DoctorTask;
use App\Models\KardexEntry;
use App\Models\MedicalExam;
use App\Models\MedicalFormula;
use App\Models\MedicalOrder;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Desactivar restricciones de clave foránea para poder truncar las tablas
        Schema::disableForeignKeyConstraints();

        // Truncar las tablas en orden para vaciar la base de datos de manera limpia
        AdoptionRequest::truncate();
        Appointment::truncate();
        Vaccination::truncate();
        MedicalRecord::truncate();
        KardexEntry::truncate();
        MedicalFormula::truncate();
        MedicalOrder::truncate();
        MedicalExam::truncate();
        DoctorTask::truncate();
        Pet::truncate();
        User::truncate();

        // Reactivar restricciones de clave foránea
        Schema::enableForeignKeyConstraints();

        // Copiar archivos de prueba reales desde public/default-assets a storage
        $sourceNoodle = public_path('default-assets/fake_noodle_exam.pdf');
        $sourceZiggy = public_path('default-assets/fake_ziggy_exam.pdf');

        if (\Illuminate\Support\Facades\File::exists($sourceNoodle)) {
            \Illuminate\Support\Facades\Storage::disk('local')->put('medical_exams/fake_noodle_exam.pdf', \Illuminate\Support\Facades\File::get($sourceNoodle));
        } else {
            \Illuminate\Support\Facades\Storage::disk('local')->put('medical_exams/fake_noodle_exam.pdf', '%PDF-1.4 dummy exam');
        }

        if (\Illuminate\Support\Facades\File::exists($sourceZiggy)) {
            \Illuminate\Support\Facades\Storage::disk('local')->put('medical_exams/fake_ziggy_exam.pdf', \Illuminate\Support\Facades\File::get($sourceZiggy));
        } else {
            \Illuminate\Support\Facades\Storage::disk('local')->put('medical_exams/fake_ziggy_exam.pdf', '%PDF-1.4 dummy exam');
        }

        // Copiar Fotos de Mascotas
        $mascotasNames = ['noodle', 'ziggy', 'coco', 'rex', 'tambor', 'spike', 'cleo'];
        $fotosMascotas = [];

        foreach ($mascotasNames as $m) {
            foreach (['jpg', 'png', 'jpeg', 'webp'] as $ext) {
                $sourceFoto = public_path("default-assets/foto_{$m}.{$ext}");
                if (\Illuminate\Support\Facades\File::exists($sourceFoto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->put("pets/foto_{$m}.{$ext}", \Illuminate\Support\Facades\File::get($sourceFoto));
                    $fotosMascotas[$m] = "pets/foto_{$m}.{$ext}";
                    break;
                }
            }
        }

        // 1. Creación de Usuarios de Prueba
        $admin = User::create([
            'id' => 1000000001,
            'name' => 'Administrador',
            'lastname' => 'Datapet',
            'email' => 'admin@datapet.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '123456789010',
            'address' => 'Calle Principal 123',
            'status' => true,
        ]);

        $doctor = User::create([
            'id' => 1000000002,
            'name' => 'Dr. Carlos',
            'lastname' => 'López',
            'email' => 'doctor@datapet.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'phone' => '098765435621',
            'address' => 'Avenida Sur 456',
            'status' => true,
        ]);

        $client1 = User::create([
            'id' => 1000000003,
            'name' => 'Mariana',
            'lastname' => 'Carrasquilla',
            'email' => 'client@datapet.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '573233432225',
            'address' => 'El Poblado, Medellín',
            'status' => true,
        ]);

        $client2 = User::create([
            'id' => 1000000004,
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'phone' => '573214842344',
            'address' => 'Laureles, Medellín',
            'status' => true,
        ]);

        // 2. Creación de Mascotas Exóticas de Clientes
        $noodle = Pet::create([
            'user_id' => $client1->id,
            'name' => 'Noodle',
            'photo' => $fotosMascotas['noodle'] ?? null,
            'species' => 'Hurón',
            'breed' => 'Sable',
            'age' => 2,
            'gender' => 'male',
            'weight' => 1.20,
            'color' => 'Sable',
            'size' => 'Pequeña',
            'reproductive_status' => 'Esterilizado',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Alimento balanceado premium para hurones y pollo cocido',
            'diet_quantity' => '50g al día',
            'diet_frequency' => '2 veces al día',
            'housing' => 'Jaula espaciosa de 3 niveles con hamacas y túneles',
            'bath_frequency' => 'Cada 2 meses',
            'bath_products' => 'Champú hidratante especial para hurones',
            'other_pets' => 'Erizo y loro',
            'last_heat' => 'N/A',
            'available_for_adoption' => false,
        ]);

        $ziggy = Pet::create([
            'user_id' => $client1->id,
            'name' => 'Ziggy',
            'photo' => $fotosMascotas['ziggy'] ?? null,
            'species' => 'Erizo de Tierra',
            'breed' => 'Pigmio Africano',
            'age' => 1,
            'gender' => 'male',
            'weight' => 0.45,
            'color' => 'Sal y pimienta',
            'size' => 'Pequeña',
            'reproductive_status' => 'Entero',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Alimento premium para insectívoros y tenebrios',
            'diet_quantity' => '2 cucharadas de alimento seco y 5 tenebrios',
            'diet_frequency' => 'Nocturna (1 vez al día)',
            'housing' => 'Terrario de madera con rueda de ejercicio sin rejilla',
            'bath_frequency' => 'Mensual (baño de avena tibio)',
            'bath_products' => 'Champú neutro hipoalergénico de avena',
            'other_pets' => 'Hurón y loro',
            'last_heat' => 'N/A',
            'available_for_adoption' => false,
        ]);

        $coco = Pet::create([
            'user_id' => $client1->id,
            'name' => 'Coco',
            'photo' => $fotosMascotas['coco'] ?? null,
            'species' => 'Loro',
            'breed' => 'Cabeza Amarilla',
            'age' => 15,
            'gender' => 'female',
            'weight' => 0.48,
            'color' => 'Verde brillante con cabeza amarilla',
            'size' => 'Mediana',
            'reproductive_status' => 'Entero',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Mezcla de semillas, pellets especializados, frutas y verduras frescas',
            'diet_quantity' => '1 taza al día',
            'diet_frequency' => 'Diario',
            'housing' => 'Pajarera amplia con ramas naturales y juguetes cognitivos',
            'bath_frequency' => 'Semanal (con atomizador de agua tibia)',
            'bath_products' => 'Solo agua limpia sin químicos',
            'other_pets' => 'Erizo y huron',
            'last_heat' => 'N/A',
            'available_for_adoption' => false,
        ]);

        $rex = Pet::create([
            'user_id' => $client2->id,
            'name' => 'Rex',
            'photo' => $fotosMascotas['rex'] ?? null,
            'species' => 'Iguana',
            'breed' => 'Iguana Verde',
            'age' => 4,
            'gender' => 'male',
            'weight' => 4.50,
            'color' => 'Verde esmeralda',
            'size' => 'Grande',
            'reproductive_status' => 'Entero',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Hojas de mostaza, dientes de león, calabacín y frutas tropicales',
            'diet_quantity' => 'Plato abundante de 300g',
            'diet_frequency' => 'Diario por la mañana',
            'housing' => 'Terrario vertical gigante de 2x1.5m con zona de asoleamiento UVB',
            'bath_frequency' => 'Diario (baño de inmersión en agua tibia de 20 min)',
            'bath_products' => 'Ninguno (solo agua)',
            'other_pets' => 'Ninguna',
            'last_heat' => 'N/A',
            'available_for_adoption' => false,
        ]);

        $tambor = Pet::create([
            'user_id' => $client2->id,
            'name' => 'Tambor',
            'photo' => $fotosMascotas['tambor'] ?? null,
            'species' => 'Conejo',
            'breed' => 'Cabeza de León',
            'age' => 1,
            'gender' => 'male',
            'weight' => 1.80,
            'color' => 'Gris',
            'size' => 'Pequeña',
            'reproductive_status' => 'Esterilizado',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Heno de alfalfa premium, pellets y hojas de diente de león',
            'diet_quantity' => '100g de heno diario',
            'diet_frequency' => 'Diario',
            'housing' => 'Jaula grande con área de juegos libre',
            'bath_frequency' => 'No se baña (solo cepillado)',
            'bath_products' => 'Ninguno',
            'other_pets' => 'Iguana',
            'last_heat' => 'N/A',
            'available_for_adoption' => false,
        ]);

        // 3. Creación de Mascotas Exóticas Disponibles para Adopción (asociadas al administrador/albergue)
        $spike = Pet::create([
            'user_id' => $admin->id,
            'name' => 'Spike',
            'photo' => $fotosMascotas['spike'] ?? null,
            'species' => 'Dragón Barbudo',
            'breed' => 'Central',
            'age' => 2,
            'gender' => 'male',
            'weight' => 0.38,
            'color' => 'Naranja y amarillo',
            'size' => 'Mediana',
            'reproductive_status' => 'Entero',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Grillos, cucarachas dubia y ensalada de hojas de mostaza',
            'diet_quantity' => '10-15 insectos e plato de verduras',
            'diet_frequency' => 'Interdiario (insectos) / Diario (verduras)',
            'housing' => 'Terrario desértico de 120x60x60 cm con luces UVB 10.0 y foco de calor',
            'bath_frequency' => '2 veces por semana (para hidratación y muda)',
            'bath_products' => 'Ninguno (solo agua)',
            'other_pets' => 'Ninguna',
            'last_heat' => 'N/A',
            'available_for_adoption' => true,
            'adoption_description' => 'Spike es un dragón barbudo rescatado, extremadamente manso y habituado al contacto humano. Busca una familia que tenga o pueda instalar un terrario adecuado con luces UVB y control de temperatura caliente.',
        ]);

        $cleo = Pet::create([
            'user_id' => $admin->id,
            'name' => 'Cleo',
            'photo' => $fotosMascotas['cleo'] ?? null,
            'species' => 'Serpiente',
            'breed' => 'Serpiente del Maíz',
            'age' => 3,
            'gender' => 'female',
            'weight' => 0.90,
            'color' => 'Naranja y rojo albino',
            'size' => 'Mediana (1.2 metros)',
            'reproductive_status' => 'Entera',
            'is_deceased' => false,
            'emotional_support' => false,
            'service_animal' => false,
            'diet' => 'Ratones medianos descongelados',
            'diet_quantity' => '1 ratón mediano',
            'diet_frequency' => 'Cada 7-10 días',
            'housing' => 'Terrario seguro a prueba de escapes con manta térmica regulada por termostato',
            'bath_frequency' => 'No requiere (solo recipiente de agua grande para automudarse)',
            'bath_products' => 'Ninguno',
            'other_pets' => 'Ninguna (solitaria)',
            'last_heat' => 'N/A',
            'available_for_adoption' => true,
            'adoption_description' => 'Cleo es una serpiente del maíz adulta con una hermosa coloración naranja y roja. Es muy tranquila e ideal para principiantes en la herpetología. Requiere un terrario seguro a prueba de fugas y calefacción.',
        ]);

        // 4. Creación de Historiales Clínicos (Medical Records)
        MedicalRecord::create([
            'pet_id' => $ziggy->id,
            'doctor_id' => $doctor->id,
            'visited_at' => Carbon::now()->subDays(2),
            'reason' => 'Control general de púas y corte de uñas',
            'diagnosis' => 'Erizo en excelente estado. Piel sana, hidratada y púas firmes sin signos de ácaros.',
            'treatment' => 'Continuar con su dieta de alimento balanceado complementado con insectos vivos. Mantener rueda de ejercicio limpia.',
            'notes' => 'Se enrolló al inicio pero luego cooperó perfectamente al ofrecerle comida.',
        ]);

        MedicalRecord::create([
            'pet_id' => $coco->id,
            'doctor_id' => $doctor->id,
            'visited_at' => Carbon::now()->subDays(10),
            'reason' => 'Pérdida inusual de plumas en la zona del pecho',
            'diagnosis' => 'Picaje leve por estrés conductual, probablemente detonado por cambio reciente en la jaula.',
            'treatment' => 'Implementar juguetes de forrajeo para estimular su mente y devolver la jaula a su lugar original y tranquilo.',
            'notes' => 'Comportamiento muy activo y excelente apetito. Raspado de plumas negativo para parásitos o bacterias.',
        ]);

        // 5. Creación de Vacunas (Vaccinations)
        Vaccination::create([
            'pet_id' => $noodle->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Moquillo (Distemper Hurón)',
            'vaccinated_at' => Carbon::now()->subDays(2),
            'next_due_date' => Carbon::now()->subDays(2)->addYear(),
            'notes' => 'Dosis de refuerzo anual recomendada para hurones. Inyección subcutánea bien tolerada.',
        ]);

        Vaccination::create([
            'pet_id' => $noodle->id,
            'doctor_id' => $doctor->id,
            'vaccine_type' => 'Antirrábica',
            'vaccinated_at' => Carbon::now()->subDays(2),
            'next_due_date' => Carbon::now()->subDays(2)->addYear(),
            'notes' => 'Aplicada exitosamente por requerimiento sanitario.',
        ]);

        // 6. Creación de Citas Médicas (Appointments)
        Appointment::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $ziggy->id,
            'date' => Carbon::now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'status' => 'scheduled',
            'reason' => 'Control de peso y revisión física general',
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $rex->id,
            'date' => Carbon::now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'scheduled',
            'reason' => 'Asesoría de muda de piel e hidratación de terrario',
        ]);

        // 7. Creación de Solicitudes de Adopción (Adoption Requests)
        AdoptionRequest::create([
            'pet_id' => $spike->id,
            'user_id' => $client2->id,
            'full_name' => 'Juan Pérez',
            'phone' => '3119876543',
            'experience' => 'Tengo un terrario completamente equipado de 120x60x60 con luces UVB y control de temperatura de mi anterior iguana.',
            'status' => 'pending',
            'admin_notes' => 'Confirmar si el terrario tiene la ventilación y rango térmico adecuado para un dragón barbudo.',
        ]);

        // 8. Creación de Registros de Kardex Clínico
        KardexEntry::create([
            'pet_id' => $noodle->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::now()->subDays(2)->toDateString(),
            'animal_type' => 'huron',
            'parameters' => [
                'frecuencia_cardiaca' => 220,
                'frecuencia_respiratoria' => 35,
                'temperatura' => 38.6,
                'glicemia' => 85,
                'hidratacion' => 100,
            ],
        ]);

        KardexEntry::create([
            'pet_id' => $ziggy->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::now()->subDays(1)->toDateString(),
            'animal_type' => 'erizo',
            'parameters' => [
                'frecuencia_cardiaca' => 240,
                'frecuencia_respiratoria' => 32,
                'temperatura' => 36.2,
                'estado_piel_puas' => 'Púas firmes y uniformes, sin descamación',
                'enrollamiento' => 'Completo/Firme',
                'peso' => 420,
            ],
        ]);

        KardexEntry::create([
            'pet_id' => $coco->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::now()->subDays(5)->toDateString(),
            'animal_type' => 'loro',
            'parameters' => [
                'frecuencia_respiratoria' => 28,
                'temperatura_cloacal' => 41.2,
                'plumaje' => 'Picaje leve en pecho, resto de plumas sanas',
                'consistencia_heces' => 'Normal',
                'comportamiento' => 'Activo/Alerta',
                'estado_buche' => 'Lleno/Normal',
            ],
        ]);

        KardexEntry::create([
            'pet_id' => $rex->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::now()->subDays(3)->toDateString(),
            'animal_type' => 'iguana',
            'parameters' => [
                'frecuencia_cardiaca' => 45,
                'temperatura_terrario' => 31.5,
                'muda_piel' => 'Completa y saludable',
                'hidratacion' => 'Normal/Turgente',
                'cola_extremidades' => 'Cola completa y sana',
                'coloracion' => 'Brillante/Verde Intenso',
            ],
        ]);

        KardexEntry::create([
            'pet_id' => $tambor->id,
            'doctor_id' => $doctor->id,
            'entry_date' => Carbon::now()->subDays(4)->toDateString(),
            'animal_type' => 'conejo',
            'parameters' => [
                'frecuencia_cardiaca' => 200,
                'frecuencia_respiratoria' => 40,
                'temperatura' => 39.1,
                'motilidad_intestinal' => 'Normal',
                'cecotrofos' => 'Normal/Heces Firmes',
                'estado_dental' => 'Perfecto estado',
            ],
        ]);

        // 9. Creación de Fórmulas Médicas (Recetas)
        MedicalFormula::create([
            'pet_id' => $noodle->id,
            'doctor_id' => $doctor->id,
            'formula_date' => Carbon::now()->subDays(2)->toDateString(),
            'instructions' => 'Administrar los medicamentos mezclados con alimento húmedo premium. Reposo relativo por 5 días.',
            'medications' => [
                [
                    'name' => 'Meloxicam Suspensión 0.5mg/ml',
                    'dose' => '0.2 ml',
                    'frequency' => 'Cada 24 horas',
                    'duration' => '5 días',
                ],
                [
                    'name' => 'Suplemento Multivitamínico Hurón-Plus',
                    'dose' => '1 gota',
                    'frequency' => 'Cada 12 horas',
                    'duration' => '30 días',
                ],
            ],
        ]);

        MedicalFormula::create([
            'pet_id' => $rex->id,
            'doctor_id' => $doctor->id,
            'formula_date' => Carbon::now()->subDays(1)->toDateString(),
            'instructions' => 'Aplicar una capa muy fina en los dedos afectados por retención de muda y masajear suavemente.',
            'medications' => [
                [
                    'name' => 'Pomada Hidratante Reptil-Shed',
                    'dose' => 'Capa delgada',
                    'frequency' => 'Cada 12 horas',
                    'duration' => '7 días',
                ],
            ],
        ]);

        // 10. Creación de Órdenes Clínicas
        $order1 = MedicalOrder::create([
            'pet_id' => $noodle->id,
            'doctor_id' => $doctor->id,
            'order_date' => Carbon::now()->subDays(10)->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Hemograma completo y panel bioquímico pre-anestésico básico (glucosa, creatinina, GPT).',
            'status' => 'completed',
        ]);

        $order2 = MedicalOrder::create([
            'pet_id' => $rex->id,
            'doctor_id' => $doctor->id,
            'order_date' => Carbon::now()->subDays(1)->toDateString(),
            'order_type' => 'Imagenología',
            'description' => 'Estudio radiográfico ventrodorsal y lateral de abdomen para descartar distocia obstructiva o impactación fecal.',
            'status' => 'pending',
        ]);

        $order3 = MedicalOrder::create([
            'pet_id' => $coco->id,
            'doctor_id' => $doctor->id,
            'order_date' => Carbon::now()->subDays(15)->toDateString(),
            'order_type' => 'Cirugía / Procedimiento',
            'description' => 'Procedimiento de limado de pico correctivo bajo sedación inhalatoria.',
            'status' => 'cancelled',
        ]);

        $order4 = MedicalOrder::create([
            'pet_id' => $ziggy->id,
            'doctor_id' => $doctor->id,
            'order_date' => Carbon::now()->subDays(3)->toDateString(),
            'order_type' => 'Laboratorio',
            'description' => 'Examen coprológico seriado por flotación directa para descarte de parásitos coccidios.',
            'status' => 'pending',
        ]);

        // 11. Creación de Exámenes de Laboratorio Subidos Enlazados
        MedicalExam::create([
            'pet_id' => $noodle->id,
            'uploaded_by' => $client1->id,
            'title' => 'Resultados Bioquímica y Hemograma Noodle',
            'original_name' => 'bioquimica_preanestesico.pdf',
            'file_path' => 'medical_exams/fake_noodle_exam.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1048576,
            'uploaded_at' => Carbon::now()->subDays(8),
            'reviewed_by_doctor_id' => $doctor->id,
            'reviewed_by_doctor_at' => Carbon::now()->subDays(8),
            'medical_order_id' => $order1->id,
        ]);

        $examZiggy = MedicalExam::create([
            'pet_id' => $ziggy->id,
            'uploaded_by' => $client1->id,
            'title' => 'Resultado Coprológico Flotación',
            'original_name' => 'coprologico_flotacion_ziggy.pdf',
            'file_path' => 'medical_exams/fake_ziggy_exam.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 512000,
            'uploaded_at' => Carbon::now()->subDays(1),
            'medical_order_id' => $order4->id,
        ]);

        // 12. Creación de Tareas de Doctores correspondientes
        DoctorTask::create([
            'doctor_id' => $doctor->id,
            'title' => 'Revisar examen externo: Resultado Coprológico Flotación',
            'description' => 'El cliente subió un examen para Ziggy y aún no ha sido revisado por el doctor.',
            'status' => 'pending',
            'due_date' => Carbon::now()->toDateString(),
            'priority' => 'high',
            'is_system' => true,
            'source_type' => 'medical_exam',
            'source_id' => $examZiggy->id,
            'task_key' => 'doctor:'.$doctor->id.':exam:'.$examZiggy->id.':review',
        ]);
    }
}
