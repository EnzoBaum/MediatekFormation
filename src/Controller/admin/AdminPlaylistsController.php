<?php
namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur des playlists
 * @author Enzo Baum
 */
class AdminPlaylistsController extends AbstractController
{
    
    /**
     * Chemin vers la page de playlist
     */
    private const CHEMIN_PLAYLIST = "pages/admin/admin.playlists.html.twig";
    
    /**
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
    /**
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Constructeur
     * @param PlaylistRepository $playlistRepository
     * @param CategorieRepository $categorieRepository
     * @param FormationRepository $formationRepository
     */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     */
    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/playlists/tri/{champ}/{ordre}", name: "admin.playlists.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'admin.playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        switch ($champ) {
            case 'name':
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case 'nbformations':
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;
            default:
                $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        }

        $categories = $this->categorieRepository->findAll();

        return $this->render(self::CHEMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }
    
    /**
     * @Route("/admin/playlists/recherche/{champ}/{table}", name: "admin.playlists.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'admin.playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::CHEMIN_PLAYLIST, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }
      
    /**
     * @Route("/admin/playlists/edit/{id}", name: "admin.playlists.edit")
     * @param type $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('admin/playlists/edit/{id}', name: 'admin.playlists.edit')]
    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $playlist = $this->playlistRepository->find($id);

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success_edit', 'La playlist a bien été mise à jour');

            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render('pages/admin/admin.editplaylist.html.twig', [
            'formplaylist' => $form->createView(),
            'formation' => $playlist,
        ]);
    }

    /**
     * @Route("/admin/playlists/delete/{id}", name: "admin.playlists.delete")
     * @param Request $request
     * @param Playlist $playlist
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('admin/playlists/delete/{id}', name: 'admin.playlists.delete', methods: ['POST'])]
    public function delete(Request $request, Playlist $playlist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $playlist->getId(), $request->request->get('_token'))) {

            if (count($playlist->getFormations()) > 0) {
                $this->addFlash('error_delete', 'Impossible de supprimer cette playlist car elle contient encore des formations.');
            } else {
                $entityManager->remove($playlist);
                $entityManager->flush();

                $this->addFlash('success_delete', 'La playlist a bien été supprimée.');
            }
        }

        return $this->redirectToRoute('admin.playlists');
    }

    /**
     * @Route("/admin/playlists/ajout", name: "admin.playlists.ajout")
     * @param Request $request
     * @param PlaylistRepository $repository
     * @return Response
     */
    #[Route('admin/playlists/ajout', name: 'admin.playlists.ajout')]
    public function ajout(Request $request, PlaylistRepository $repository): Response
    {
        $playlist = new Playlist();

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                        
            $repository->add($playlist);

            $this->addFlash('success_ajout', 'La playlist a bien été ajoutée');

            return $this->redirectToRoute('admin.playlists');
        }

        return $this->render('pages/admin/admin.ajoutplaylist.html.twig', [
            'formplaylist' => $form->createView(),
            'playlist' => $playlist,
        ]);
    }
    
}