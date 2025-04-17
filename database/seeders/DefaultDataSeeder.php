<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Persistence\Models\Role;
use App\Infrastructure\Persistence\Models\EntityType;
use App\Infrastructure\Persistence\Models\Attribute;
use App\Infrastructure\Persistence\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $roles = [
            ['name' => 'Administrator', 'code' => 'admin', 'description' => 'System administrator with full access'],
            ['name' => 'Doctor', 'code' => 'doctor', 'description' => 'Medical doctor'],
            ['name' => 'Nurse', 'code' => 'nurse', 'description' => 'Medical nurse'],
            ['name' => 'Patient', 'code' => 'patient', 'description' => 'Patient user'],
            ['name' => 'Staff', 'code' => 'staff', 'description' => 'General staff member'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['code' => $role['code']], $role);
        }

        // Create entity types
        $entityTypes = [
            ['name' => 'User', 'code' => 'user', 'description' => 'System user'],
            ['name' => 'Doctor', 'code' => 'doctor', 'description' => 'Doctor profile'],
            ['name' => 'Patient', 'code' => 'patient', 'description' => 'Patient profile'],
            ['name' => 'Chatbot', 'code' => 'chatbot', 'description' => 'Chatbot instance'],
        ];

        foreach ($entityTypes as $entityType) {
            EntityType::updateOrCreate(['code' => $entityType['code']], $entityType);
        }

        // Create admin user if not exists
        if (!User::where('email', 'admin@example.com')->exists()) {
            $user = User::create([
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);

            $adminRole = Role::where('code', 'admin')->first();
            $user->roles()->attach($adminRole);
        }

        // Create default attributes
        $doctorEntityType = EntityType::where('code', 'doctor')->first();
        $patientEntityType = EntityType::where('code', 'patient')->first();

        $attributes = [
            [
                'name' => 'Specialty',
                'code' => 'specialty',
                'type' => 'string',
                'description' => 'Medical specialty',
                'is_required' => true,
                'entity_type_id' => $doctorEntityType->id,
            ],
            [
                'name' => 'License Number',
                'code' => 'license_number',
                'type' => 'string',
                'description' => 'Professional license number',
                'is_required' => true,
                'entity_type_id' => $doctorEntityType->id,
            ],
            [
                'name' => 'Blood Type',
                'code' => 'blood_type',
                'type' => 'string',
                'description' => 'Patient blood type',
                'is_required' => false,
                'entity_type_id' => $patientEntityType->id,
            ],
            [
                'name' => 'Date of Birth',
                'code' => 'date_of_birth',
                'type' => 'date',
                'description' => 'Patient date of birth',
                'is_required' => true,
                'entity_type_id' => $patientEntityType->id,
            ],
        ];

        foreach ($attributes as $attribute) {
            Attribute::updateOrCreate(['code' => $attribute['code']], $attribute);
        }
    }
}
