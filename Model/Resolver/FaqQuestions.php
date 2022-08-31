<?php
/**
 * Copyright © Marcin Materzok - MTRZK Sp. z o .o. (MIT License)
 * See LICENSE_MTRZK for license details.
 */

declare(strict_types=1);

namespace Mtrzk\FaqPageGraphQl\Model\Resolver;

use Magento\Framework\Api\SortOrder;
use Mtrzk\FaqPage\Api\Data\QuestionInterface;
use Mtrzk\FaqPage\Model\ResourceModel\Question\Collection;
use Mtrzk\FaqPage\Model\ResourceModel\Question\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Query\Uid;
use Magento\Store\Model\Store;

class FaqQuestions implements ResolverInterface
{
    private const ALL_STORE_VIEWS = 0;
    private CollectionFactory $collectionFactory;
    private Uid $uidEncoder;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Uid               $uidEncoder
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Uid               $uidEncoder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->uidEncoder        = $uidEncoder;
    }

    /**
     * @param Field            $field
     * @param ContextInterface $context
     * @param ResolveInfo      $info
     * @param array|null       $value
     * @param array|null       $args
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field       $field,
                    $context,
        ResolveInfo $info,
        array       $value = null,
        array       $args = null
    ) {
        /** @var Store $store */
        $store   = $context->getExtensionAttributes()->getStore();
        $storeId = (int) $store->getStoreId();
        $offers  = $this->getList($storeId);

        if (empty($offers)) {
            return [];
        }

        return array_map(
            function (QuestionInterface $question): array {
                return [
                    'id'                          => $this->uidEncoder->encode((string) $question->getId()),
                    QuestionInterface::QUESTION   => $question->getQuestion(),
                    QuestionInterface::ANSWER     => $question->getAnswer(),
                    QuestionInterface::ACTIVE     => $question->isActive(),
                    QuestionInterface::POSITION   => $question->getPosition(),
                    QuestionInterface::STORE_IDS  => $question->getStoreIds(),
                    QuestionInterface::CREATED_AT => $question->getCreatedAt(),
                    QuestionInterface::UPDATED_AT => $question->getUpdatedAt(),
                ];
            },
            $offers
        );
    }

    /**
     * @param int $storeId
     *
     * @return QuestionInterface[]
     */
    public function getList(int $storeId): array
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(QuestionInterface::ACTIVE, '1');
        $collection->addFieldToFilter(
            QuestionInterface::STORE_IDS,
            [
                ['finset' => self::ALL_STORE_VIEWS],
                ['finset' => $storeId],
            ]
        );

        $collection->setOrder(QuestionInterface::POSITION, SortOrder::SORT_ASC);

        return $collection->getItems(); // @phpstan-ignore-line
    }
}
