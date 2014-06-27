<?php

namespace TobiasOlry\Talkly\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

use TobiasOlry\Talkly\Entity\Topic;
use TobiasOlry\Talkly\Form\CreateTopicType;

use TobiasOlry\Talkly\Security\Security;

class IndexController
{
    private $twig;
    private $formFactory;
    private $topicService;
    private $security;

    public function __construct(
        \Twig_Environment $twig,
        $formFactory,
        Security $security,
        $topicService
    ) {
        $this->twig         = $twig;
        $this->formFactory  = $formFactory;
        $this->security     = $security;
        $this->topicService = $topicService;
    }

    public function dashboardAction(Request $request)
    {
        $form = $this->formFactory->create(
            new CreateTopicType(),
            new Topic($this->security->getUser())
        );



        $topics          = $this->topicService->findNonArchivedMostVotesFirst();
        $lastSubmissions = $this->topicService->findLastSubmissions($limit = 5);
        $nextTopics      = $this->topicService->findNextTopics($limit = 5);

        return new Response(
            $this->twig->render(
                'index/dashboard.html.twig',
                array(
                    'topics'           => $topics,
                    'last_submissions' => $lastSubmissions,
                    'next_topics'      => $nextTopics,
                    'form'             => $form->createView(),
                )
            )
        );
    }

    public function archiveAction(Request $request)
    {
        $topics = $this->topicService->findArchivedGroupByMonth();

        return new Response(
            $this->twig->render(
                'index/archive.html.twig',
                array(
                    'topics' => $topics,
                )
            )
        );
    }

    public function calendarAction(Request $request)
    {
        $topics = $this->topicService->findNextGroupByMonth();

        return new Response(
            $this->twig->render(
                'index/calendar.html.twig',
                array(
                    'topics' => $topics,
                )
            )
        );
    }
}
