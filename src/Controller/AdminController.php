<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Profiler\Profiler;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use App\Entity\User;
use App\Entity\Tasks;
use App\Service\Utilities;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="app_admin")
     * @param Request $request
     * @param Utilities $utilities
     * @return Response
     */
    public function index(Request $request, Utilities $utilities): Response
    {
        if (!$utilities->securityCheck($this->get('session'))) {
            return $this->redirectToRoute('app_index');
        }

        $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');

        return $this->render(
            'admin.html.twig',
            [
                'request' => $request,
                'user' => $this->get('session')->get('user'),
                'users' => $userManager->findAll(),
            ]
        );
    }

    /**
     * @Route("/crear-usuario", methods={"GET", "POST"}, name="app_admin_create_user")
     * @param Request $request
     * @param Utilities $utilities
     * @return Response
     */
    public function createUser(Request $request, Utilities $utilities): Response
    {
        if (!$utilities->securityCheck($this->get('session'))) {
            return $this->redirectToRoute('app_index');
        }

        if ($request->isMethod('POST')) {
            $login = trim($request->get('login'));
            $password = trim($request->get('password'));
            $name = trim($request->get('name'));
            $admin = false;

            if (empty($login)) {
                $this->addFlash('notice', 'El login no puede estar vacio.');

                return $this->redirectToRoute('app_admin_create_user');
            }

            if (empty($password)) {
                $password = User::DEFAULT_PASSWORD;
            }

            if ($request->get('admin') == 'on') {
                $admin = true;
            }

            $manager = $this->getDoctrine()->getManager();
            $user = new User();
            $user->setLogin($login);
            $user->setPassword($password);
            $user->setName($name);
            $user->setAdmin($admin);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('notice', 'Usuario creado');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render(
            'createUser.html.twig',
            [
                'request' => $request,
                'user' => $this->get('session')->get('user'),
            ]
        );
    }

    /**
     * @Route("/editar-usuario/{id}", methods={"GET", "POST"}, name="app_admin_edit_user")
     * @param Request $request
     * @param Utilities $utilities
     * @param integer $id
     * @return Response
     */
    public function editUser(Request $request, Utilities $utilities, $id): Response
    {
        if (!$utilities->securityCheck($this->get('session'))) {
            return $this->redirectToRoute('app_index');
        }

        $manager = $this->getDoctrine()->getManager();
        $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');
        $userToEdit = $userManager->findOneById($id);

        if ($request->isMethod('POST')) {
            $password = trim($request->get('password'));
            $name = trim($request->get('name'));
            $admin = false;

            if ($request->get('admin') == 'on') {
                $admin = true;
            }

            if (!empty($password)) {
                $userToEdit->setPassword($password);
            }

            $userToEdit->setName($name);
            $userToEdit->setAdmin($admin);

            $manager->persist($userToEdit);
            $manager->flush();

            $this->addFlash('notice', 'Usuario modificado.');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render(
            'createUser.html.twig',
            [
                'request' => $request,
                'user' => $this->get('session')->get('user'),
                'userToEdit' => $userToEdit,
            ]
        );
    }

    /**
     * @Route("/ver-tareas-usuario/{id}", methods={"GET", "POST"}, name="app_admin_view_user_tasks")
     * @param Request $request
     * @param Utilities $utilities
     * @return Response
     */
    public function viewUserTasksUser(Request $request, Utilities $utilities, $id): Response
    {
        if (!$utilities->securityCheck($this->get('session'))) {
            return $this->redirectToRoute('app_index');
        }


    }
}
