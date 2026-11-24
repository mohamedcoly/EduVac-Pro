<?php

namespace Database\Seeders;

use App\Models\SchoolLevel;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Mathématiques', 'Français', 'Anglais', 'Arabe', 'Histoire-Géographie',
            'Sciences de la vie et de la terre', 'Physique-Chimie', 'Informatique',
            'Économie', 'Comptabilité', 'Philosophie', 'Éducation physique et sportive',
        ] as $name) {
            Subject::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }

        foreach ([
            'Préscolaire', 'Primaire', 'Collège', 'Lycée', 'Enseignement supérieur',
            'Formation professionnelle',
        ] as $name) {
            SchoolLevel::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name]);
        }
    }
}
