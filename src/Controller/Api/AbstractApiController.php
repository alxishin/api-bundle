<?php

declare(strict_types=1);

namespace ApiBundle\Controller\Api;

use ApiBundle\Dto\Request\RequestInterface;
use ApiBundle\Dto\Response\Data\List\ListResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends AbstractController
{
    protected const string ENTITY_CLASS = '';
    protected const ?string SERIALIZE_GROUP = null;

    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    protected function listHandle(RequestInterface $request): JsonResponse
    {
        [$list, $total] = $this->getList($request);
        return $this->json(new ListResponse($list, $request->limit, $request->page, $total)->toArray(), 200, [], [
            'groups' => static::SERIALIZE_GROUP
        ]);
    }

    protected function getList(RequestInterface $request): array
    {
        $limit = $request->limit;

        $offset = ($request->page - 1) * $limit;
        return $request->getItemsAndTotalCount($this->entityManager->getRepository(static::ENTITY_CLASS)->createQueryBuilder('t'), $limit, $offset);
    }
}
