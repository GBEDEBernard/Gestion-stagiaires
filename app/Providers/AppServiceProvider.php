<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Crypt;
use App\Models\Stage;
use App\Models\Etudiant;
use App\Models\Badge;
use App\Models\Service;
use App\Models\Jour;
use App\Models\TypeStage;
use App\Models\Signataire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Paginator::useTailwind();

        // Route Model Binding personnalisé pour le décryptage
        // Ce binding s'exécute automatiquement quand une route contient {stage}, {etudiant}, etc.
        Route::model('stage', Stage::class);
        Route::model('etudiant', Etudiant::class);
        Route::model('badge', Badge::class);
        Route::model('service', Service::class);
        Route::model('jour', Jour::class);
        Route::model('type_stage', TypeStage::class);
        Route::model('signataire', Signataire::class);

        // Personalized route binding - décrypter les paramètres automatiquement
        Route::bind('stage', function ($value) {
            return $this->resolveEncryptedModel($value, Stage::class);
        });

        Route::bind('etudiant', function ($value) {
            return $this->resolveEncryptedModel($value, Etudiant::class);
        });

        Route::bind('badge', function ($value) {
            return $this->resolveEncryptedModel($value, Badge::class);
        });

        Route::bind('service', function ($value) {
            return $this->resolveEncryptedModel($value, Service::class);
        });

        Route::bind('jour', function ($value) {
            return $this->resolveEncryptedModel($value, Jour::class);
        });

        Route::bind('type_stage', function ($value) {
            return $this->resolveEncryptedModel($value, TypeStage::class);
        });

        Route::bind('signataire', function ($value) {
            return $this->resolveEncryptedModel($value, Signataire::class);
        });
    }

    /**
     * Résoudre un paramètre de route potentiellement crypté
     * Try to decrypt if encrypted, otherwise treat as normal ID
     * 
     * @param string $value Valeur du paramètre
     * @param string $modelClass Classe du modèle
     * @return mixed Model or abort 404
     */
    private function resolveEncryptedModel($value, $modelClass)
    {
        $id = $value;

        // Try to decrypt if value looks encrypted (base64-looking)
        if ($this->looksEncrypted($value)) {
            try {
                $id = Crypt::decryptString($value);
            } catch (\Exception $e) {
                // If decryption fails, might be a normal ID
                $id = $value;
            }
        }

        // Find the model by ID
        return $modelClass::findOrFail($id);
    }

    /**
     * Check if a value looks like it might be encrypted (heuristic)
     * 
     * @param string $value
     * @return bool
     */
    private function looksEncrypted($value)
    {
        // Encrypted strings are typically long, contain '=' at the end (base64 padding)
        // and are not purely numeric
        return strlen($value) > 20 && !is_numeric($value);
    }
}
