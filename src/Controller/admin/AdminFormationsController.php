<?php
namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author Enzo Baum
 */
class AdminFormationsController extends AbstractController
{
    
    /**
     * Chemin vers la page de gestion des formations
     */
    private const CHEMIN_FORMATIONS = "pages/admin/admin.formations.html.twig";
    
    /**
     * Permet d'extraire l'Id d'une vidéo YouTube
     * @param string $videoUrl
     * @return string
     */
    private function extractVideoId(string $videoUrl): string
    {
        if (str_contains($videoUrl, 'youtube.com/watch?v=')) {
            $videoId = explode('v=', $videoUrl)[1];
            return explode('&', $videoId)[0];
        }

        if (str_contains($videoUrl, 'youtu.be/')) {
            return explode('youtu.be/', $videoUrl)[1];
        }

        return $videoUrl;
    }

    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    #[Route('/admin/formations', name: 'admin.formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'admin.formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'admin.formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    #[Route('/admin/formations/formation/{id}', name: 'admin.formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render(self::CHEMIN_FORMATIONS, [
            'formation' => $formation
        ]);
    }
    
    #[Route('admin/formations/edit/{id}', name: 'admin.formations.edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = $this->formationRepository->find($id);

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fullVideoUrl = $form->get('videoId')->getData();

            $formation->setVideoId($this->extractVideoId($fullVideoUrl));

            $entityManager->flush();

            $this->addFlash('success_edit', 'La formation a bien été mise à jour');

            return $this->redirectToRoute('admin.formations');
        }

        return $this->render('pages/admin/admin.editformation.html.twig', [
            'formformation' => $form->createView(),
            'formation' => $formation,
        ]);
    }

    #[Route('admin/formations/delete/{id}', name: 'admin.formations.delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();

            $this->addFlash('success_delete', 'La formation a bien été supprimée');
        }

        return $this->redirectToRoute('admin.formations');
    }
    
    #[Route('admin/formation/ajout', name: 'admin.formations.ajout')]
    public function ajout(Request $request, FormationRepository $repository): Response
    {
        $formation = new Formation();

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $formation->setPublishedAt(new DateTime());
            
            $repository->add($formation);

            $this->addFlash('success_ajout', 'La formation a bien été ajoutée');

            return $this->redirectToRoute('admin.formations');
        }

        return $this->render('pages/admin/admin.ajoutformation.html.twig', [
            'formformation' => $form->createView(),
            'formation' => $formation,
        ]);
    }
    
}