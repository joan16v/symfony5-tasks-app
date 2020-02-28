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

        return $this->render(
            'admin.html.twig',
            [
                'request' => $request,
                'user' => $this->get('session')->get('user'),
            ]
        );
    }

    /**
     * @Route("/usuarios", methods={"GET"}, name="app_admin_users")
     * @param Request $request
     * @param Utilities $utilities
     * @return Response
     */
    public function usersList(Request $request, Utilities $utilities): Response
    {
        if (!$utilities->securityCheck($this->get('session'))) {
            return $this->redirectToRoute('app_index');
        }

        $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');

        return $this->render(
            'users.html.twig',
            [
                'request' => $request,
                'user' => $this->get('session')->get('user'),
                'users' => $userManager->findBy([], ['login' => 'ASC']),
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
            $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');
            $login = trim($request->get('login'));
            $password = trim($request->get('password'));
            $name = trim($request->get('name'));
            $admin = false;

            if (empty($login)) {
                $this->addFlash('notice', 'El login no puede estar vacio.');

                return $this->redirectToRoute('app_admin_create_user');
            }

            if ($userManager->findOneBy(['login' => $login]) instanceof User) {
                $this->addFlash('notice', 'El usuario ya existe.');

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

            return $this->redirectToRoute('app_admin_users');
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
        $userManager = $manager->getRepository('App:User');
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

            return $this->redirectToRoute('app_admin_users');
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

        $session = $this->get('session');
        $sessionUser = $session->get('user');
        $manager = $this->getDoctrine()->getManager();
        $userManager = $manager->getRepository('App:User');
        $tasksManager = $manager->getRepository('App:Tasks');
        $userToView = $userManager->findOneById($id);
        $currentYear = date('Y');
        $year = !empty($request->get('year')) ? $request->get('year') : $currentYear;
        $week = !empty($request->get('week')) ? $request->get('week') : $utilities->getCurrentWeekValue();

        return $this->render(
            'userTasks.html.twig',
            [
                'request' => $request,
                'user' => $sessionUser,
                'userToView' => $userToView,
                'year' => $year,
                'currentYear' => $currentYear,
                'week' => $week,
                'previousYear' => $utilities->getPreviousYear($week, $year),
                'previousWeek' => $utilities->getPreviousWeek($week, $year),
                'nextYear' => $utilities->getNextYear($week, $year),
                'nextWeek' => $utilities->getNextWeek($week, $year),
                'editAllowed' => false,
                'tasks' => $tasksManager->findBy(
                    [
                        'user' => $userToView,
                        'week' => $week,
                        'year' => $year,
                    ]
                ),
            ]
        );
    }

    /**
     * @Route("/estadisticas", methods={"GET"}, name="app_admin_stats")
     * @param Request $request
     * @param Utilities $utilities
     * @return Response
     */
    public function stats(Request $request, Utilities $utilities): Response
    {
        if (!$utilities->securityCheck($this->get('session'))) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render(
            'stats.html.twig',
            [
                'request' => $request,
                'user' => $this->get('session')->get('user'),
            ]
        );
    }
}
