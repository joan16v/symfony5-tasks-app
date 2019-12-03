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
     */
    public function index(Request $request): Response
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');
        $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }

        if (!$sessionUser->getAdmin()) {
            return $this->redirectToRoute('app_index');
        }

        return $this->render(
            'admin.html.twig',
            [
                'request' => $request,
                'user' => $sessionUser,
                'users' => $userManager->findAll(),
            ]
        );
    }
}
