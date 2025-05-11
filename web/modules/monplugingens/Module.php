<?php
// ***** CHANGEMENT IMPORTANT : Namespace correspondant au chemin du module *****
namespace modules\monplugingens; // Doit correspondre au dossier et à la config app.php

use Craft;
// Retiré : use craft\base\Plugin; // N'est plus un Plugin
use craft\elements\Entry;
use craft\events\ModelEvent; // Utiliser ModelEvent pour accéder à $event->sender plus facilement
use yii\base\Event;         // Garder Event si vous préférez le type hint générique
use yii\base\Module as BaseModule; // Hériter du module de base Yii

/**
 * Class Module (remplace MonpluginGens)
 * Module pour gérer la logique spécifique à la section "Gens".
 */
// ***** CHANGEMENT IMPORTANT : La classe s'appelle maintenant Module et hérite de BaseModule *****
class Module extends BaseModule
{
    // ***** RETIRÉ : La méthode statique config() n'est généralement pas utilisée/nécessaire pour les modules simples *****
    // public static function config(): array ...

    /**
     * Initialisation du module.
     */
    public function init()
    {
        parent::init();

        // Définir un alias si besoin (optionnel)
        Craft::setAlias('@modules/monplugingens', $this->getBasePath());

        // // ***** AJUSTEMENT : Message de log pour refléter un module et utiliser l'ID du module *****
        // Craft::info(
        //     Craft::t(
        //         'monplugingens', // Catégorie de traduction (le handle du module est bien ici)
        //         'Module {id} chargé', // Message indiquant que c'est un module
        //         ['id' => $this->id] // Utiliser l'ID du module (le handle défini dans app.php)
        //     ),
        //     __METHOD__
        // );

        Craft::info(
            'Module ' . $this->id . ' chargé.', // Simple concaténation de chaînes
            __METHOD__
        );

        // L'écouteur d'événement reste le même logiquement
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE,
            // Utiliser ModelEvent pour un typage plus précis (contient $sender)
            function (ModelEvent $event) {
                /** @var Entry $entry */
                $entry = $event->sender; // Récupère l'entrée

                // Vérifier la section (handle 'gens')
                if ($entry->section->handle === 'gens') { // Toujours adapter 'gens' si besoin
                    // Récupérer la valeur du champ mot de passe (handle 'pass')
                    $plainPassword = $entry->getFieldValue('pass'); // Toujours adapter 'pass' si besoin

                    // Hasher si le mot de passe est fourni
                    if (!empty($plainPassword)) { // Utiliser !empty() est plus sûr
                        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
                        $entry->setFieldValue('pass', $hashedPassword);
                        Craft::info('Module Gens : Mot de passe hashé pour l\'entrée ID: ' . ($entry->id ?? 'Nouvelle'), __METHOD__);
                    }
                }
            }
        );
    }

    
}