<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\RedisCache;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    private const CACHE_LIFETIME = 60*60*24;

    /**
     * @var RedisCache
     */
    private $cache;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    public function __construct(ContainerInterface $container)
    {
        $redis = $container->get('snc_redis.top_10_cache');
        $this->cache = new RedisCache($redis, '', self::CACHE_LIFETIME);
        $this->logger = $container->get('monolog.logger.php');
    }
    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Movie::class);

        $redisKey = (new \DateTime())->format('Y-m-d');

        try {
            $top = $this->cache->get($redisKey);
        } catch (\Exception | \Psr\SimpleCache\InvalidArgumentException $exception) {
            $top = NULL;
            $this->logger->error($exception);
        }

        if ($top === NULL) {
            $top = $repo->getAllForCurrentDay();
            $repo->resetQueryBuilder();

            try {
                $this->cache->set($redisKey, $top, self::CACHE_LIFETIME);
            } catch (\Exception | \Psr\SimpleCache\InvalidArgumentException $exception) {
                $this->logger->error($exception);
            }
        }

        return $this->render('@App/default/index.html.twig', [
            'date'     => new \DateTime(),
            'top'      => $top,
            'dateList' => $repo->getInTopDates(),
        ]);
    }

    /**
     * @Route("/datetop", name="datetop")
     * @param Request $request
     *
     * @return Response
     */
    public function actionGetByDate(Request $request): Response
    {
        $em   = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Movie::class);

        $date = $request->query->get('date', new \DateTime());
        if (!($date instanceof \DateTime)) {
            $date = new \DateTime($date);
        }

        try {
            $top = $this->cache->get($date->format('Y-m-d'));
        } catch (\Exception | \Psr\SimpleCache\InvalidArgumentException $exception) {
            $top = NULL;
            $this->logger->error($exception);
        }

        if ($top === NULL) {
            $top = $repo->getAllByDate($date);
            $repo->resetQueryBuilder();

            try {
                $this->cache->set($date->format('Y-m-d'), $top, self::CACHE_LIFETIME);
            } catch (\Exception | \Psr\SimpleCache\InvalidArgumentException $exception) {
                $this->logger->error($exception);
            }

        }

        return $this->render('@App/default/index.html.twig', [
            'date'     => $date,
            'top'      => $top,
            'dateList' => $repo->getInTopDates(),
        ]);
    }
}
