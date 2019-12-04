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
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use App\Entity\User;
use App\Entity\Tasks;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="app_admin")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if (!$this->securityCheck()) {
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
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        if (!$this->securityCheck()) {
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
            $user->setPassword(md5($password));
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
     * @return boolean
     */
    private function securityCheck()
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');

        if (empty($sessionUser)) {
            return false;
        }

        if (!$sessionUser->getAdmin()) {
            return false;
        }

        return true;
    }
}
