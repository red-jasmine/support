<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: shook Liu  |  Email:24147287@qq.com  | Time: 2018/8/29/029 13:52
// +----------------------------------------------------------------------
// | TITLE: todo?
// +----------------------------------------------------------------------

namespace RedJasmine\Support\Exceptions;


use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;


/**
 * 需要统一的异常处理
 * - 上报格式
 * - 响应数据
 * 业务状态码可有地方列出
 */
class AbstractException extends Exception implements HttpExceptionInterface
{

    // 通用错误码
    protected array $errors;
    protected int   $statusCode;
    protected array $headers;
    protected mixed $data;

    protected static array $codes = [];

    public static function getCodes() : array
    {
        return static::$codes;
    }

    public static function extendCode(int $code, string $message) : void
    {
        static::$codes[$code] = $message;
    }


    public function __construct(string $message = 'error', int $code = 999999, array $errors = [], int $statusCode = 400, array $headers = [], mixed $data = null, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors     = $errors;
        $this->headers    = $headers;
        $this->statusCode = $statusCode;
        $this->data       = $data;
    }


    public static function newFromCodes(int $code, string $message = null, array $errors = [], int $statusCode = 400, array $headers = [], mixed $data = null, ?Throwable $previous = null) : static
    {
        $message = $message ?? static::$codes[$code] ?? 'exception';

        return new static($message, $code, $errors, $statusCode, $headers, $data, $previous);
    }


    /**
     * @param array $errors
     *
     * @return AbstractException
     */
    public function setErrors(array $errors) : static
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @param int $statusCode
     *
     * @return AbstractException
     */
    public function setStatusCode(int $statusCode) : static
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @param array $headers
     *
     * @return AbstractException
     */
    public function setHeaders(array $headers) : static
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param mixed $data
     *
     * @return AbstractException
     */
    public function setData(mixed $data) : static
    {
        $this->data = $data;
        return $this;
    }


    public function formatCode(int $code) : int
    {
        // 6位
        return (int)str_pad((string)$code, 6, 0);
    }


    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    public function getHeaders() : array
    {
        return $this->headers;
    }


    public function getData() : mixed
    {
        return $this->data;
    }

    public function getErrors() : ?array
    {
        return count($this->errors) > 0 ? $this->errors : null;
    }


    public function setMessage($message = '') : static
    {
        $this->message = $message;
        return $this;
    }


    public function render(Request $request) : ?JsonResponse
    {
        if (!$request->wantsJson()) {
            return null;
        }

        // 响应Json
        $arrData['code']    = $this->getCode();
        $arrData['message'] = $this->getMessage();
        $arrData['data']    = $this->getData();
        $arrData['errors']  = $this->getErrors();

        if (config('app.debug')) {
            $arrData['exception'] = get_class($this);
            $arrData['message']   = $this->getMessage();
            $arrData['file']      = $this->getFile();
            $arrData['line']      = $this->getLine();
            $arrData['trace']     = collect($this->getTrace())->map(fn($trace) => Arr::except($trace, [ 'args' ]))->all();
        }
        return response()->json($arrData, $this->getStatusCode());


    }

}
