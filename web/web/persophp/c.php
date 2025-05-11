<?php

// Démarrez la session si vous prévoyez d'utiliser des sessions
session_start();

// Vérifiez si le formulaire a été soumis en utilisant la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // *** GESTION DU CSRF (Simplifiée pour un exemple PHP autonome) ***
    // Dans Craft CMS, {{ csrfInput() }} génère un token de sécurité.
    // Pour un script PHP autonome, vous devriez implémenter votre propre système de CSRF.
    // Voici une vérification très basique (à améliorer pour une production réelle) :
    if (isset($_POST['CRAFT_CSRF_TOKEN']) && hash_equals($_SESSION['csrf_token'] ?? '', $_POST['CRAFT_CSRF_TOKEN'])) {
        // Le token CSRF est présent et correspond (dans notre exemple simplifié)
        // Vous devriez générer et vérifier un token plus robuste en production.
    } else {
        // Token CSRF manquant ou invalide
        die("Erreur de sécurité : Token CSRF invalide.");
    }

    // Récupérer les données du formulaire
    $title = $_POST["title"] ?? null;
    $mail = $_POST["fields"]["mail"] ?? null;
    $passwordBrut = $_POST["fields"]["pass"] ?? null;

    // *** TRAITEMENT DU MOT DE PASSE ***
    // Hasher le mot de passe avant de le stocker ou de l'afficher
    $passwordHache = password_hash($passwordBrut, PASSWORD_DEFAULT);







 // --- Supposons que vous avez obtenu ces valeurs ---
$csrfTokenName = 'CRAFT_CSRF_TOKEN'; // Remplacez par le vrai nom si différent
$csrfTokenValue = $_POST['CRAFT_CSRF_TOKEN']; // Remplacez par la vraie valeur


// --- Construire le tableau de données POST ---
// IMPORTANT : La structure doit correspondre exactement aux noms des champs du formulaire
$postData = [
    $csrfTokenName => $csrfTokenValue,
    'action' => 'guest-entries/save',
    'redirect' => 'users?success=1', // Même si cURL ne suit pas, envoyez-le
    'sectionId' => 3,
    'title' => $title,
    'fields' => [ // Utiliser un sous-tableau pour les champs 'fields[...]'
        'mail' => $mail,
        'pass' => $passwordHache, // Utilisez le mot de passe haché
    ]
    // Si vous aviez un champ fichier <input type="file" name="fields[assetHandle]"> :
    // 'fields[assetHandle]' => new CURLFile('/chemin/vers/votre/fichier.jpg', 'image/jpeg', 'nom_fichier.jpg')
];

// --- L'URL cible est l'URL racine de votre site Craft ---
// Craft route la requête en interne grâce au paramètre 'action'.
$craftSiteUrl = 'http://localhost:8080/'; // Adaptez ceci

// --- Configuration cURL ---
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $craftSiteUrl);
curl_setopt($ch, CURLOPT_POST, true);
// IMPORTANT : Pour envoyer des données structurées comme 'fields[mail]' et potentiellement
// gérer `multipart/form-data` (à cause de enctype dans le form original ou si fichiers),
// passez directement le tableau PHP. cURL choisira le bon Content-Type.
// Si vous étiez SÛR qu'aucun fichier n'est envoyé et que l'action accepte
// 'application/x-www-form-urlencoded', vous pourriez utiliser http_build_query($postData).
// Mais avec la structure 'fields' et l'enctype original, passer le tableau est plus sûr.
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Augmentez si nécessaire

// --- Gestion potentielle des Cookies/Session (peut être nécessaire pour CSRF/Auth) ---
// Si la méthode d'obtention du CSRF (comme la pré-requête GET) a nécessité
// de récupérer un cookie de session, il faut le renvoyer.
// curl_setopt($ch, CURLOPT_COOKIE, "CraftSessionId=VALEUR_DU_COOKIE_SESSION");
// Ou utiliser CURLOPT_COOKIEJAR / CURLOPT_COOKIEFILE si vous gérez les cookies plus proprement.

// --- Vérification SSL (garder activé en production) ---
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// --- Exécution et récupération de la réponse ---
echo "<h1>Envoi des données à Craft CMS...</h1>";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// --- Affichage du résultat ---
if ($error) {
    echo "<p style='color:red;'>Erreur cURL : " . htmlspecialchars($error) . "</p>";
} else {
    echo "<p>Requête terminée. Code de statut HTTP : " . $httpCode . "</p>";
    // Analyser la $response peut donner des indices sur le succès ou l'échec
    // Une réponse 200 ne garantit pas que Craft a sauvegardé l'entrée (il peut y avoir des erreurs de validation)
    // Une réponse 302 Found indique souvent un succès et une tentative de redirection vers l'URL 'redirect'.
    echo "<pre>Réponse du serveur : \n" . htmlspecialchars($response) . "</pre>";

    if ($httpCode >= 200 && $httpCode < 400) {
         echo "<p style='color:green;'>La requête semble avoir été traitée (code $httpCode). Vérifiez dans Craft si l'entrée a été créée/modifiée.</p>";
         if ($httpCode == 302) {
             // Vous pourriez vouloir extraire l'URL de redirection de l'en-tête 'Location' dans la réponse complète
             // (nécessite curl_setopt($ch, CURLOPT_HEADER, true); et de parser les en-têtes)
             echo "<p>Le serveur a tenté de rediriger (probablement un succès).</p>";
         }
    } else {
         echo "<p style='color:red;'>La requête a échoué côté serveur (code $httpCode). La réponse ci-dessus peut contenir des erreurs de validation Craft.</p>";
    }
}




    // *** REDIRECTION (Optionnelle) ***
    // Vous pouvez rediriger l'utilisateur vers une autre page après le traitement
    // $redirectUrl = "users?success=1";
    // header("Location: " . $redirectUrl);
    exit();

} else {
    // Si la page est accédée directement sans soumission de formulaire
    echo "<p>Ce script ne doit être appelé que via une requête POST.</p>";
}

// *** GÉNÉRATION D'UN TOKEN CSRF (Pour l'exemple de vérification ci-dessus) ***
// if (empty($_SESSION['csrf_token'])) {
//     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// }

?>