<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $ingredients = array_map(fn(string $name) => (new Ingredient())
        ->setName($name)
        ->setSlug($this->slugger->slug($name)), [
            "Farine", "Sucre", "Oeufs", "Beurre", "Lait", "Levure", "Sel", "Chocolat", "Fruits secs (amandes, noix, etc.)",
            "Vanille", "Cannelle", "Fraises", "Pommes", "Carottes", "Oignon", "Poivre", "Ail", "Échalottes", "Fines herbes",
            "Pommes de terre", "Tomates", "Poireaux"
        ]);

        $units = [
            "g", "Kg", "L", "ml", "cl", "dl", "c. à soupe", "c. à café", "pincée", "verre"
        ];

        foreach($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categories = ['Entrée', 'Plat principal', 'Dessert', 'Goûter'];

        foreach ($categories as $c) {
            $category = new Category()
                ->setName($c)
                ->setSlug($this->slugger->slug(strtolower($c)))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
            ;
            $manager->persist($category);
            $this->addReference($c, $category);
        }

        for ($i = 1; $i <= 10; ++$i) {
            $title = $faker->foodName();
            $recipe = new Recipe()
                ->setTitle($title)
                ->setSlug($this->slugger->slug(strtolower($title)))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(10, true))
                ->setDuration($faker->numberBetween(50, 80))
                ->setCategory($this->getReference($faker->randomElement($categories), Category::class))
                ->setUser($this->getReference('USER'.$faker->numberBetween(1, 10), User::class))
            ;

            foreach ($faker->randomElements($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                $recipe->addQuantity(new Quantity()
                    ->setQuantity($faker->numberBetween(1, 250))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient)
                );
            }

            $manager->persist($recipe);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
