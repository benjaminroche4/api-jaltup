<?php

namespace App\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The OpenApiFactoryDecorator class.
 */
#[AsDecorator(
    decorates: 'api_platform.openapi.factory',
    priority: -25,
    onInvalid: ContainerInterface::IGNORE_ON_INVALID_REFERENCE,
)]
class OpenApiFactoryDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private readonly OpenApiFactoryInterface $decorated,
        private readonly string $loginCheck,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $securitySchemes = $openApi->getComponents()->getSecuritySchemes() ?: new \ArrayObject();
        $securitySchemes['access_token'] = new SecurityScheme(
            type: 'http',
            scheme: 'bearer',
        );

        $openApi = ($this->decorated)($context);
        $authPath = $openApi->getPaths()->getPath($this->loginCheck);

        if ($authPath instanceof PathItem && $authPath->getPost() instanceof Operation) {
            $post = $authPath->getPost()->withTags(['Auth']);

            $openApi->getPaths()->addPath(
                $this->loginCheck,
                (new PathItem())->withPost($post),
            );
        }

        return $openApi;
    }
}
