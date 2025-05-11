<?php
namespace modules\monplugingens\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;
use modules\monplugingens\Module as gensmodule; // Importer la classe du Module
use yii\web\Response;


class AuthController extends Controller
{
    protected array|int|bool $allowAnonymous = true;

    public function actionTest(): \yii\web\Response
    {
        return $this->asJson(['message' => 'Le test fonctionne !']);
    }

    public function actionLogin(): ?Response
    {
        $request = Craft::$app->getRequest();
        if ($request->getIsPost()) {
            $valuemail=Craft::$app->request->post('mail');
            $valuepass=Craft::$app->request->post('pass');
            // ... récupérer loginIdentifier et password ...

            // ***** CHANGEMENT : Récupérer l'instance du MODULE *****
            /** @var GensModule|null $gensModule */
            $gensModule = Craft::$app->getModule('monplugingens'); // Utiliser le handle défini dans app.php

            if ($gensModule) {
                $loggedInGens = $this->loginGens(
                    $valuemail,
                    $valuepass,
                    'mail', // Adapter si besoin
                    'pass'  // Adapter si besoin
                );

                if ($loggedInGens) {
                    // Connexion réussie ...
                    // ... (logique de session, redirection) ...
                     Craft::$app->getSession()->set('loggedInGensId', $loggedInGens->id);
                     return $this->redirect('/u');
                } else {
                    // Échec ...
                    // ... (message d'erreur, redirection) ...
                     Craft::$app->getSession()->setError("Identifiant ou mot de passe incorrect.");
                     return $this->redirect('/u');
                    //  return $this->redirectTo('/connexion');
                }
            } else {
                 Craft::$app->getSession()->setError("Erreur interne du module.");
                 Craft::error('Module monplugingens non trouvé ou inactif.');
                 return $this->redirect('/u');
            }
        }
        // ... afficher le formulaire ...
        Craft::error('not post request boiiii.');
        return $this->redirect('/u');
        //  return $this->renderTemplate('chemin/vers/template/loginForm');
    }

    /**
     * Fonction pour connecter un "Gens" en vérifiant le mot de passe.
     * La logique interne de cette méthode ne change pas.
     *
     * @param string $identifier
     * @param string $password
     * @param string $identifierFieldHandle (default 'mail')
     * @param string $passwordFieldHandle (default 'pass')
     * @return Entry|null
     */
    public function loginGens(string $identifier, string $password, string $identifierFieldHandle = 'mail', string $passwordFieldHandle = 'pass'): ?Entry
    {
        // Rechercher l'entrée
        $gensEntry = Entry::find()
            ->section('gens') // Adapter 'gens' si besoin
            ->where([$identifierFieldHandle => $identifier])
            ->one();

        // Vérifier l'entrée et le mot de passe
        if ($gensEntry) {
            $hashedPassword = $gensEntry->getFieldValue($passwordFieldHandle); // Adapter 'pass' si besoin
            if ($hashedPassword && password_verify($password, $hashedPassword)) {
                return $gensEntry; // Succès
            }
        }
        return null; // Échec
    }

     public function actionLogout(): Response
     {
         Craft::$app->getSession()->remove('loggedInGensId');
         return $this->redirect('/u');
        //  reload();
        //  return $this->redirectTo('/');
     }
}