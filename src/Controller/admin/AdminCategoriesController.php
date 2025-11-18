<?php
namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminCategoriesController
 *
 * @author Enzo Baum
 */
class AdminCategoriesController extends AbstractController
{
    
    /**
     * Chemin vers la page de catégorie
     */
    private const CHEMIN_CATEGORIE = "pages/admin/admin.categories.html.twig";  
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    public function __construct(
        CategorieRepository $categorieRepository,
    ) {
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * @Route("/categories", name="categories")
     * @return Response
     */
    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(Request $request): Response
    {
        $categories = $this->categorieRepository->findAll();

        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $existe = $this->categorieRepository->findOneBy(['name' => $categorie->getName()]);

            if ($existe) {
                $this->addFlash('error_ajout', "Une catégorie portant ce nom existe déjà ");
                return $this->redirectToRoute('admin.categories');
            }

            $this->categorieRepository->add($categorie);

            $this->addFlash('success_ajout', 'La catégorie a bien été ajoutée');

            return $this->redirectToRoute('admin.categories');
        }

        return $this->render(self::CHEMIN_CATEGORIE, [
            'categories' => $categories,
            'formcategorie' => $form->createView(),
        ]);
    }

    #[Route('admin/categories/delete/{id}', name: 'admin.categories.delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {

            if (count($categorie->getFormations()) > 0) {
                $this->addFlash('error_delete', 'Impossible de supprimer cette catégorie car elle contient encore des formations.');
            } else {
                $entityManager->remove($categorie);
                $entityManager->flush();

                $this->addFlash('success_delete', 'La catégorie a bien été supprimée.');
            }
        }

        return $this->redirectToRoute('admin.categories');
    }  
}