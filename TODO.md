# TODO: Add Etudiants + Badges Seeding for Testing

## Plan Steps:

1. [x] Edit database/seeders/DatabaseSeeder.php to call EtudiantsPresenceSeeder::class
2. [x] Run \`php artisan migrate:fresh --seed\` (in progress/completed)
3. [ ] Verify: Etudiant::count() == 5, Badge::count() > 0, presence data seeded
4. [ ] Test pointage/geolocation with seeded etudiant accounts (email: lucas.martin@etudiant.tfg / password123)

Progress: Step 1 complete. Running migration seed next... 2. [ ] Run \`php artisan migrate:fresh --seed\` 3. [ ] Verify: Etudiant::count() == 5, Badge::count() > 0, presence data seeded 4. [ ] Test pointage/geolocation with seeded etudiant accounts (email: lucas.martin@etudiant.tfg / password123)

Progress: Starting step 1...
