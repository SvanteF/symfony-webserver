<?php

namespace App\Controller;

use App\Entity\Library;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(): Response
    {
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
        ]);
    }

    #[Route('/library/create', name: 'book_create_get', methods: ["GET"])]
    public function createBookGet(): Response
    {
        return $this->render('library/create.html.twig');
    }

    #[Route('/library/create', name: 'book_create', methods: ["POST"])]
    public function createBookPost(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {

        $title = (string) $request->request->get('title');
        $isbn = (string) $request->request->get('isbn');
        $author = (string) $request->request->get('author');
        $image = (string) $request->request->get('image');


        $entityManager = $doctrine->getManager();

        $book = new Library();
        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setAuthor($author);
        $book->setImage($image);

        // Tell Doctrine you want to (eventually) save the Product
        $entityManager->persist($book);

        // Execute the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('library_view_all');
    }

    #[Route('/library/view/{id}', name: 'book_by_id', methods: ["GET"])]
    public function showBookById(
        LibraryRepository $libraryRepository,
        int $id
    ): Response {
        $book = $libraryRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $data = [
            'book' => $book
        ];

        return $this->render('library/view_one.html.twig', $data);
    }

    #[Route('/library/view', name: 'library_view_all', methods: ["GET"])]
    public function viewAllBooks(
        LibraryRepository $libraryRepository
    ): Response {
        $library = $libraryRepository->findAll();

        $data = [
            'library' => $library
        ];

        return $this->render('library/view_all.html.twig', $data);
    }

    #[Route('/library/delete/', name: 'library_delete_by_id', methods: ["POST"])]
    public function deleteLibraryById(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {
        $id = $request->request->get('id');

        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Library::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('library_view_all');
    }


    #[Route('/library/update/{id}', name: 'book_update_get', methods: ["GET"])]
    public function updateBookGet(
        LibraryRepository $libraryRepository,
        int $id
    ): Response {
        $book = $libraryRepository->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $data = [
            'book' => $book
        ];

        return $this->render('library/update.html.twig', $data);
    }

    #[Route('/library/update', name: 'book_update', methods: ["POST"])]
    public function updateBookPost(
        ManagerRegistry $doctrine,
        Request $request
    ): Response {

        $id = $request->request->get('id');
        $title = (string) $request->request->get('title');
        $isbn = (string) $request->request->get('isbn');
        $author = (string) $request->request->get('author');
        $image = (string) $request->request->get('image');

        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Library::class)->find($id);

        if (!$book) {
            throw $this->createNotFoundException(
                'No book found for id '.$id
            );
        }

        $book->setTitle($title);
        $book->setIsbn($isbn);
        $book->setAuthor($author);
        $book->setImage($image);

        // Tell Doctrine you want to (eventually) save the Product
        $entityManager->persist($book);

        // Execute the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirectToRoute('book_by_id', [
            'id' => $book->getId()
        ]);
    }

    #[Route('/library/reset/', name: 'library_reset_get', methods: ["GET"])]
    public function resetDatabaseGet(): Response
    {

        return $this->render('library/reset.html.twig');
    }

    #[Route('/library/reset/', name: 'library_reset', methods: ["POST"])]
    public function resetDatabase(
        LibraryRepository $libraryRepository
    ): Response {
        $libraryRepository->resetLibrary();

        return $this->redirectToRoute('library_view_all');
    }
}
