<?php
namespace Pms\BaseBundle\Component\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    protected $response = array(
        'err' => null,
        'msg' => array(),
        'val' => array()
    );

    public function __construct($data = null, $status = 200, $headers = array())
    {
        parent::__construct('', $status, $headers);
    }

    public function success(array $results = array())
    {
        $this->response['err'] = false;
        $this->response['val'] = $results;

        $this->setData($this->response);

        return $this;
    }

    public function failure(array $messages = array())
    {
        $this->response['err'] = true;
        $this->response['msg'] = $messages;

        $this->setData($this->response);

        return $this;
    }

    public function error(array $messages = array())
    {
        return $this->failure($messages)->setStatusCode(500);
    }
}
