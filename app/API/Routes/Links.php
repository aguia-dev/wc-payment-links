<?php

namespace WCPaymentLink\API\Routes;

use DateTime;
use WCPaymentLink\Model\LinkModel;
use WCPaymentLink\Repository\LinkRepository;

final class Links extends Route
{
    public function __construct()
    {
        $this->setNamespace();
        $this->registerRoute(
            'links(/(?P<id>\d+))?',
            [$this, 'handler'],
            ['GET', 'POST', 'PUT', 'DELETE']
        );
    }

    public function handler(object $data)
    {
        $this->validateAuthentication($data->get_headers());

        switch ($data->get_method()) {
            case 'GET':
                $this->getLinks($data->get_params());
                break;
            case 'POST':
                $this->insertLink($data->get_params());
                break;
            case 'PUT':
                $this->updateLink($data->get_params());
                break;
            case 'DELETE':
                $this->removeLink($data->get_params());
                break;
        }
    }

    private function getLinks(array $params): void
    {
        $id      = isset($params['id']) ? $params['id'] : false;
        $order   = isset($params['order']) ? $params['order'] : 'ASC';
        $limit   = isset($params['per_page']) ? $params['per_page'] : 10;
        $page    = isset($params['page']) ? $params['page'] : 1;
        $orderBy = isset($params['order_by']) ? $params['order_by'] : '';

        try {
            $linkRepository = new LinkRepository();

            if ($id) {
                $result = $linkRepository->findById($id);

                if ($result) {
                    $this->sendJsonResponse('', true, 200, $result->getData());
                } else {
                    $this->sendJsonResponse(
                        __('No link founded!', 'wc-payment-links'),
                        false,
                        400,
                        ["id" => $id]
                    );
                }
            } else {
                $result = $linkRepository->findAll($orderBy, $limit, $page, $order, true);

                if ($result) {
                    $this->sendJsonResponse('', true, 200, $this->formatLinks($result));
                }
            }
        } catch (\Exception $e) {
            $this->sendJsonResponse($e->getMessage(), false, 422, []);
        }
    }

    private function updateLink(array $params): void
    {
        $this->saveLink($params, 'update');
    }

    private function insertLink(array $params): void
    {
        $this->saveLink($params, 'insert');
    }

    public function saveLink(array $params, string $type): void
    {
        $this->validateLinkFields($params, $type);

        try {
            $link = new LinkModel(
                $params['name'],
                '',
                new DateTime($params['expire_at']),
                $params['coupon']
            );

            if (isset($params['id'])) {
                $link->setId($params['id']);
            }

            $link->setToken($params['token']);


            $repository = new LinkRepository();
            $result = $repository->save($link);

            if ($result) {
                $link->setId($result);
                $link->saveProducts($params['products']);

                $message = __('Link succefuly saved!', 'wc-payment-links');
                $this->sendJsonResponse($message, true, 200, $link->getData());
            }

        } catch (\Exception $e) {
            $this->sendJsonResponse($e->getMessage(), false, 422, []);
        }
    }

    private function removeLink(array $params): void
    {
        if (!isset($params['id'])) {
            $this->sendUnprocessableEntity(
                __('Missing some required fields.', 'wc-payment-links'),
                ['missing' => ['id']]
            );
        }

        try {
            $repository = new LinkRepository();
            $link = $repository->findById($params['id']);

            $result = $repository->remove($link);

            if ($result) {
                $this->sendJsonResponse(
                    __('Link succefuly deleted!', 'wc-payment-links'),
                    true,
                    200,
                    $link->getData()
                );
            } else {

            }
        } catch (\Exception $e) {
            $this->sendJsonResponse(
                $e->getMessage(),
                false,
                422,
                []
            );
        }

    }

    private function formatLinks(array $links): array
    {
        $return = [];

        if (isset($links['pagination'])) {
            $return['pagination'] = $links['pagination'];
        }

        if (!isset($links['rows'])) {
            return [];
        }

        foreach($links['rows'] as $rows) {
            if ($rows instanceof LinkModel) {
                $return['links'][] = $rows->getData();
            }
        }

        return $return;
    }

    private function validateLinkFields(array $params, string $type): void
    {
        $needed = [
            'name' => 'string',
            'token' => 'string',
            'expire_at' => 'string',
            'products' => 'array'
        ];

        if ($type === 'update') {
            $params['id'] = isset($params['id']) && intval($params['id']) ? (int) $params['id'] : false;
            $needed['id'] = 'integer';
        }

        $missing = [];
        $type_error = [];

        foreach($needed as $key => $item) {
            if (!isset($params[$key])) {
                $missing[] = $key;

            } else if (gettype($params[$key]) !== $item) {
                $type_error[$key] = $item;
            }
        }

        if (!empty($missing)) {
            $this->sendUnprocessableEntity(
                __('Missing some required fields.', 'wc-payment-links'),
                ['missing' => $missing]
            );

        } else if (!empty($type_error)) {
            $this->sendUnprocessableEntity(
                __('Some fields do not have the expected types.', 'wc-payment-links'),
                ['fields' => $type_error]
            );
        }
    }

    private function sendUnprocessableEntity(string $message, array $fields): void
    {
        $this->sendJsonResponse(
            $message,
            false,
            422,
            $fields
        );
    }
}
