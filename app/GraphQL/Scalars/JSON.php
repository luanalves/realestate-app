<?php

/**
 * @author      Luan Silva
 * @copyright   2025 The Dev Kitchen (https://www.thedevkitchen.com.br)
 * @license     https://www.thedevkitchen.com.br  Copyright
 */

declare(strict_types=1);

namespace App\GraphQL\Scalars;

use GraphQL\Error\Error;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Utils\Utils;

class JSON extends ScalarType
{
    public string $name = 'JSON';

    public ?string $description = 'JSON scalar type that represents JSON values.';

    /**
     * Serializes an internal value to include in a response.
     */
    public function serialize($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            // Se já é uma string, assume que é JSON válido e retorna como está
            return $value;
        }

        if (is_array($value) || is_object($value)) {
            // Se é array ou object, converte para JSON string
            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Error('Cannot encode value as JSON: '.json_last_error_msg());
            }

            return $encoded;
        }

        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input.
     *
     * @throws Error
     */
    public function parseValue($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Error('Cannot parse JSON: '.json_last_error_msg());
            }

            return $decoded;
        }

        if (is_array($value) || is_object($value) || is_null($value) || is_bool($value) || is_numeric($value)) {
            return $value;
        }

        throw new Error('Cannot parse the given value as JSON: '.Utils::printSafe($value));
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * @param ValueNode $valueNode
     *
     * @throws Error
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null)
    {
        if ($valueNode instanceof StringValueNode) {
            return $this->parseValue($valueNode->value);
        }

        throw new Error('Can only parse strings to JSON but got: '.$valueNode->kind, [$valueNode]);
    }
}
