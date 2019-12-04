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
     * @Route("/crear-usuario", methods={"GET"}, name="app_admin_create_user")
     * @param Request $request
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        if (!$this->securityCheck()) {
            return $this->redirectToRoute('app_index');
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
