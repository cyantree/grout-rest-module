<?php
namespace Grout\Cyantree\RestModule\Types;

use Cyantree\Grout\App\Page;
use Cyantree\Grout\App\Types\ResponseCode;
use Cyantree\Grout\Filter\ArrayFilter;

class RestPage extends Page
{
    private $errors = array();
    private $responseCode = ResponseCode::CODE_200;

    public $hasError = false;

    /** @var ArrayFilter */
    public $request;

    public function parseTask()
    {
        $method = 'do' . substr($this->request()->method, 0, 1) . strtolower(substr($this->request()->method, 1));

        if ($method == 'doPost' || $method == 'doPut') {
            $data = json_decode(file_get_contents('php://input'), true);

        } else {
            $data = $this->task->request->get->getData();
        }

        if (json_last_error()) {
            $this->postError('request');
        }

        $this->request = new ArrayFilter($data);

        if (!$this->hasError && method_exists($this, $method)) {
            $this->{$method}();

        } else {
            $this->execute();
        }

        if ($this->hasError) {
            $this->setResult(json_encode($this->errors), 'application/json', $this->responseCode);
        }
    }

    public function execute()
    {
        $this->setResult('', null, ResponseCode::CODE_405);
    }

    public function postSuccess($data = null)
    {
        $this->setResult(json_encode($data), 'application/json', ResponseCode::CODE_200);
        $this->hasError = false;
    }

    public function postError($code = null, $message = null, $responseCode = '400 Bad Request')
    {
        if ($code) {
            $this->errors[$code] = $message;
        }

        $this->responseCode = $responseCode;
        $this->hasError = true;
    }
}
