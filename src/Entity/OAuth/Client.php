<?php

namespace AppBundle\Entity\OAuth;

use AppBundle\Entity\EntityIdentityTrait;
use AppBundle\Entity\EntitySoftDeletableTrait;
use AppBundle\Entity\EntityTimestampableTrait;
use AppBundle\OAuth\Model\GrantTypeEnum;
use AppBundle\OAuth\Model\Scope;
use AppBundle\OAuth\SecretGenerator;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OAuth\ClientRepository")
 * @ORM\Table(name="oauth_clients", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="oauth_clients_uuid_unique", columns="uuid")
 * })
 */
class Client
{
    use EntityIdentityTrait;
    use EntitySoftDeletableTrait;
    use EntityTimestampableTrait;

    /**
     * @Assert\Length(max = 32, maxMessage = "client.name.constraint.length.max")
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @Assert\Length(
     *     min = 10,
     *     max = 200,
     *     minMessage = "client.description.constraint.length.min",
     *     maxMessage = "client.description.constraint.length.max"
     * )
     *
     * @ORM\Column
     */
    private $description;

    /**
     * @Assert\Count(min = 1, minMessage = "client.redirectUris.constraint.count.min")
     *
     * @ORM\Column(type="json")
     */
    private $redirectUris;

    /**
     * @ORM\Column
     */
    private $secret;

    /**
     * @ORM\Column(type="simple_array")
     *
     * @Assert\NotBlank()
     */
    private $allowedGrantTypes;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $supportedScopes;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
     */
    private $askUserForAuthorization;

    public function __construct(
        UuidInterface $uuid = null,
        string $name = '',
        string $description = '',
        string $secret = '',
        array $allowedGrantTypes = [],
        array $redirectUris = []
    ) {
        $this->uuid = $uuid ?: Uuid::uuid4();
        $this->name = $name;
        $this->description = $description;
        $this->secret = $secret ?: SecretGenerator::generate();
        $this->setAllowedGrantTypes($allowedGrantTypes);
        $this->redirectUris = $redirectUris;
        $this->supportedScopes = [];
        $this->askUserForAuthorization = true;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function addRedirectUri(string $redirectUri): void
    {
        if (!in_array($redirectUri, $this->redirectUris, true)) {
            $this->redirectUris[] = $redirectUri;
        }
    }

    public function removeRedirectUri(string $redirectUri): void
    {
        if (false !== ($key = array_search($redirectUri, $this->redirectUris, true))) {
            unset($this->redirectUris[$key]);
        }
    }

    public function getRedirectUris(): array
    {
        return array_values($this->redirectUris);
    }

    public function hasRedirectUri(string $uri): bool
    {
        return in_array($uri, $this->redirectUris, true);
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setAllowedGrantTypes(array $allowedGrantTypes): void
    {
        foreach ($allowedGrantTypes as $grantType) {
            if (!GrantTypeEnum::isValid($grantType)) {
                throw new \DomainException(sprintf(
                    '"%s" is not a valid grant type. Use constants defined in %s.', $grantType, GrantTypeEnum::class
                ));
            }
        }

        $this->allowedGrantTypes = $allowedGrantTypes;
    }

    public function getAllowedGrantTypes(): array
    {
        return $this->allowedGrantTypes;
    }

    public function isAllowedGrantType(string $grantType): bool
    {
        return in_array($grantType, $this->allowedGrantTypes, true);
    }

    public function addSupportedScope(string $scope): void
    {
        if (in_array($scope, $this->supportedScopes, true)) {
            throw new \LogicException("$scope is already supported");
        }

        if (!Scope::isValid($scope)) {
            throw new \DomainException("$scope is not supported. Choose one from ".Scope::class);
        }

        $this->supportedScopes[] = $scope;
    }

    public function setSupportedScopes(array $supportedScopes): void
    {
        $this->supportedScopes = [];

        foreach ($supportedScopes as $scope) {
            $this->addSupportedScope($scope);
        }
    }

    public function supportsScope(string $scope): bool
    {
        return in_array($scope, $this->supportedScopes, true);
    }

    public function getSupportedScopes(): array
    {
        return $this->supportedScopes;
    }

    public function verifySecret(string $secret): bool
    {
        return $this->secret === $secret;
    }

    public function isAskUserForAuthorization(): bool
    {
        return $this->askUserForAuthorization;
    }

    public function setAskUserForAuthorization(bool $askUserForAuthorization): void
    {
        $this->askUserForAuthorization = $askUserForAuthorization;
    }
}
