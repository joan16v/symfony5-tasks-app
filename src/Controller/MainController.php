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

/**
 * @Route("/")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="app_index")
     */
    public function index(Request $request): Response
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }
dump('gfdgfd');
        dump($sessionUser);exit;

        return new Response('ggg');
    }

    /**
     * @Route("/login", methods={"GET", "POST"}, name="app_login")
     */
    public function login(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $login = trim($request->get('login'));
            $password = trim($request->get('password'));
            $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');

            $user = $userManager->findOneBy(
                ['login' => $login, 'password' => md5($password)]
            );

            if ($user instanceof User) {
                $session = $this->get('session');
                $sessionUser = $session->set('user', $user);

                return $this->redirectToRoute('app_index');
            }
        }

        return $this->render(
            'login.html.twig',
            [
                'request' => $request,
            ]
        );
    }
}
