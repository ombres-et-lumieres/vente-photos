# vente-photos
Ce plugin wordpress est créé pour pouvoir vendre des tirages photos sans avoir à passer par woocommerce que je trouve mal adapté pour ce genre d' exercice. Je me fais lourdement aider par codewp.ai

Il reposera sur les éléments suivants:

La page de menu principale affichera la galerie des photos à vendre, galerie créée à partir de la bibliothèque de médias.

Une page de sous-menu "informations sur les photos" créera des settings sur lesquels les ventes se feront: les différentes dimensions de tirages disponibles ainsi que les différents types de titages disponibles. Ces settings permettront de créer des champs personnalisés pour les photos à vendre afin d' en définir leurs prix, avec une valeur par défaut et au cas par cas.

Une troisième page de menu permettra de créer deux CPT: un pour lister les photos à vendre et un pour créer un système de messagerie de types "contact" et "paiement", avec enregistrement dans la base de données. La page permettra à l' utilisateur de nommer et organiser ces deux CPT à sa convenance, dans les limites de ce plugin.

Les photos sélectionnées seront automatiquement incluses dans le CPT dédié à la vente. Ce CPT comprendra, outre l' image mise en avant, l' éditeur de texte, une série de custom fields reprenant des informations des champs XMP, IPTC et EXIF. Ces customs fields seront désactivables en fonction des besoins.

Si j' arrive à me débrouiller en programmation classique, j' ai plus de mal avec la POO, raison pour laquelle j' ai recours à codewp. Et à quelques humains si ils veulent m' aider.

Je me sens à l' aise avec le PHP en version wordpress, mais j' ai nettement plus de mal avec le JS et, surtout, l' AJAX (même sans AMsterdam).



07-12-2023
Je suis reparti avec ma grosse classe pour l' affichage du menu, surtout due à la méthode pour afficher les différents champs de types et tailles de tirage.

A cette date, le plugin recherche et affiche des informations liées aux photos sur leurs pages. Il ajoute un champ "à vendre?" pour la recherche sur la page de settings "photos à vendre"
Il permet la recherche et le stockage en BD de la liste de photos à vendre.
