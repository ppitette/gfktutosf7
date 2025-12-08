<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        // $recipes = $repository->findAll();
        // $recipes = $em->getRepository(Recipe::class)->findWithDurationLowerThan(110);
        // dd($repository->findTotalDuration());

        $recipes = $repository->findWithDurationLowerThan(110);

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements:['slug' => '[a-z0-9-]+', 'id' => '\d+'])]
    public function show(Request $request, RecipeRepository $repository, string $slug, int $id): Response
    {
        $recipe = $repository->find($id);

        if($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}

// https://127.0.0.1:8000/recette/pate-bolognaise-32
