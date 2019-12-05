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
     * @param Request $request
     * @return Response
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
                'currentYear' => $currentYear,
                'year' => $year,
                'week' => $week,
                'nextYear' => $this->getNextYear($week, $year),
                'nextWeek' => $this->getNextWeek($week, $year),
                'previousYear' => $this->getPreviousYear($week, $year),
                'previousWeek' => $this->getPreviousWeek($week, $year),
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
     * @param Request $request
     * @return Response
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
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request): Response
    {
        $session = $this->get('session');
        $session->remove('user');

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/guardar-tarea", methods={"GET"}, name="app_save_task")
     * @param Request $request
     * @return Response
     */
    public function addTask(Request $request): Response
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');
        $userManager = $this->getDoctrine()->getManager()->getRepository('App:User');
        $manager = $this->getDoctrine()->getManager();
        $week = $request->get('week');
        $year = $request->get('year');

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
        $task->setWeek($week);
        $task->setYear($year);

        $manager->persist($task);
        $manager->flush();

        $this->addFlash('notice', 'Tarea insertada');

        return $this->redirectToRoute(
            'app_index',
            [
                'week' => $week,
                'year' => $year,
            ]
        );
    }

    /**
     * @Route("/obtener-tarea", methods={"GET", "POST"}, name="app_get_task")
     * @param Request $request
     * @return Response
     */
    public function getTask(Request $request): Response
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');
        $tasksManager = $this->getDoctrine()->getManager()->getRepository('App:Tasks');

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }

        $task = $tasksManager->findOneBy(
            [
                'id' => $request->get('id'),
            ]
        );

        $taskArray = [
            'id' => $task->getId(),
            'day' => $task->getDay(),
            'description' => $task->getDescription(),
            'description_gestic' => $task->getGesticDescription(),
            'gestic' => $task->getGestic(),
            'hour_type' => $task->getHourType(),
            'hours' => $task->getHours(),
            'percent' => $task->getPercent(),
            'status' => $task->getStatus(),
            'tarea' => $task->getTask(),
        ];

        return new Response(json_encode($taskArray, true));
    }

    /**
     * @Route("/editar-tarea", methods={"GET"}, name="app_edit_task")
     * @param Request $request
     * @return Response
     */
    public function editTask(Request $request): Response
    {
        $session = $this->get('session');
        $sessionUser = $session->get('user');
        $manager = $this->getDoctrine()->getManager();
        $tasksManager = $this->getDoctrine()->getManager()->getRepository('App:Tasks');

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }

        $task = $tasksManager->findOneBy(
            [
                'id' => $request->get('id'),
            ]
        );

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

        $this->addFlash('notice', 'Tarea editada');

        return $this->redirectToRoute(
            'app_index',
            [
                'week' => $request->get('week'),
                'year' => $request->get('year'),
            ]
        );
    }

    /**
     * @Route("/borrar-tarea", methods={"GET", "POST"}, name="app_delete_task")
     * @param Request $request
     * @return Response
     */
    public function deleteTask(Request $request): Response
    {
        $sessionUser = $this->get('session')->get('user');
        $manager = $this->getDoctrine()->getManager();
        $tasksManager = $this->getDoctrine()->getManager()->getRepository('App:Tasks');

        if (empty($sessionUser)) {
            return $this->redirectToRoute('app_login');
        }

        $task = $tasksManager->findOneBy(
            [
                'id' => $request->get('id'),
            ]
        );

        $manager->remove($task);
        $manager->flush();

        $this->addFlash('notice', 'Tarea borrada');

        return $this->redirectToRoute(
            'app_index',
            [
                'week' => $request->get('week'),
                'year' => $request->get('year'),
            ]
        );
    }

    /**
     * @Route("/cambiar-password", methods={"GET"}, name="app_change_password")
     * @param Request $request
     * @return Response
     */
    public function changePassword(Request $request): Response
    {
    }

    /**
     * @return string
     */
    private function getCurrentWeekValue()
    {
        $date = new \DateTime();

        return $date->format("W");
    }

    /**
     * @return string
     */
    private function getCurrentWeek()
    {
        $dtmin = new \DateTime("last sunday");
        $dtmin->modify('+1 day');
        $dtmax = clone($dtmin);
        $dtmax->modify('+6 days');

        return $dtmin->format('d/m/Y') . ' - ' . $dtmax->format('d/m/Y');
    }

    /**
     * @return integer
     */
    private function getNextWeek($week, $year)
    {
        if ($week > 52) {
            return 1;
        }

        return ($week + 1);
    }

    /**
     * @return integer
     */
    private function getNextYear($week, $year)
    {
        if ($week > 52) {
            return ($year + 1);
        }

        return $year;
    }

    /**
     * @return integer
     */
    private function getPreviousWeek($week, $year)
    {
        if ($week < 2) {
            $week = 53;
        }

        return ($week - 1);
    }

    /**
     * @return integer
     */
    private function getPreviousYear($week, $year)
    {
        if ($week < 2) {
            $year = $year - 1;
        }

        return $year;
    }
}
