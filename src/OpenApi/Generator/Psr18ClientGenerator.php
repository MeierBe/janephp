<?php

namespace Jane\OpenApi\Generator;

use Http\Discovery\Psr17FactoryDiscovery;
use Jane\JsonSchema\Generator\Context\Context;
use Jane\JsonSchema\Schema as BaseSchema;
use Jane\OpenApi\Schema;
use Jane\OpenApiRuntime\Client\Authentication;
use Jane\OpenApiRuntime\Client\Psr18Client;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class Psr18ClientGenerator extends ClientGenerator
{
    protected function getSuffix(): string
    {
        return '';
    }

    protected function createResourceClass(string $name): Stmt\Class_
    {
        return new Stmt\Class_($name, [
            'extends' => new Node\Name\FullyQualified(Psr18Client::class),
        ]);
    }

    protected function getFactoryMethod(BaseSchema $schema, Context $context): Stmt
    {
        $params = [new Node\Param(new Expr\Variable('httpClient'), new Expr\ConstFetch(new Name('null')))];
        if ($schema instanceof Schema && \count($schema->getSecuritySchemes()) > 0) {
            $params[] = new Node\Param(new Expr\Variable('authentication'), new Expr\ConstFetch(new Name('null')), new Name\FullyQualified(Authentication::class));
        }

        return new Stmt\ClassMethod(
            'create', [
                'flags' => Stmt\Class_::MODIFIER_STATIC | Stmt\Class_::MODIFIER_PUBLIC,
                'params' => $params,
                'stmts' => [
                    new Stmt\If_(
                        new Expr\BinaryOp\Identical(new Expr\ConstFetch(new Name('null')), new Expr\Variable('httpClient')),
                        [
                            'stmts' => $this->getHttpClientCreateExpr($context),
                        ]
                    ),
                    new Stmt\Expression(new Expr\Assign(
                        new Expr\Variable('requestFactory'),
                        new Expr\StaticCall(
                            new Name\FullyQualified(Psr17FactoryDiscovery::class),
                            'findRequestFactory'
                        )
                    )),
                    new Stmt\Expression(new Expr\Assign(
                        new Expr\Variable('streamFactory'),
                        new Expr\StaticCall(
                            new Name\FullyQualified(Psr17FactoryDiscovery::class),
                            'findStreamFactory'
                        )
                    )),
                    new Stmt\Expression(new Expr\Assign(
                        new Expr\Variable('serializer'),
                        new Expr\New_(
                            new Name\FullyQualified(Serializer::class),
                            [
                                new Node\Arg(new Expr\Array_([
                                    new Expr\ArrayItem(new Expr\New_(new Name('\\Symfony\\Component\\Serializer\\Normalizer\\ArrayDenormalizer'))),
                                    new Expr\ArrayItem(new Expr\New_(new Name('\\' . $context->getCurrentSchema()->getNamespace() . '\\Normalizer\\JaneObjectNormalizer'))),
                                ])),
                                new Node\Arg(
                                    new Expr\Array_([
                                        new Expr\ArrayItem(
                                            new Expr\New_(new Name\FullyQualified(JsonEncoder::class), [
                                                new Node\Arg(new Expr\New_(new Name\FullyQualified(JsonEncode::class))),
                                                new Node\Arg(new Expr\New_(new Name\FullyQualified(JsonDecode::class))),
                                            ])
                                        ),
                                    ])
                                ),
                            ]
                        )
                    )),
                    new Stmt\Return_(
                        new Expr\New_(
                            new Name('static'), [
                                new Node\Arg(new Expr\Variable('httpClient')),
                                new Node\Arg(new Expr\Variable('requestFactory')),
                                new Node\Arg(new Expr\Variable('serializer')),
                                new Node\Arg(new Expr\Variable('streamFactory')),
                            ]
                        )
                    ),
                ],
            ]
        );
    }
}
