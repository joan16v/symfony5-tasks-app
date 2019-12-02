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
        $currentYear = date('Y');
        $year = !empty($request->get('year')) ? $request->get('year') : $currentYear;
        $currentWeek = $this->getCurrentWeek();
        $currentWeekValue = $this->getCurrentWeekValue();
        $week = !empty($request->get('week')) ? $request->get('week') : $currentWeekValue;
        $tasksManager = $this->getDoctrine()->getManager()->getRepository('App:Tasks');

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'index.html.twig',
            [
                'request' => $request,
                'user' => $sessionUser,
                'year' => $year,
                'currentYear' => $currentYear,
                'week' => $week,
                'tasks' => $tasksManager->findBy(
                    [
                        'user' => $sessionUser,
                        'week' => $week,
                        'year' => $year,
                    ]
                ),
            ]
        );
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

            $this->addFlash('notice', 'Login error');

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'login.html.twig',
            [
                'request' => $request,
            ]
        );
    }

    /**
     * @Route("/logout", methods={"GET", "POST"}, name="app_logout")
     */
    public function logout(Request $request): Response
    {
        $session = $this->get('session');
        $session->remove('user');

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/guardar-tarea", methods={"GET"}, name="app_save_task")
     */
    public function addTask(Request $request): Response
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');
        $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');
        $manager = $this->getDoctrine()->getManager();

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userManager->findOneBy(
            ['id' => $sessionUser->getId()]
        );

        $task = new Tasks();
        $task->setUser($user);
        $task->setDay($request->get('day'));
        $task->setGestic($request->get('gestic'));
        $task->setGesticDescription($request->get('gestic_description'));
        $task->setDescription($request->get('description'));
        $task->setTask($request->get('tarea'));
        $task->setHourType($request->get('hour_type'));
        $task->setHours($request->get('hours'));
        $task->setStatus($request->get('status'));
        $task->setPercent($request->get('percent'));
        $task->setWeek($request->get('week'));
        $task->setYear($request->get('year'));

        $manager->persist($task);
        $manager->flush();

        $this->addFlash('notice', 'Tarea insertada');

        return $this->redirectToRoute('app_index');
    }

    private function getCurrentWeekValue()
    {
        $date = new \DateTime();

        return $date->format("W");
    }

    private function getCurrentWeek()
    {
        $dtmin = new \DateTime("last sunday");
        $dtmin->modify('+1 day');
        $dtmax = clone($dtmin);
        $dtmax->modify('+6 days');

        return $dtmin->format('d/m/Y') . ' - ' . $dtmax->format('d/m/Y');
    }
}
