<?php

namespace App\Controller\Api;

use App\Dto\PaginationDto;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

final class RecipeController extends AbstractController
{
    #[Route('/api/recipes', methods: ['GET'])]
    public function index(
        RecipeRepository $recipeRepository,
        #[MapQueryString()]
        PaginationDto $paginationDto,
    ) {
        // $recipes = $recipeRepository->findAll();
        $recipes = $recipeRepository->paginateRecipes($paginationDto->page);

        return $this->json($recipes, 200, [], [
            'groups' => ['recipes.index'],
        ]);
    }

    #[Route('/api/recipes/{id}', requirements: ['id' => Requirement::DIGITS])]
    public function show(Recipe $recipe)
    {
        return $this->json($recipe, 200, [], [
            'groups' => ['recipes.index', 'recipes.show'],
        ]);
    }

    // #[Route('/api/recipes', methods: ['POST'])]
    // public function new(Request $request, SerializerInterface $serializer)
    // {
    //     $recipe = new Recipe();
    //     $recipe->setCreatedAt(new \DateTimeImmutable());
    //     $recipe->setUpdatedAt(new \DateTimeImmutable());
    //     dd($serializer->deserialize($request->getContent(), Recipe::class, 'json', [
    //         AbstractNormalizer::OBJECT_TO_POPULATE => $recipe,
    //         'groups' => ['recipes.new'],
    //     ]));
    // }

    #[Route('/api/recipes', methods: ['POST'])]
    public function new(
        Request $request,
        // Il est dangereux de mapper des données qui viennent de l'extérieur sur un objet
        // Il est conseiller de mapper sur un objet qui représente la requête (NewRecipeDto et/ou UpdateRecipeDto)
        // avec les règles de validation propres puis de faire l'hydratation dans le controlleur (seters)
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['recipes.new'],
            ]
        )]
        Recipe $recipe,
        EntityManagerInterface $em)
    {
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        // TODO : Ajouter la gestion des images
        dd($recipe);

        $em->persist($recipe);
        $em->flush();

        return $this->json($recipes, 200, [], [
            'groups' => ['recipes.index', 'recipes.show'],
        ]);
    }
}
