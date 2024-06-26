<?php

namespace RedJasmine\Support\Application\Handlers;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RedJasmine\Support\Data\Data;
use RedJasmine\Support\Domain\Models\OperatorInterface;
use RedJasmine\Support\Domain\Models\OwnerInterface;
use RedJasmine\Support\Facades\ServiceContext;
use RedJasmine\Support\Application\CommandHandler;


class CreateCommandHandler extends CommandHandler
{


    /**
     * @param Data $command
     *
     * @return mixed
     * @throws Exception
     */
    public function handle(Data $command) : mixed
    {

        // TODO 关联模型处理
        // 操作 领域事件 中间件处理
        // 持久化
        // 应用层事件
        // 返回值


        // 创建model
        $this->model = $this->getService()->newModel($command);

        // 填充数据 ( 值对象验证) 中间件处理
        $this->validate($this->model, $command);
        // 填充数据

        if ($this->model instanceof OwnerInterface) {
            $this->model->owner = $command->owner;
        }
        // 数据填充
        $this->model->fill($command->all());

        if ($this->model instanceof OperatorInterface) {
            $this->model->creator = ServiceContext::getOperator();
        }


        $execute = method_exists($this->model, 'create') ? fn() => $this->model->create() : null;
        $this->execute(
            execute: $execute,
            persistence: fn() => $this->getService()->getRepository()->store($this->model),
        );

        return $this->model;
    }


    protected function validate(Model $model, Data $command) : void
    {

    }
}
