<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Category;
use AppBundle\Entity\Question;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

class QuestionRepository extends BaseRepository
{
    public function getRandomQuestions(Category $category, $limit)
    {
        $count = $this->getEnabledQuestionsCount($category);

        if ($count < $limit) {
            $limit = $count;
        }

        return $this->getRandomResults($category, $limit);
    }

    /**
     * @param Category $category
     * @return int
     */
    public function getEnabledQuestionsCount(Category $category)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(*) FROM questions AS q WHERE q.category_id = :category AND q.status = :enabled';
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'category' => $category->getId(),
            'enabled' => Question::STATUS_ENABLED
        ]);

        return (int)$stmt->fetchColumn();
    }

    /**
     * @param Category $category
     * @param $limit
     * @return ArrayCollection
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    protected function getRandomResults(Category $category, $limit)
    {
        $results = new ArrayCollection();

        $em = $this->getEntityManager();

        $sql = 'SELECT * 
                FROM questions AS q 
                JOIN (SELECT (RAND() * (SELECT MAX(id) FROM questions)) AS r_id) AS random 
                WHERE q.id >= random.r_id 
                AND q.category_id = :category
                AND q.status = :enabled
                ORDER BY q.id ASC 
                LIMIT 1';

        while ($results->count() < $limit) {
            $rsm = new ResultSetMappingBuilder($em);
            $rsm->addRootEntityFromClassMetadata(Question::class, 'q');
            $query = $em->createNativeQuery($sql, $rsm);
            $query->setParameter('category', $category->getId());
            $query->setParameter('enabled', Question::STATUS_ENABLED);

            $result = $query->getSingleResult();
            $em->refresh($result);

            if (!$results->contains($result)) {
                $results->add($result);
            }
        }

        return $results;
    }
}
