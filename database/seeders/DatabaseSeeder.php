<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. Role 1: 627 (Admin - Full System Access)
        // ==========================================
        User::create([
            'name' => '627 Central Admin',
            'username' => 'admin',
            'email' => 'codetech.ph@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'position' => 'Chief Operations Director',
            'department' => '627 Central Command',
            'contact_number' => '+63 917 123 4567',
            'status' => 'active',
        ]);

        // ==========================================
        // 2. Role 2: HQ (Directives to Clusters & Field)
        // ==========================================
        User::create([
            'name' => 'HQ Lead - Sofia Ramos',
            'username' => 'hq_lead',
            'email' => 'hq@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'hq',
            'position' => 'Operations Commander',
            'department' => 'HQ Command Desk',
            'contact_number' => '+63 918 234 5678',
            'status' => 'active',
        ]);

        // ==========================================
        // 3. Role 3: Cluster Heads
        // ==========================================
        User::create([
            'name' => 'Engr. Marco Torres',
            'username' => 'cluster_head1',
            'email' => 'ch1@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'cluster_head',
            'cluster' => 'Cluster 1 - Mariveles North',
            'position' => 'Cluster Head North',
            'department' => 'Field Cluster Operations',
            'contact_number' => '+63 919 345 6789',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Rowena Dela Cruz',
            'username' => 'cluster_head2',
            'email' => 'ch2@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'cluster_head',
            'cluster' => 'Cluster 2 - Mariveles South',
            'position' => 'Cluster Head South',
            'department' => 'Field Cluster Operations',
            'contact_number' => '+63 920 456 7890',
            'status' => 'active',
        ]);

        // ==========================================
        // 4. Role 4: Top Coordinators
        // ==========================================
        User::create([
            'name' => 'Christian Ramos',
            'username' => 'topcoor_c1',
            'email' => 'topcoor1@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'top_coor',
            'cluster' => 'Cluster 1 - Mariveles North',
            'position' => 'Top Field Coordinator',
            'department' => 'Coordination Desk',
            'contact_number' => '+63 921 567 8901',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Angelito Buan',
            'username' => 'topcoor_c2',
            'email' => 'topcoor2@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'top_coor',
            'cluster' => 'Cluster 2 - Mariveles South',
            'position' => 'Top Field Coordinator',
            'department' => 'Coordination Desk',
            'contact_number' => '+63 922 678 9012',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Gerald Mendoza',
            'username' => 'topcoor_c3',
            'email' => 'topcoor3@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'top_coor',
            'cluster' => 'Cluster 3 - Bataan Industrial',
            'position' => 'Top Field Coordinator',
            'department' => 'Coordination Desk',
            'contact_number' => '+63 923 789 0123',
            'status' => 'active',
        ]);

        // ==========================================
        // 5. Role 5: Registry Officers
        // ==========================================
        User::create([
            'name' => 'Juan Dela Cruz',
            'username' => 'officer_juan',
            'email' => 'juan@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'registry_officer',
            'cluster' => 'Cluster 1 - Mariveles North',
            'position' => 'Senior Registry Officer',
            'department' => 'Field Registry',
            'contact_number' => '+63 924 890 1234',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Maria Santos',
            'username' => 'officer_maria',
            'email' => 'maria@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'registry_officer',
            'cluster' => 'Cluster 1 - Mariveles North',
            'position' => 'Registry Officer',
            'department' => 'Field Registry',
            'contact_number' => '+63 925 901 2345',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Pedro Reyes',
            'username' => 'officer_pedro',
            'email' => 'pedro@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'registry_officer',
            'cluster' => 'Cluster 2 - Mariveles South',
            'position' => 'Registry Officer',
            'department' => 'Field Registry',
            'contact_number' => '+63 926 012 3456',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Elena Gomez',
            'username' => 'officer_elena',
            'email' => 'elena@worthyacosta.com',
            'password' => Hash::make('password123'),
            'role' => 'registry_officer',
            'cluster' => 'Cluster 2 - Mariveles South',
            'position' => 'Field Registry Specialist',
            'department' => 'Field Registry',
            'contact_number' => '+63 927 123 4567',
            'status' => 'active',
        ]);
    }
}
