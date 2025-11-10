<?php

namespace App\Controller\admin;

use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur de l'accueil 
 *
 * @author Enzo Baum
 */
class AdminAccueilController extends AbstractController
{
    
    /**
     * @var FormationRepository
     */
    private $repository;
    
    /**
     *
     * @param FormationRepository $repository
     */
    public function __construct(FormationRepository $repository)
    {
        $this->repository = $repository;
    }
    
    #[Route('/admin/', name: 'admin.accueil')]
    public function index(): Response
    {
        $formations = $this->repository->findAllLasted(2);
        return $this->render("pages/admin/admin.accueil.html.twig", [
            'formations' => $formations
        ]);
    }
}
