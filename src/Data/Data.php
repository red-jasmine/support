<?php

namespace RedJasmine\Support\Data;


use Illuminate\Contracts\Support\Arrayable;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\DataPipeline;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;


#[MapInputName(SnakeCaseMapper::class)]
#[MapOutputName(SnakeCaseMapper::class)]
class Data extends \Spatie\LaravelData\Data
{


    protected string $_primaryKey = 'id';

    public function getKey()
    {
        return $this->{$this->_primaryKey} ?? null;
    }

    public function setKey($key) : void
    {
        $this->{$this->_primaryKey} = $key;
    }


    public static function pipeline() : DataPipeline
    {
        $pipeline = parent::pipeline();
        $pipeline->firstThrough(UserInterfacePipeline::class);
        return $pipeline;
    }


}
