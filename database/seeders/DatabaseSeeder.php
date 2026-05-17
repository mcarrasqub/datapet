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
            'notes' => 'Sumamente curioso y escurridizo. Le encanta esconder calcetines. Dieta alta en proteínas animales.',
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
            'notes' => 'Nocturno. Le asustan los ruidos fuertes. Adora los tenebrios (gusanos de la harina) como premio.',
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
            'notes' => 'Muy inteligente, repite palabras sencillas. Requiere mucho enriquecimiento cognitivo con juguetes.',
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
            'notes' => 'Requiere baños de agua tibia diarios y un terrario amplio con control estricto de humedad.',
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
            'notes' => 'Le encanta posarse en las ramas bajo la lámpara de calor. Muy dócil y fácil de manipular.',
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
            'notes' => 'Come ratones descongelados una vez por semana. Excelente temperamento.',
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
