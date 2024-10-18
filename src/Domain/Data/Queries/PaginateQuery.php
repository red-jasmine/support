<?php

namespace RedJasmine\Support\Domain\Data\Queries;

/**
 * 分页查询类，用于处理数据的分页逻辑
 * 继承自 Data 类，提供分页、字段、排序等查询参数的管理
 */
class PaginateQuery extends Query
{
    /**
     * 当前页码
     * @var int|null
     */
    public ?int $page = null;

    /**
     * 每页显示数量
     * @var int|null
     */
    public ?int $perPage = null;

    /**
     * 附加字段，用于指定查询结果中需要附加的额外字段
     * @var mixed
     */
    public mixed $append;

    /**
     * 排序字段，用于指定查询结果的排序依据
     * @var mixed
     */
    public mixed $sort;

}
