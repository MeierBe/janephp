<?php

namespace PicturePark\API\Endpoint;

class UserRoleDelete extends \PicturePark\API\Runtime\Client\BaseEndpoint implements \PicturePark\API\Runtime\Client\Endpoint
{
    protected $id;
    /**
     * Deletes the user role specified by the ID.
     *
     * @param string $id User role ID.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    use \PicturePark\API\Runtime\Client\EndpointTrait;
    public function getMethod() : string
    {
        return 'DELETE';
    }
    public function getUri() : string
    {
        return str_replace(array('{id}'), array($this->id), '/v1/UserRoles/{id}');
    }
    public function getBody(\Symfony\Component\Serializer\SerializerInterface $serializer, $streamFactory = null) : array
    {
        return array(array(), null);
    }
    public function getExtraHeaders() : array
    {
        return array('Accept' => array('application/json'));
    }
    /**
     * {@inheritdoc}
     *
     * @throws \PicturePark\API\Exception\UserRoleDeleteBadRequestException
     * @throws \PicturePark\API\Exception\UserRoleDeleteUnauthorizedException
     * @throws \PicturePark\API\Exception\UserRoleDeleteNotFoundException
     * @throws \PicturePark\API\Exception\UserRoleDeleteMethodNotAllowedException
     * @throws \PicturePark\API\Exception\UserRoleDeleteConflictException
     * @throws \PicturePark\API\Exception\UserRoleDeleteTooManyRequestsException
     * @throws \PicturePark\API\Exception\UserRoleDeleteInternalServerErrorException
     *
     * @return null
     */
    protected function transformResponseBody(string $body, int $status, \Symfony\Component\Serializer\SerializerInterface $serializer, ?string $contentType = null)
    {
        if (200 === $status) {
            return null;
        }
        if (400 === $status && mb_strpos($contentType, 'application/json') !== false) {
            throw new \PicturePark\API\Exception\UserRoleDeleteBadRequestException($serializer->deserialize($body, 'PicturePark\\API\\Model\\PictureparkValidationException', 'json'));
        }
        if (401 === $status) {
            throw new \PicturePark\API\Exception\UserRoleDeleteUnauthorizedException();
        }
        if (404 === $status && mb_strpos($contentType, 'application/json') !== false) {
            throw new \PicturePark\API\Exception\UserRoleDeleteNotFoundException($serializer->deserialize($body, 'PicturePark\\API\\Model\\PictureparkNotFoundException', 'json'));
        }
        if (405 === $status) {
            throw new \PicturePark\API\Exception\UserRoleDeleteMethodNotAllowedException();
        }
        if (409 === $status && mb_strpos($contentType, 'application/json') !== false) {
            throw new \PicturePark\API\Exception\UserRoleDeleteConflictException($serializer->deserialize($body, 'PicturePark\\API\\Model\\PictureparkConflictException', 'json'));
        }
        if (429 === $status) {
            throw new \PicturePark\API\Exception\UserRoleDeleteTooManyRequestsException();
        }
        if (500 === $status && mb_strpos($contentType, 'application/json') !== false) {
            throw new \PicturePark\API\Exception\UserRoleDeleteInternalServerErrorException($serializer->deserialize($body, 'PicturePark\\API\\Model\\PictureparkException', 'json'));
        }
    }
    public function getAuthenticationScopes() : array
    {
        return array('Bearer');
    }
}