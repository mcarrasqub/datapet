<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use App\Models\Appointment;
use App\Models\AdoptionRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
        Pet::truncate();
        User::truncate();

        // Reactivar restricciones de clave foránea
        Schema::enableForeignKeyConstraints();

        // 1. Creación de Usuarios de Prueba
        $admin = User::create([
            'id' => 1000000001,
            'name' => 'Administrador',
            'lastname' => 'Datapet',
            'email' => 'admin@datapet.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '1234567890',
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
            'phone' => '0987654321',
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
            'phone' => '3001234567',
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
            'phone' => '3119876543',
            'address' => 'Laureles, Medellín',
            'status' => true,
        ]);

        // 2. Creación de Mascotas Exóticas de Clientes
        $noodle = Pet::create([
            'user_id' => $client1->id,
            'name' => 'Noodle',
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

        // 3. Creación de Mascotas Exóticas Disponibles para Adopción (asociadas al administrador/albergue)
        $spike = Pet::create([
            'user_id' => $admin->id,
            'name' => 'Spike',
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
        // Los hurones son de las pocas mascotas exóticas estándar que sí tienen un protocolo estricto de vacunación (Rabia y Moquillo)
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
    }
}
