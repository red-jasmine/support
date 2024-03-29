<?php

namespace RedJasmine\Support\Foundation\Service\Actions;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RedJasmine\Support\Data\Data;
use RedJasmine\Support\Foundation\Service\ResourceService;

/**
 * @property ResourceService $service
 */
class UpdateAction extends ResourceAction
{

    protected int|string|null $key = null;

    public function execute(int|string $key, Data|array $data) : Model
    {
        $this->key = $key;

        $this->data = $data;

        return $this->update();
    }

    /**
     * @return Model
     * @throws Exception
     */
    public function handle() : Model
    {
        $this->model->save();
        return $this->model;
    }


}
