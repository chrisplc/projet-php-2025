# Guide d'utilisation de FakerPHP

## Installation

FakerPHP est déjà installé dans votre projet. Il se trouve dans `require-dev` de `composer.json`.

## Utilisation de base

### 1. Créer une instance de Faker

```php
use Faker\Factory;

// Pour le français
$faker = Factory::create('fr_FR');

// Pour l'anglais (par défaut)
$faker = Factory::create();
```

### 2. Générer des données

#### Données personnelles

```php
$faker->name();              // "Jean Dupont"
$faker->firstName();         // "Marie"
$faker->lastName();          // "Martin"
$faker->email();             // "marie.martin@example.com"
$faker->phoneNumber();       // "+33 1 23 45 67 89"
$faker->address();           // "123 rue de la Paix, 75001 Paris"
$faker->city();              // "Lyon"
$faker->postcode();          // "69001"
$faker->country();           // "France"
```

#### Dates et heures

```php
$faker->date();              // "2024-12-04"
$faker->dateTime();          // DateTime object
$faker->dateTimeBetween('-1 year', 'now'); // Date aléatoire entre il y a 1 an et maintenant
$faker->time();              // "14:30:00"
```

#### Texte

```php
$faker->text();              // Texte aléatoire
$faker->sentence();          // "Une phrase aléatoire."
$faker->paragraph();         // Un paragraphe
$faker->words(5);            // Array de 5 mots
```

#### Nombres

```php
$faker->randomDigit();       // 0-9
$faker->randomNumber(3);     // Nombre aléatoire de 3 chiffres
$faker->numberBetween(10, 100); // Nombre entre 10 et 100
$faker->randomFloat(2, 0, 100); // Nombre décimal avec 2 décimales entre 0 et 100
```

#### Images et URLs

```php
$faker->imageUrl(800, 600);  // URL d'image aléatoire
$faker->url();               // URL aléatoire
$faker->slug();              // "un-slug-aléatoire"
```

### 3. Modificateurs spéciaux

#### unique() - Valeurs uniques

```php
$faker->unique()->email();   // Génère un email unique (ne se répète pas)
```

#### optional() - Valeurs optionnelles

```php
$faker->optional()->email(); // Génère un email ou null (50% de chance)
$faker->optional(0.8)->email(); // 80% de chance d'avoir un email
```

#### valid() - Valeurs valides selon un critère

```php
$faker->valid(function($digit) {
    return $digit % 2 === 0;
})->randomDigit(); // Génère uniquement des chiffres pairs
```

## Exemples pratiques pour votre projet

### Générer des utilisateurs

```php
use App\Entity\Utilisateur;
use Faker\Factory;

$faker = Factory::create('fr_FR');

$utilisateur = new Utilisateur();
$utilisateur->setEmail($faker->unique()->email());
$utilisateur->setNom($faker->lastName());
$utilisateur->setPrenom($faker->firstName());
// ... hasher le mot de passe
```

### Générer des spectacles

```php
use App\Entity\Spectacle;
use Faker\Factory;

$faker = Factory::create('fr_FR');

$spectacle = new Spectacle();
$spectacle->setTitre($faker->sentence(3)); // "Un spectacle magnifique."
$spectacle->setPrix($faker->randomFloat(2, 20, 50)); // Prix entre 20€ et 50€
$spectacle->setLieu($faker->city() . ' - ' . $faker->company());
$spectacle->setImage($faker->imageUrl(800, 600, 'theater'));
$spectacle->setPlacesDisponibles($faker->numberBetween(5, 50));
```

## Utilisation dans une commande Symfony

Voir `src/Command/GenerateFixturesCommand.php` pour un exemple complet.

## Commandes disponibles

```bash
# Générer 10 utilisateurs (par défaut)
php bin/console app:generate-fixtures

# Générer 20 utilisateurs
php bin/console app:generate-fixtures --users=20

# Générer 6 spectacles (par défaut)
php bin/console app:generate-fixtures --spectacles=6

# Générer les deux
php bin/console app:generate-fixtures --users=15 --spectacles=10
```

## Documentation complète

- [Documentation officielle FakerPHP](https://fakerphp.github.io/)
- [Liste des formatters disponibles](https://fakerphp.github.io/formatters/)
- [Locales disponibles](https://fakerphp.github.io/locales/)

