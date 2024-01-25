<?php

namespace ExampleClient;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class Client
{
    private string $baseUrl = 'http://example.com';
    private const POST = 'POST';
    private const PUT = 'PUT';
    private const GET = 'GET';

    /**
     * @throws Exception
     */
    public function getComments(): array
    {
        $url = $this->baseUrl . '/comments';

        try {
            $response = $this->sendRequest(Client::GET, $url);
        } catch (Exception $e) {
            throw new Exception("getComments Error: " . $e->getMessage(), 0, $e);
        }

        $comments = @json_decode($response, true);

        $commentsArrayCollection = new ArrayCollection();

        if(isset($comments['comments'])) {

            foreach($comments as $comment) {
                $newComment = $this->newComment($comment);
                $commentsArrayCollection->add($newComment);
            }
        } else {
            throw new Exception('Ошибка при получении комментариев');
        }

        return $commentsArrayCollection->toArray();
    }

    /**
     * @throws Exception
     */
    public function createComment(string $name, string $text): Comment
    {
        $url = $this->baseUrl . '/comment';
        try {
            $response = $this->sendRequest(Client::POST, $url, compact('name', 'text'));
        } catch (Exception $e) {
            throw new Exception("createComment Error: " . $e->getMessage(), 0, $e);
        }

        try {
            return $this->uploadComment($response);
        } catch (Exception $e) {
            throw new Exception("uploadComment Error: " . $e->getMessage(), 0, $e);

        }
    }

    /**
     * @throws Exception
     */
    public function updateComment(int $id, string $name, string $text): Comment
    {
        $url = $this->baseUrl . '/comment/' . $id;

        try {
            $response = $this->sendRequest(Client::PUT, $url, compact('name', 'text'));
        } catch (Exception $e) {
            throw new Exception("updateComment Error: " . $e->getMessage(), 0, $e);
        }

        try {
            return $this->uploadComment($response);
        } catch (Exception $e) {
            throw new Exception("uploadComment Error: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    private function uploadComment(string $response): Comment
    {
        $data = @json_decode($response, true);
        if(isset($data['comment'])) {
            $this->newComment($data['comment']);
        }

        throw new Exception('Ошибка при загрузке комментария');
    }

    /**
     * @throws Exception
     */
    private function newComment(array $commentData): Comment
    {
        if (isset($commentData['id']) && isset($commentData['name']) && isset($commentData['text'])) {
            return new Comment((int) $commentData['id'],$commentData['name'], $commentData['text']);
        }
        throw new Exception('Ошибка при создании комментария');


    }

    /**
     * @throws Exception
     */
    private function sendRequest(string $method, string $url, array $data = []): string
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle,CURLOPT_URL, $url);
        curl_setopt($curlHandle,CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curlHandle,CURLOPT_RETURNTRANSFER, true);

        if($method === Client::POST || $method === Client::PUT) {
            curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if(curl_errno($curlHandle)){
            throw new Exception(curl_error($curlHandle));
        }

        $result = curl_exec($curlHandle);
        curl_close($curlHandle);

        return $result;
    }
}