<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Question;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class QuestionRepository extends BaseRepository
{
    public function getRandomQuestions($categoryId, $limit)
    {
        $count = $this->getEnabledQuestionsCount($categoryId);

        if ($count < $limit) {
            $limit = $count;
        }

        return $this->getRandomResults($categoryId, $limit);
    }

    /**
     * @param $categoryId
     * @return int
     */
    public function getEnabledQuestionsCount($categoryId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(*) FROM questions AS q 
                WHERE q.category_id = :category AND q.status = :enabled';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'category' => $categoryId,
            'enabled' => Question::STATUS_ENABLED
        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * @param $categoryId
     * @param $limit
     * @return ArrayCollection
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @internal param Category $category
     */
    protected function getRandomResults($categoryId, $limit)
    {
        $randomResults = new ArrayCollection();

        $sql = 'SELECT *  FROM questions AS q 
                JOIN (SELECT (RAND() * (SELECT MAX(id) FROM questions)) AS r_id) AS random 
                WHERE q.id >= random.r_id  AND q.category_id = :category AND q.status = :enabled
                ORDER BY q.id ASC LIMIT 1';
        $query = $this->createNativeQuery($sql, Question::class, 'q');
        $query->setParameter('category', (int)$categoryId);
        $query->setParameter('enabled', Question::STATUS_ENABLED);

        while ($randomResults->count() < $limit) {
            $queryResult = $query->getResult();
            $this->handleQueryResult($randomResults, $queryResult);
        }

        return $randomResults;
    }

    /**
     * @param $sql
     * @param $rootEntityClass
     * @param $alias
     * @return \Doctrine\ORM\NativeQuery
     */
    protected function createNativeQuery($sql, $rootEntityClass, $alias)
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata($rootEntityClass, $alias);

        return $this->getEntityManager()->createNativeQuery($sql, $rsm);
    }

    /**
     * @param ArrayCollection $randomResults
     * @param array $queryResult
     *
     * $queryResults: an array, because of probability that every sufficient
     * question id will be lower than "random.r_id"
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     */
    protected function handleQueryResult(ArrayCollection $randomResults, array $queryResult)
    {
        if (count($queryResult)) {
            $result = $queryResult[0];
            $this->_em->refresh($result);

            if (!$randomResults->contains($result)) {
                $randomResults->add($result);
            }
        }
    }

    public function getQuestionsQB(array $statuses = [Question::STATUS_ENABLED])
    {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('statuses', $statuses);

        return $qb;
    }

    public function findQuestionByShortId(
        $shortId,
        array $statuses = [Question::STATUS_ENABLED]
    ) {
        $qb = $this->createQueryBuilder('o')
            ->andWhere('o.shortId = :shortId')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('shortId', $shortId)
            ->setParameter('statuses', $statuses);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
