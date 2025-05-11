<?php
// Assurez-vous que la session est démarrée pour pouvoir utiliser les cookies
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'email et le mot de passe soumis
    $email = $_POST["email"] ?? null;
    $password = $_POST["nom"] ?? null; // Le champ "nom" dans votre formulaire est utilisé pour le mot de passe

    // Rechercher l'utilisateur dans l'objet "gens"
    $utilisateurTrouve = null;
    foreach ($gens as $utilisateur) {
        if ($utilisateur['email'] === $email && password_verify($password, $utilisateur['password'])) {
            $utilisateurTrouve = $utilisateur;
            break;
        }
    }

    // Traitement de la connexion
    if ($utilisateurTrouve) {
        // Définir un cookie pour identifier l'utilisateur connecté
        $expire = time() + 36000; // Expire dans 10 heure
        $path = '/'; // Disponible sur tout le site
        $domain = $_SERVER['HTTP_HOST']; // Le domaine actuel
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'; // Sécurisé uniquement sur HTTPS
        $httponly = true; // Empêche l'accès via JavaScript

        setcookie('eemailUtilisateur', $utilisateurTrouve['email'], $expire, $path, $domain, $secure, $httponly);

        // Rediriger l'utilisateur vers la page précédente ou une autre page de votre choix
        header("Location: " . $_SERVER['HTTP_REFERER']); // Redirige vers la page d'où provient le formulaire
        exit();
    } else {
        // Identifiants incorrects
        echo "<p style='color:red;'>Identifiants incorrects.</p>";
        // Vous pourriez rediriger l'utilisateur vers le formulaire de connexion avec un message d'erreur
        // header("Location: votre-page-de-connexion?erreur=1");
        // exit();
    }
} else {
    // Si la page est accédée directement sans soumission de formulaire
    echo "<p>Ce script ne doit être appelé que via une requête POST.</p>";
}
?>